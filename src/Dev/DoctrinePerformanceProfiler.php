<?php

namespace App\Dev;

use Psr\Log\LoggerInterface;

class DoctrinePerformanceProfiler
{
    /**
     * @var list<array{
     *     entity:string,
     *     entityId:string|int|null,
     *     operation:string,
     *     durationMs:float,
     *     memoryStartBytes:int,
     *     memoryEndBytes:int,
     *     memoryDeltaBytes:int,
     *     peakMemoryBytes:int
     * }>
     */
    private array $records = [];

    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly string $storagePath,
    ) {
    }

    /**
     * @template T
     * @param callable():T $callback
     * @return T
     */
    public function profileEntity(string $entity, string|int|null $entityId, string $operation, callable $callback): mixed
    {
        $startTime = hrtime(true);
        $startMemory = memory_get_usage(true);

        try {
            $result = $callback();
        } finally {
            $endTime = hrtime(true);
            $endMemory = memory_get_usage(true);

            $record = [
                'entity' => $entity,
                'entityId' => $entityId,
                'operation' => $operation,
                'durationMs' => ($endTime - $startTime) / 1_000_000,
                'memoryStartBytes' => $startMemory,
                'memoryEndBytes' => $endMemory,
                'memoryDeltaBytes' => $endMemory - $startMemory,
                'peakMemoryBytes' => memory_get_peak_usage(true),
            ];

            $this->records[] = $record;
            $this->appendRecord($record);
            $this->logger->debug('Doctrine entity profile recorded.', $record);
        }

        return $result;
    }

    /**
     * @return list<array{
     *     entity:string,
     *     entityId:string|int|null,
     *     operation:string,
     *     durationMs:float,
     *     memoryStartBytes:int,
     *     memoryEndBytes:int,
     *     memoryDeltaBytes:int,
     *     peakMemoryBytes:int
     * }>
     */
    public function getRecords(): array
    {
        return $this->records;
    }

    /**
     * @param positive-int|null $limit
     * @return list<array{
     *     entity:string,
     *     entityId:string|int|null,
     *     operation:string,
     *     durationMs:float,
     *     memoryStartBytes:int,
     *     memoryEndBytes:int,
     *     memoryDeltaBytes:int,
     *     peakMemoryBytes:int
     * }>
     */
    public function getPersistedRecords(?int $limit = 100): array
    {
        $records = $this->readStoredRecords();

        if ($limit !== null && $limit > 0 && count($records) > $limit) {
            $records = array_slice($records, -$limit);
        }

        return $records;
    }

    public function getSummary(): array
    {
        $summary = [];

        foreach ($this->getPersistedRecords(null) as $record) {
            $entity = (string) ($record['entity'] ?? 'Unknown');
            $operation = (string) ($record['operation'] ?? 'unknown');
            $key = $entity . '::' . $operation;

            if (!isset($summary[$key])) {
                $summary[$key] = [
                    'entity' => $entity,
                    'operation' => $operation,
                    'count' => 0,
                    'totalDurationMs' => 0.0,
                    'totalMemoryDeltaBytes' => 0,
                    'peakMemoryBytes' => 0,
                ];
            }

            $summary[$key]['count']++;
            $summary[$key]['totalDurationMs'] += (float) ($record['durationMs'] ?? 0.0);
            $summary[$key]['totalMemoryDeltaBytes'] += (int) ($record['memoryDeltaBytes'] ?? 0);
            $summary[$key]['peakMemoryBytes'] = max($summary[$key]['peakMemoryBytes'], (int) ($record['peakMemoryBytes'] ?? 0));
        }

        usort($summary, static fn (array $left, array $right): int => $right['totalDurationMs'] <=> $left['totalDurationMs']);

        return $summary;
    }

    public function clear(): void
    {
        $this->records = [];
        if (is_file($this->storagePath)) {
            @unlink($this->storagePath);
        }
    }

    /**
     * @param array{
     *     entity:string,
     *     entityId:string|int|null,
     *     operation:string,
     *     durationMs:float,
     *     memoryStartBytes:int,
     *     memoryEndBytes:int,
     *     memoryDeltaBytes:int,
     *     peakMemoryBytes:int
     * } $record
     */
    private function appendRecord(array $record): void
    {
        $records = $this->readStoredRecords();
        $records[] = $record;

        if (count($records) > 250) {
            $records = array_slice($records, -250);
        }

        $this->writeStoredRecords($records);
    }

    /**
     * @return list<array{
     *     entity:string,
     *     entityId:string|int|null,
     *     operation:string,
     *     durationMs:float,
     *     memoryStartBytes:int,
     *     memoryEndBytes:int,
     *     memoryDeltaBytes:int,
     *     peakMemoryBytes:int
     * }>
     */
    private function readStoredRecords(): array
    {
        if (!is_file($this->storagePath)) {
            return [];
        }

        $content = file_get_contents($this->storagePath);
        if ($content === false || trim($content) === '') {
            return [];
        }

        $decoded = json_decode($content, true);
        if (!is_array($decoded)) {
            return [];
        }

        return array_values(array_filter($decoded, static fn ($record): bool => is_array($record)));
    }

    /**
     * @param list<array{
     *     entity:string,
     *     entityId:string|int|null,
     *     operation:string,
     *     durationMs:float,
     *     memoryStartBytes:int,
     *     memoryEndBytes:int,
     *     memoryDeltaBytes:int,
     *     peakMemoryBytes:int
     * }> $records
     */
    private function writeStoredRecords(array $records): void
    {
        $directory = dirname($this->storagePath);
        if (!is_dir($directory)) {
            @mkdir($directory, 0775, true);
        }

        file_put_contents(
            $this->storagePath,
            json_encode($records, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) ?: '[]',
            LOCK_EX
        );
    }
}