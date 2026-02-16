<?php

namespace App\Database\Turso;

use Illuminate\Database\Connectors\Connector;
use Illuminate\Database\Connectors\ConnectorInterface;

class TursoConnector extends Connector implements ConnectorInterface
{
    public function connect(array $config)
    {
        return new TursoPdo($config);
    }
}
