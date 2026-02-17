<?php

namespace Turso\Http\Laravel\Database;

use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Query\Processors\SQLiteProcessor;

class LibSQLQueryProcessor extends SQLiteProcessor
{
    /**
     * Process the list of tables.
     *
     * @param  mixed  $results
     */
    public function processTables($results): array
    {
        return $results;
    }

    public function processSelect(Builder $query, $results)
    {
        return $results;
    }
}
