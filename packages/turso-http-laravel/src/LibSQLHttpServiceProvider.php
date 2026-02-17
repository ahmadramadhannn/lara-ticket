<?php

namespace Turso\Http\Laravel;

use Illuminate\Database\DatabaseManager;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Turso\Http\Laravel\Database\LibSQLConnection;
use Turso\Http\Laravel\Database\LibSQLConnectionFactory;
use Turso\Http\Laravel\Database\LibSQLConnector;

class LibSQLHttpServiceProvider extends PackageServiceProvider
{
    public function boot(): void
    {
        parent::boot();
        if (config('database.connections.' . config('database.default') . '.driver') !== 'libsql') {
            return;
        }
    }

    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('turso-http-laravel');
    }

    public function register(): void
    {
        parent::register();
        $this->app->singleton('db.factory', function ($app) {
            return new LibSQLConnectionFactory($app);
        });

        $this->app->scoped(LibSQLManager::class, function () {
            $connectionName = config('database.default', 'libsql');
            return new LibSQLManager(config("database.connections.{$connectionName}"));
        });

        $this->app->resolving('db', function (DatabaseManager $db) {
            $db->extend('libsql', function ($config, $name) {
                $config = config("database.connections.{$name}");
                $config['name'] = $name;
                if (! isset($config['driver'])) {
                    $config['driver'] = 'libsql';
                }

                $connector = new LibSQLConnector;
                $db = $connector->connect($config);

                $connection = new LibSQLConnection($db, $config['database'] ?? ':memory:', $config['prefix'], $config);
                app()->instance(LibSQLConnection::class, $connection);

                $connection->createReadPdo($config);

                return $connection;
            });
        });
    }
}
