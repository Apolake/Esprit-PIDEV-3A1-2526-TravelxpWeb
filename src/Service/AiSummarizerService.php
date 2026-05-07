<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class AiSummarizerService
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly ?string $openAiApiKey = null,
    ) {
    }

    /**
     * @return array{summary:string,source:string,error:string|null}
     */
    public function summarize(string $text): array
    {
        $content = trim($text);
        if ($content === '') {
            return [
                'summary' => 'No content available to summarize.',
                'source' => 'none',
                'error' => null,
            ];
        }

        if ($this->openAiApiKey !== null && trim($this->openAiApiKey) !== '') {
            try {
                $response = $this->httpClient->request('POST', 'https://api.openai.com/v1/chat/completions', [
                    'headers' => [
                        'Authorization' => 'Bearer ' . trim($this->openAiApiKey),
                        'Content-Type' => 'application/json',
                    ],
                    'json' => [
                        'model' => 'gpt-4o-mini',
                        'messages' => [
                            [
                                'role' => 'system',
                                'content' => 'You summarize travel blog posts in 3 to 6 concise bullet points.',
                            ],
                            [
                                'role' => 'user',
                                'content' => $content,
                            ],
                        ],
                        'temperature' => 0.3,
                    ],
                    'timeout' => 20,
                ]);

                $data = $response->toArray(false);
                $summary = (string) ($data['choices'][0]['message']['content'] ?? '');
                if ($summary !== '') {
                    return [
                        'summary' => trim($summary),
                        'source' => 'openai',
                        'error' => null,
                    ];
                }
            } catch (\Throwable) {
                // Fallback to local summarization.
            }
        }

        return [
            'summary' => $this->fallbackSummary($content),
            'source' => 'fallback',
            'error' => null,
        ];
    }

    private function fallbackSummary(string $content): string
    {
        $normalized = preg_replace('/\s+/', ' ', trim(strip_tags($content))) ?? '';
        if ($normalized === '') {
            return 'No content available to summarize.';
        }

        $sentences = preg_split('/(?<=[.!?])\s+/', $normalized) ?: [];
        $top = array_slice(array_filter(array_map('trim', $sentences)), 0, 4);

        if ($top === []) {
            return mb_substr($normalized, 0, 420) . (mb_strlen($normalized) > 420 ? '...' : '');
        }

        return implode("\n", array_map(static fn (string $line): string => '- ' . $line, $top));
    }
}

//upgraded api endpoint calling logic with fallback and error handling for better reliability and user experience   
