<?php

namespace App\Database\Turso;

use PDO;
use Illuminate\Support\Facades\Http;
use Illuminate\Database\QueryException;

class TursoPdo extends PDO
{
    protected $url;
    protected $token;
    protected $database;
    protected $attributes = [];

    public function __construct(array $config)
    {
        $this->url = $config['url'] ?? $config['db_url'] ?? '';
        $this->token = $config['token'] ?? $config['access_token'] ?? '';
        
        // Clean URL: libsql:// -> https://
        $this->url = str_replace('libsql://', 'https://', $this->url);
    }

    public function prepare(string $query, array $options = []): \PDOStatement|false
    {
        return new TursoPdoStatement($this, $query);
    }

    public function exec(string $statement): int|false
    {
        $response = $this->queryTurso($statement);
        return count($response['results'][0]['response']['result']['rows'] ?? []);
    }

    #[\ReturnTypeWillChange]
    public function query($query, $fetchMode = null, ...$fetchModeArgs): \PDOStatement|false
    {
        $stmt = $this->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function lastInsertId(?string $name = null): string|false
    {
        // Turso returns affected rows and last_insert_rowid in the response
        return '0'; // Stub for now, can be improved
    }

    public function beginTransaction(): bool { return true; }
    public function commit(): bool { return true; }
    public function rollBack(): bool { return true; }

    public function setAttribute(int $attribute, mixed $value): bool
    {
        $this->attributes[$attribute] = $value;
        return true;
    }

    public function getAttribute(int $attribute): mixed
    {
        return $this->attributes[$attribute] ?? null;
    }

    public function queryTurso(string $sql, array $params = [])
    {
        $payload = [
            'requests' => [
                [
                    'type' => 'execute',
                    'stmt' => [
                        'sql' => $sql,
                        'args' => array_map(function ($value) {
                            if (is_int($value)) return ['type' => 'integer', 'value' => (string)$value];
                            if (is_float($value)) return ['type' => 'float', 'value' => $value];
                            if (is_null($value)) return ['type' => 'null'];
                            return ['type' => 'text', 'value' => (string)$value];
                        }, array_values($params))
                    ]
                ],
                [ 'type' => 'close' ]
            ]
        ];

        $response = Http::withToken($this->token)
            ->post($this->url . '/v2/pipeline', $payload);

        if ($response->failed()) {
            throw new \Exception("Turso Query Failed: " . $response->body());
        }

        $data = $response->json();
        
        if (isset($data['results'][0]['error'])) {
            throw new \Exception("Turso SQL Error: " . ($data['results'][0]['error']['message'] ?? 'Unknown error'));
        }

        return $data;
    }
}
