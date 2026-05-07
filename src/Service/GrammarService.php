<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class GrammarService
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly ?string $openAiApiKey = null,
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
            $rewrite = $this->rewriteWithOpenAi($content, $language);
            if ($rewrite !== null) {
                return [
                    'correctedText' => $rewrite,
                    'changed' => $rewrite !== $content,
                    'message' => $rewrite !== $content ? 'Grammar suggestions applied.' : 'No grammar suggestions found.',
                ];
            }

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
                $replacementCandidates = $match['replacements'] ?? [];
                $replacement = $this->selectReplacement($replacementCandidates);

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

    private function rewriteWithOpenAi(string $content, string $language): ?string
    {
        if ($this->openAiApiKey === null || trim($this->openAiApiKey) === '') {
            return null;
        }

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
                            'content' => 'You are an expert English copy editor. Correct grammar, spelling, punctuation, capitalization, and sentence structure. Preserve the original meaning, tone, and length as much as possible. Return only the corrected text with no explanation, no bullets, and no markdown.',
                        ],
                        [
                            'role' => 'user',
                            'content' => sprintf(
                                "Correct this %s text:\n\n%s",
                                $this->normalizeLanguage($language),
                                $content
                            ),
                        ],
                    ],
                    'temperature' => 0.1,
                ],
                'timeout' => 20,
            ]);

            $data = $response->toArray(false);
            $text = trim((string) ($data['choices'][0]['message']['content'] ?? ''));

            if ($text !== '') {
                return $text;
            }
        } catch (\Throwable) {
            return null;
        }

        return null;
    }

    /**
     * @param mixed $replacementCandidates
     */
    private function selectReplacement(mixed $replacementCandidates): string
    {
        if (!is_array($replacementCandidates) || $replacementCandidates === []) {
            return '';
        }

        $preferred = [];
        foreach ($replacementCandidates as $candidate) {
            if (is_array($candidate) && isset($candidate['value'])) {
                $value = trim((string) $candidate['value']);
                if ($value !== '') {
                    $preferred[] = $value;
                }
            }
        }

        if ($preferred === []) {
            return '';
        }

        foreach ($preferred as $candidate) {
            if (!preg_match('/^[\p{L}\p{N}\p{P}\p{Zs}\'"-]+$/u', $candidate)) {
                continue;
            }

            return $candidate;
        }

        return $preferred[0];
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
