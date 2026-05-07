<?php

namespace App\Tests\Service;

use App\Service\ProfanityFilterService;
use PHPUnit\Framework\TestCase;

class ProfanityFilterServiceTest extends TestCase
{
    public function testSanitizeMasksBlockedWords(): void
    {
        $s = new ProfanityFilterService();

        $input = 'This is shit and crap and a bitch.';
        $out = $s->sanitize($input);

        $this->assertStringNotContainsString('shit', strtolower($out));
        $this->assertStringNotContainsString('crap', strtolower($out));
        $this->assertStringNotContainsString('bitch', strtolower($out));
        $this->assertMatchesRegularExpression('/\*+/', $out);
    }

    public function testSanitizeReturnsEmptyStringUnchanged(): void
    {
        $s = new ProfanityFilterService();
        $this->assertSame('', $s->sanitize(''));
    }
}
