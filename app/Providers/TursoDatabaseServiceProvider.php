<?php

namespace App\Providers;

use App\Database\Turso\TursoConnector;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\SQLiteConnection;

class TursoDatabaseServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->resolving('db', function ($db) {
            $db->extend('turso', function ($config, $name) {
                $connector = new TursoConnector();
                $pdo = $connector->connect($config);
                
                return new SQLiteConnection(
                    $pdo, 
                    $config['database'] ?? 'main', 
                    $config['prefix'] ?? '', 
                    $config
                );
            });
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
