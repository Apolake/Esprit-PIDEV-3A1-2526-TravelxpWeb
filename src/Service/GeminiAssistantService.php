<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class GeminiAssistantService
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly string $apiKey = '',
        private readonly string $model = 'gemini-1.5-flash'
    ) {
    }

    public function isConfigured(): bool
    {
        return trim($this->apiKey) !== '';
    }

    /**
     * @param array<string, mixed> $context
     */
    public function generateBookingAssistantReply(array $context, string $prompt): string
    {
        $cleanPrompt = trim($prompt);
        if ($cleanPrompt === '') {
            return 'Ask about pricing, timing, services, payments, or how to improve this booking.';
        }

        if (!$this->isConfigured()) {
            return $this->buildFallbackReply($context, $cleanPrompt);
        }

        try {
            $response = $this->httpClient->request('POST', sprintf(
                'https://generativelanguage.googleapis.com/v1beta/models/%s:generateContent?key=%s',
                rawurlencode($this->model),
                rawurlencode($this->apiKey)
            ), [
                'timeout' => 15,
                'json' => [
                    'contents' => [[
                        'parts' => [[
                            'text' => $this->buildPrompt($context, $cleanPrompt),
                        ]],
                    ]],
                    'generationConfig' => [
                        'temperature' => 0.5,
                        'maxOutputTokens' => 350,
                    ],
                ],
            ]);

            $payload = $response->toArray(false);
            $text = trim((string) ($payload['candidates'][0]['content']['parts'][0]['text'] ?? ''));

            return $text !== '' ? $text : $this->buildFallbackReply($context, $cleanPrompt);
        } catch (\Throwable) {
            return $this->buildFallbackReply($context, $cleanPrompt);
        }
    }

    /**
     * @param array<string, mixed> $context
     */
    private function buildPrompt(array $context, string $userPrompt): string
    {
        return implode("\n\n", [
            'You are the TravelXP booking assistant.',
            'Reply for a normal end user in concise, practical language.',
            'Use only the booking context provided and avoid inventing unavailable features.',
            'Booking context:',
            (string) json_encode($context, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
            'User question:',
            $userPrompt,
        ]);
    }

    /**
     * @param array<string, mixed> $context
     */
    private function buildFallbackReply(array $context, string $prompt): string
    {
        $property = (string) ($context['property'] ?? 'this property');
        $price = (float) ($context['total'] ?? 0.0);
        $season = (string) ($context['seasonalLabel'] ?? 'standard season');
        $timing = (string) ($context['timingLabel'] ?? 'standard timing');
        $services = $context['services'] ?? [];
        $serviceText = is_array($services) && $services !== [] ? implode(', ', array_map('strval', $services)) : 'no extra services selected';

        return sprintf(
            "For %s, your current estimate is $%s. Pricing reflects %s and %s. Selected services: %s. Based on your question \"%s\", compare nearby dates, keep long-stay discounts in mind, and lock in payment when this mix feels right.",
            $property,
            number_format($price, 2, '.', ','),
            strtolower($season),
            strtolower($timing),
            $serviceText,
            $prompt
        );
    }
}
