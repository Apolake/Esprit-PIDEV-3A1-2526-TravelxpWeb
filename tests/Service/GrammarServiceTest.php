<?php

namespace App\Tests\Service;

use App\Service\GrammarService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class GrammarServiceTest extends TestCase
{
    public function testCorrectUsesOpenAiRewriteWhenAvailable(): void
    {
        $responses = [
            new MockResponse(json_encode([
                'choices' => [
                    [
                        'message' => [
                            'content' => 'john went to paris. it was amazing and he loved the eiffel tower.',
                        ],
                    ],
                ],
            ], JSON_THROW_ON_ERROR)),
        ];

        $client = new MockHttpClient(static function (string $method, string $url, array $options) use (&$responses): MockResponse {
            self::assertSame('POST', $method);
            self::assertSame('https://api.openai.com/v1/chat/completions', $url);

            return array_shift($responses) ?? new MockResponse('{}');
        });

        $service = new GrammarService($client, 'test-key');
        $result = $service->correct('john went to paris it was amazing and he loved the eiffel tower', 'en-US');

        self::assertTrue($result['changed']);
        self::assertSame('john went to paris. it was amazing and he loved the eiffel tower.', $result['correctedText']);
        self::assertSame('Grammar suggestions applied.', $result['message']);
    }

    public function testCorrectFallsBackToLanguageToolSuggestions(): void
    {
        $responses = [
            new MockResponse(json_encode([
                'matches' => [
                    [
                        'offset' => 0,
                        'length' => 4,
                        'replacements' => [
                            ['value' => 'This'],
                        ],
                    ],
                    [
                        'offset' => 5,
                        'length' => 7,
                        'replacements' => [
                            ['value' => 'spelling'],
                        ],
                    ],
                    [
                        'offset' => 16,
                        'length' => 4,
                        'replacements' => [
                            ['value' => 'wrong'],
                        ],
                    ],
                ],
            ], JSON_THROW_ON_ERROR)),
        ];

        $client = new MockHttpClient(static function (string $method, string $url) use (&$responses): MockResponse {
            self::assertSame('POST', $method);
            self::assertSame('https://api.languagetool.org/v2/check', $url);

            return array_shift($responses) ?? new MockResponse('{}');
        });

        $service = new GrammarService($client, null);
        $result = $service->correct('this speling is wrog', 'en-US');

        self::assertTrue($result['changed']);
        self::assertSame('This spelling is wrong', $result['correctedText']);
        self::assertSame('Grammar suggestions applied.', $result['message']);
    }
}