<?php

namespace Turso\Http\Laravel\Database;

use Darkterminal\TursoHttp\core\Http\LibSQLStatement;
use Darkterminal\TursoHttp\core\Http\LibSQLResult;
use Darkterminal\TursoHttp\core\Utils;
use Exception;

class LibSQLStatementDriver extends LibSQLStatement
{
    public function execute(array $parameters)
    {
        $this->parameters = $parameters;
        $response = $this->db->getConnection()->prepareRequest($this->sql, $parameters)->executeRequest()->get();

        if (is_string($response)) {
            throw new Exception($response);
        }

        if (isset($response['type']) && $response['type'] === 'error') {
            throw new Exception($response['error']['message']);
        }

        if (!is_array($response)) {
             throw new Exception("Unexpected response format from Turso");
        }

        // Use reflection or protected access if needed, currently $results is protected in parent
        $this->results = Utils::removeCloseResponses($response['results']);
        
        return $this->results['affected_row_count'] ?? 0;
    }

    public function query(array $parameters = [])
    {
        $this->parameters = $parameters;
        $response = $this->db->getConnection()->prepareRequest($this->sql, $parameters)->executeRequest()->get();

        if (is_string($response)) {
            throw new Exception($response);
        }

        if (isset($response['type']) && $response['type'] === 'error') {
            throw new Exception($response['error']['message']);
        }
        
        if (!is_array($response)) {
             throw new Exception("Unexpected response format from Turso");
        }

        // We need to set results for columns() method to work
        $this->results = Utils::removeCloseResponses($response['results']);
        
        return new LibSQLResult($response);
    }
}
