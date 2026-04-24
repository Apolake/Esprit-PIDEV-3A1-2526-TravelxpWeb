<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class TranslationService
{
    private const FALLBACK_ENDPOINTS = [
        'https://api.mymemory.translated.net/get',
        'https://translate.argosopentech.com/translate',
        'https://libretranslate.com/translate',
        'https://libretranslate.de/translate',
    ];

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly string $translateApiUrl = 'https://translate.googleapis.com/translate_a/single?client=gtx&dt=t&sl=auto',
    ) {
    }

    /**
     * @return array{translatedText:string,provider:string,error:string|null}
     */
    public function translate(string $text, string $targetLang, string $sourceLang = 'auto'): array
    {
        $content = trim($text);
        if ($content === '') {
            return [
                'translatedText' => '',
                'provider' => 'none',
                'error' => null,
            ];
        }

        $lastError = null;
        foreach ($this->getCandidateEndpoints() as $endpoint) {
            try {
                $translated = $this->translateViaEndpoint($endpoint, $content, $targetLang, $sourceLang);
                if ($translated === '') {
                    throw new \RuntimeException('Translation API returned an empty response.');
                }

                return [
                    'translatedText' => $translated,
                    'provider' => $this->resolveProviderName($endpoint),
                    'error' => null,
                ];
            } catch (TransportExceptionInterface | \Throwable $exception) {
                $lastError = $exception->getMessage();
            }
        }

        return [
            'translatedText' => $content,
            'provider' => 'fallback',
            'error' => $lastError ?: 'Translation service is unavailable right now.',
        ];
    }

    /**
     * @return list<string>
     */
    private function getCandidateEndpoints(): array
    {
        return array_values(array_unique(array_filter([
            $this->translateApiUrl,
            ...self::FALLBACK_ENDPOINTS,
        ])));
    }

    private function translateViaEndpoint(string $endpoint, string $content, string $targetLang, string $sourceLang): string
    {
        if (str_contains($endpoint, 'translate.googleapis.com/translate_a/single')) {
            $response = $this->httpClient->request('GET', $endpoint . '&tl=' . rawurlencode(strtolower($targetLang)) . '&q=' . rawurlencode($content), [
                'timeout' => 4,
            ]);

            $data = $response->toArray(false);
            $segments = $data[0] ?? [];
            $parts = [];

            foreach ($segments as $segment) {
                if (isset($segment[0])) {
                    $parts[] = (string) $segment[0];
                }
            }

            return trim(implode('', $parts));
        }

        if (str_contains($endpoint, 'api.mymemory.translated.net/get')) {
            $source = $sourceLang !== 'auto' ? strtolower($sourceLang) : 'en';
            $response = $this->httpClient->request('GET', $endpoint . '?q=' . rawurlencode($content) . '&langpair=' . rawurlencode($source . '|' . strtolower($targetLang)), [
                'timeout' => 4,
            ]);

            $data = $response->toArray(false);
            return (string) ($data['responseData']['translatedText'] ?? '');
        }

        $response = $this->httpClient->request('POST', $endpoint, [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'q' => $content,
                'source' => $sourceLang,
                'target' => strtolower($targetLang),
                'format' => 'text',
            ],
            'timeout' => 4,
        ]);

        $statusCode = $response->getStatusCode();
        if ($statusCode < 200 || $statusCode >= 300) {
            throw new \RuntimeException('Translation API returned HTTP ' . $statusCode . '.');
        }

        $data = $response->toArray(false);

        return (string) ($data['translatedText'] ?? '');
    }

    private function resolveProviderName(string $endpoint): string
    {
        $host = (string) parse_url($endpoint, PHP_URL_HOST);

        return match ($host) {
            'translate.googleapis.com' => 'google-translate',
            'api.mymemory.translated.net' => 'mymemory',
            'translate.argosopentech.com' => 'argosopentech',
            'libretranslate.com', 'libretranslate.de' => 'libretranslate',
            default => $host !== '' ? $host : 'translation-api',
        };
    }
}
