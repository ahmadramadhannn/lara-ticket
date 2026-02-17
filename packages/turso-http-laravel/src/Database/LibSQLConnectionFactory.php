<?php

namespace Turso\Http\Laravel\Database;

use Illuminate\Database\Connectors\ConnectionFactory;

class LibSQLConnectionFactory extends ConnectionFactory
{
    protected function createConnection($driver, $connection, $database, $prefix = '', array $config = [])
    {
        $config['driver'] = 'libsql';
        $url = $config['url'] ?? $config['database'] ?? ':memory:';
        $config['url'] = (str_starts_with($url, 'libsql://') || str_starts_with($url, 'http'))
            ? $url
            : 'file:' . $config['database'];
        $connection = function () use ($config) {
            return new LibSQLDatabase($config);
        };

        return new LibSQLConnection($connection(), $config['url'], $prefix, $config);
    }

    public function createConnector(array $config)
    {
        $connector = new LibSQLConnector;
        $connector->connect(config('database.connections.libsql'));

        return new LibSQLConnector;
    }
}
