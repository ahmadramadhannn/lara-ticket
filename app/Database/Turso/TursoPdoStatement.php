<?php

namespace App\Database\Turso;

use PDO;

class TursoPdoStatement extends \PDOStatement
{
    protected $pdo;
    protected $query;
    protected $params = [];
    protected $results = [];
    protected $cursor = 0;

    public function __construct(TursoPdo $pdo, string $query)
    {
        $this->pdo = $pdo;
        $this->query = $query;
    }

    public function execute(?array $params = null): bool
    {
        if ($params) {
            $this->params = $params;
        }

        $data = $this->pdo->queryTurso($this->query, $this->params);
        $result = $data['results'][0]['response']['result'] ?? null;

        $this->results = [];
        if ($result && isset($result['cols']) && isset($result['rows'])) {
            foreach ($result['rows'] as $row) {
                $item = [];
                foreach ($result['cols'] as $i => $col) {
                    $val = $row[$i]['value'] ?? null;
                    if (isset($row[$i]['type'])) {
                        if ($row[$i]['type'] === 'integer') $val = (int)$val;
                        if ($row[$i]['type'] === 'float') $val = (float)$val;
                        if ($row[$i]['type'] === 'null') $val = null;
                    }
                    $item[$col['name']] = $val;
                }
                $this->results[] = (object) $item;
            }
        }
        
        $this->cursor = 0;
        return true;
    }

    #[\ReturnTypeWillChange]
    public function fetch($mode = PDO::FETCH_DEFAULT, $cursor_orientation = PDO::FETCH_ORI_NEXT, $offset = 0): mixed
    {
        if ($this->cursor >= count($this->results)) {
            return false;
        }

        $row = $this->results[$this->cursor++];
        return (array) $row;
    }

    #[\ReturnTypeWillChange]
    public function fetchAll(int $mode = PDO::FETCH_DEFAULT, ...$args): array
    {
        return array_map(function($row) { return (array)$row; }, $this->results);
    }

    public function rowCount(): int
    {
        return count($this->results);
    }
    
    public function bindParam(string|int $param, mixed &$var, int $type = PDO::PARAM_STR, int $maxLength = 0, mixed $driverOptions = null): bool
    {
        $this->params[$param] = &$var;
        return true;
    }

    public function bindValue(string|int $param, mixed $value, int $type = PDO::PARAM_STR): bool
    {
        $this->params[$param] = $value;
        return true;
    }

    #[\ReturnTypeWillChange]
    public function setFetchMode($mode = PDO::FETCH_DEFAULT, $class = null, $constructorArgs = null): bool
    {
        return true;
    }
}
