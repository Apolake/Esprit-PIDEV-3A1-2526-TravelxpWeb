<?php

namespace App\Tests\Dev;

use App\Dev\DoctrinePerformanceProfiler;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class DoctrinePerformanceProfilerTest extends TestCase
{
    public function testProfileEntityPersistsRecordAndSummary(): void
    {
        $storagePath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'doctrine_performance_profiler_test_' . uniqid('', true) . '.json';
        $profiler = new DoctrinePerformanceProfiler(new NullLogger(), $storagePath);

        $result = $profiler->profileEntity('Blog', 7, 'index_listing', static fn (): string => 'ok');

        self::assertSame('ok', $result);
        self::assertCount(1, $profiler->getRecords());
        self::assertFileExists($storagePath);

        $freshProfiler = new DoctrinePerformanceProfiler(new NullLogger(), $storagePath);
        $records = $freshProfiler->getPersistedRecords();
        $summary = $freshProfiler->getSummary();

        self::assertCount(1, $records);
        self::assertSame('Blog', $records[0]['entity']);
        self::assertSame(7, $records[0]['entityId']);
        self::assertSame('index_listing', $records[0]['operation']);
        self::assertSame('Blog', $summary[0]['entity']);
        self::assertSame('index_listing', $summary[0]['operation']);
        self::assertSame(1, $summary[0]['count']);

        @unlink($storagePath);
    }
}