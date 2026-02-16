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
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $db = $this->app->make('db');

        $driverExtension = function ($config, $name) {
            $connector = new TursoConnector();
            $pdo = $connector->connect($config);
            
            return new SQLiteConnection(
                $pdo, 
                $config['database'] ?? 'main', 
                $config['prefix'] ?? '', 
                $config
            );
        };

        $db->extend('turso', $driverExtension);
        $db->extend('libsql', $driverExtension);
    }
}
