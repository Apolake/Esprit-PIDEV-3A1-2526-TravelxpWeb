<?php

namespace App\Tests\Service;

use App\Service\ReadTimeEstimatorService;
use PHPUnit\Framework\TestCase;

class ReadTimeEstimatorServiceTest extends TestCase
{
    public function testEstimateReturnsOneForEmptyContent(): void
    {
        $s = new ReadTimeEstimatorService();
        $this->assertSame(1, $s->estimateMinutes(null));
        $this->assertSame(1, $s->estimateMinutes(''));
        $this->assertSame(1, $s->estimateMinutes('   '));
    }

    public function testEstimateCalculatesMinutesByWords(): void
    {
        $s = new ReadTimeEstimatorService();
        $text = str_repeat('word ', 400); // 400 words -> 2 minutes at 200 wpm
        $this->assertSame(2, $s->estimateMinutes($text));

        $text = str_repeat('word ', 1); // 1 word -> 1 minute minimum
        $this->assertSame(1, $s->estimateMinutes($text));
    }
}
