<?php

namespace Turso\Http\Laravel\Database;

use Darkterminal\TursoHttp\LibSQL;
use Darkterminal\TursoHttp\core\Utils;
use Darkterminal\TursoHttp\core\Http\LibSQLResult;
use Exception;

class LibSQLDriver extends LibSQL
{

    public function query(string $sql, array $parameters = []): LibSQLResult
    {
        $response = $this->http->prepareRequest($sql, $parameters)->executeRequest()->get();
        
        if (is_string($response)) {
             throw new Exception($response);
        }

        if (isset($response['type']) && $response['type'] === 'error') {
            throw new Exception($response['error']['message']);
        }
        
        // Handle case where results might not be set or not an array as expected by LibSQLResult
        if (!isset($response['results']) || !is_array($response['results'])) {
             // Debugging or fallback
             throw new Exception("Unexpected response format from Turso: " . json_encode($response));
        }

        // We can't call setChanges because it's private in parent.
        // But we can replicate its logic if needed, or ignore it if affected_rows isn't used critically here.
        // $this->setChanges($response); // This will fail.
        
        // Manually set affected_rows if possible, or skip. 
        // Since affected_rows is protected, we CAN set it!
        $this->affected_rows = 0;
        if (isset($response['results'])) {
             $resultCtx = Utils::removeCloseResponses($response['results']);
             $this->affected_rows = $resultCtx['affected_row_count'] ?? 0;
        }

        return new LibSQLResult($response);
    }

    public function prepare(string $sql): \Darkterminal\TursoHttp\core\Http\LibSQLStatement
    {
        return new LibSQLStatementDriver($this, $sql);
    }
    
    public function execute(string $sql, array $parameters = []): int
    {
        $response = $this->http->prepareRequest($sql, $parameters)->executeRequest()->get();
        
        if (is_string($response)) {
             throw new Exception($response);
        }

        if (isset($response['type']) && $response['type'] === 'error') {
            throw new Exception($response['error']['message']);
        }

        if (!isset($response['results']) || !is_array($response['results'])) {
             // throw new Exception("Unexpected response format from Turso: " . json_encode($response)); 
             // Allow for executions that return no results if needed, but usually they return results structure
        }

        $result = Utils::removeCloseResponses($response['results']);
        
        // Update affected_rows protected property
        $this->affected_rows = $result['affected_row_count'] ?? 0;
        
        return $this->affected_rows;
    }

    public function version(): string
    {
        $response = Utils::makeRequest('GET', "{$this->baseURL}/version", $this->authToken);
        
        if (is_array($response)) {
            // Adjust key based on actual API response, usually just the string or 'version' key
            return $response['version'] ?? json_encode($response);
        }

        return (string) $response;
    }
}
