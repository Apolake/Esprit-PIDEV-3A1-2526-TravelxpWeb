<?php

namespace App\Service;

use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class SchedulerRunStateService
{
    private const CACHE_KEY = 'dashboard.scheduler_runs.v1';

    public const JOB_WAITING_LIST_EXPIRATION = 'waiting_list_expiration';
    public const JOB_WEATHER_WARNING_CHECKS = 'weather_warning_checks';

    public function __construct(
        #[Autowire(service: 'cache.app')]
        private readonly CacheItemPoolInterface $cachePool,
    ) {
    }

    /**
     * @param array<string, mixed> $metrics
     */
    public function markSuccess(string $jobKey, array $metrics, \DateTimeImmutable $startedAt, \DateTimeImmutable $finishedAt): void
    {
        $this->saveJobState($jobKey, [
            'status' => 'success',
            'lastRunAtTs' => $finishedAt->getTimestamp(),
            'durationMs' => max(0, ((int) $finishedAt->format('Uu') - (int) $startedAt->format('Uu')) / 1000),
            'metrics' => $metrics,
            'error' => null,
        ]);
    }

    /**
     * @param array<string, mixed> $metrics
     */
    public function markFailure(string $jobKey, array $metrics, \DateTimeImmutable $startedAt, \DateTimeImmutable $failedAt, string $error): void
    {
        $this->saveJobState($jobKey, [
            'status' => 'failed',
            'lastRunAtTs' => $failedAt->getTimestamp(),
            'durationMs' => max(0, ((int) $failedAt->format('Uu') - (int) $startedAt->format('Uu')) / 1000),
            'metrics' => $metrics,
            'error' => trim($error),
        ]);
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function getDashboardSnapshot(): array
    {
        $state = $this->readState();

        return [
            self::JOB_WAITING_LIST_EXPIRATION => $this->normalizeJobState(
                $state[self::JOB_WAITING_LIST_EXPIRATION] ?? [],
                'Waiting List Expiration',
                'Every 15 minutes'
            ),
            self::JOB_WEATHER_WARNING_CHECKS => $this->normalizeJobState(
                $state[self::JOB_WEATHER_WARNING_CHECKS] ?? [],
                'Weather Warning Checks',
                'Every 2 hours'
            ),
        ];
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function saveJobState(string $jobKey, array $payload): void
    {
        $item = $this->cachePool->getItem(self::CACHE_KEY);
        $state = $item->isHit() ? (array) $item->get() : [];
        $state[$jobKey] = $payload;
        $item->set($state);
        $this->cachePool->save($item);
    }

    /**
     * @return array<string, mixed>
     */
    private function readState(): array
    {
        $item = $this->cachePool->getItem(self::CACHE_KEY);

        return $item->isHit() ? (array) $item->get() : [];
    }

    /**
     * @param array<string, mixed> $state
     * @return array<string, mixed>
     */
    private function normalizeJobState(array $state, string $title, string $interval): array
    {
        $status = in_array(($state['status'] ?? ''), ['success', 'failed'], true) ? (string) $state['status'] : 'unknown';
        $timestamp = isset($state['lastRunAtTs']) ? (int) $state['lastRunAtTs'] : 0;

        return [
            'title' => $title,
            'interval' => $interval,
            'status' => $status,
            'lastRunAt' => $timestamp > 0 ? (new \DateTimeImmutable())->setTimestamp($timestamp) : null,
            'durationMs' => isset($state['durationMs']) ? max(0, (int) $state['durationMs']) : null,
            'metrics' => is_array($state['metrics'] ?? null) ? $state['metrics'] : [],
            'error' => isset($state['error']) && trim((string) $state['error']) !== '' ? trim((string) $state['error']) : null,
        ];
    }
}

