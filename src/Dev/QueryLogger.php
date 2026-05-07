<?php

namespace App\Dev;

use Doctrine\DBAL\Logging\SQLLogger;

class QueryLogger implements SQLLogger
{
    private array $queries = [];

    public function startQuery(string $sql, ?array $params = null, ?array $types = null): void
    {
        $this->queries[] = [
            'sql' => $sql,
            'params' => $params,
            'types' => $types,
            'start' => microtime(true),
            'end' => null,
            'time' => null,
        ];
    }

    public function stopQuery(): void
    {
        $last = array_key_last($this->queries);
        if ($last !== null) {
            $this->queries[$last]['end'] = microtime(true);
            $this->queries[$last]['time'] = $this->queries[$last]['end'] - $this->queries[$last]['start'];
        }
    }

    /** @return array<int, array{sql:string,params:array|null,types:array|null,start:float,end:float|null,time:float|null}> */
    public function getQueries(): array
    {
        return $this->queries;
    }

    public function clear(): void
    {
        $this->queries = [];
    }
}
