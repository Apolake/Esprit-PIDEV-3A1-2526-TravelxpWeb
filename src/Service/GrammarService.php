<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GrammarService
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly string $languageToolUrl = 'https://api.languagetool.org/v2/check',
    ) {
    }

    /**
     * @return array{correctedText:string,changed:bool,message:string}
     */
    public function correct(string $text, string $language): array
    {
        $content = trim($text);
        if ($content === '') {
            return [
                'correctedText' => $text,
                'changed' => false,
                'message' => 'Text is empty.',
            ];
        }

        try {
            $response = $this->httpClient->request('POST', $this->languageToolUrl, [
                'headers' => [
                    'Accept' => 'application/json',
                ],
                'body' => [
                    'text' => $content,
                    'language' => $this->normalizeLanguage($language),
                ],
                'timeout' => 10,
            ]);

            $data = $response->toArray(false);
            $matches = is_array($data['matches'] ?? null) ? $data['matches'] : [];
            if ($matches === []) {
                return [
                    'correctedText' => $text,
                    'changed' => false,
                    'message' => 'No grammar suggestions found.',
                ];
            }

            $corrected = $content;
            $replacements = [];
            foreach ($matches as $match) {
                if (!is_array($match)) {
                    continue;
                }

                $offset = (int) ($match['offset'] ?? -1);
                $length = (int) ($match['length'] ?? 0);
                $replacement = '';
                $replacementCandidates = $match['replacements'] ?? [];
                if (is_array($replacementCandidates) && isset($replacementCandidates[0]['value'])) {
                    $replacement = (string) $replacementCandidates[0]['value'];
                }

                if ($offset < 0 || $length <= 0 || $replacement === '') {
                    continue;
                }

                $replacements[] = [
                    'offset' => $offset,
                    'length' => $length,
                    'value' => $replacement,
                ];
            }

            usort($replacements, static fn (array $a, array $b): int => $b['offset'] <=> $a['offset']);
            foreach ($replacements as $replacement) {
                $corrected = substr($corrected, 0, $replacement['offset']) . $replacement['value'] . substr($corrected, $replacement['offset'] + $replacement['length']);
            }

            return [
                'correctedText' => $corrected,
                'changed' => $corrected !== $content,
                'message' => $corrected !== $content ? 'Grammar suggestions applied.' : 'No grammar suggestions found.',
            ];
        } catch (\Throwable) {
            return [
                'correctedText' => $text,
                'changed' => false,
                'message' => 'Grammar service is unavailable right now.',
            ];
        }
    }

    private function normalizeLanguage(string $language): string
    {
        return match (strtoupper(trim($language))) {
            'EN', 'EN-US', 'EN-GB' => 'en-US',
            'FR', 'FR-FR' => 'fr',
            default => 'en-US',
        };
    }
}
