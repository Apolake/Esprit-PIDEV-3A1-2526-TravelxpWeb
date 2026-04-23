<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class TripAiAssistantService
{
    public const ADMIN_TOOLS = [
        'description',
        'recommendations',
        'budget_plan',
        'feasibility_check',
    ];

    public const USER_PRESET_QUESTIONS = [
        'wear' => 'What should I wear for this trip?',
        'risks' => 'What are the expected risks or cautions?',
        'prepare' => 'What should I prepare before going?',
        'best_activities' => 'What kind of activities are best for this trip?',
        'low_budget' => 'Is this trip suitable for a low budget?',
        'avoid' => 'What should I avoid?',
        'highlights' => 'What are the highlights of this destination?',
    ];

    public function __construct(private readonly HttpClientInterface $httpClient)
    {
    }

    private ?string $lastProviderError = null;

    public function getLastProviderError(): ?string
    {
        return $this->lastProviderError;
    }

    public function hasConfiguredLiveProvider(): bool
    {
        return $this->readGeminiApiKey() !== null
            || $this->readOpenAiApiKey() !== null
            || $this->hasConfiguredOllamaProvider();
    }

    /**
     * @param array<string, mixed> $context
     * @return array{title: string, content: string}
     */
    public function generateAdminInsight(array $context, string $tool): array
    {
        $tool = strtolower(trim($tool));
        if (!in_array($tool, self::ADMIN_TOOLS, true)) {
            $tool = 'description';
        }

        $title = match ($tool) {
            'description' => 'AI Trip Description',
            'recommendations' => 'AI Recommendations',
            'budget_plan' => 'AI Budget Plan',
            'feasibility_check' => 'AI Feasibility Review',
            default => 'AI Output',
        };

        $systemPrompt = $this->adminSystemPrompt($tool);
        $userPrompt = $this->buildContextPrompt($context);

        $content = $this->requestPreferredLiveText($systemPrompt, $userPrompt)
            ?? $this->adminFallback($context, $tool);

        return [
            'title' => $title,
            'content' => $content,
        ];
    }

    /**
     * @param array<string, mixed> $context
     * @return array{question: string, answer: string}
     */
    public function answerUserPresetQuestion(array $context, string $questionKey): array
    {
        $key = strtolower(trim($questionKey));
        if (!isset(self::USER_PRESET_QUESTIONS[$key])) {
            $key = 'prepare';
        }

        $question = self::USER_PRESET_QUESTIONS[$key];
        $systemPrompt = $this->userSystemPrompt($question);
        $contextPrompt = $this->buildContextPrompt($context);

        $answer = $this->requestPreferredLiveText($systemPrompt, $contextPrompt, $question, [])
            ?? $this->userFallback($context, $key);

        return [
            'question' => $question,
            'answer' => $answer,
        ];
    }

    /**
     * @param array<string, mixed> $context
     * @return array{question: string, answer: string}|null
     */
    public function answerUserPresetQuestionLive(array $context, string $questionKey): ?array
    {
        $key = strtolower(trim($questionKey));
        if (!isset(self::USER_PRESET_QUESTIONS[$key])) {
            $key = 'prepare';
        }

        $question = self::USER_PRESET_QUESTIONS[$key];
        $answer = $this->generateUserAnswerLive($context, $question, []);
        if ($answer === null) {
            return null;
        }

        return [
            'question' => $question,
            'answer' => $answer,
        ];
    }

    /**
     * @param array<string, mixed> $context
     * @param list<array{role: string, content: string}> $history
     * @return array{question: string, answer: string}
     */
    public function answerUserFreeMessage(array $context, string $message, array $history = []): array
    {
        $question = trim($message);
        if ($question === '') {
            $question = self::USER_PRESET_QUESTIONS['prepare'];
        }

        $systemPrompt = $this->userSystemPrompt($question);
        $contextPrompt = $this->buildContextPrompt($context);

        $geminiAnswer = $this->requestPreferredLiveText($systemPrompt, $contextPrompt, $question, $history);
        $fallbackKey = $this->inferFallbackKeyFromMessage($question);
        $answer = $geminiAnswer
            ?? $this->userFallback($context, $fallbackKey);

        return [
            'question' => $question,
            'answer' => $answer,
        ];
    }

    /**
     * @param array<string, mixed> $context
     * @param list<array{role: string, content: string}> $history
     * @return array{question: string, answer: string}|null
     */
    public function answerUserFreeMessageLive(array $context, string $message, array $history = []): ?array
    {
        $question = trim($message);
        if ($question === '') {
            return null;
        }

        $answer = $this->generateUserAnswerLive($context, $question, $history);
        if ($answer === null) {
            return null;
        }

        return [
            'question' => $question,
            'answer' => $answer,
        ];
    }

    /**
     * @param array<string, mixed> $context
     * @param list<array{role: string, content: string}> $history
     */
    private function generateUserAnswerLive(array $context, string $question, array $history = []): ?string
    {
        $this->lastProviderError = null;
        $systemPrompt = $this->userSystemPrompt($question);
        $contextPrompt = $this->buildContextPrompt($context);

        return $this->requestPreferredLiveText($systemPrompt, $contextPrompt, $question, $history);
    }

    /**
     * @param list<array{role: string, content: string}> $history
     */
    private function requestPreferredLiveText(
        string $systemPrompt,
        string $contextPrompt,
        string $question = '',
        array $history = [],
    ): ?string {
        $answer = null;

        if ($this->readGeminiApiKey() !== null) {
            $answer = $this->requestGeminiText($systemPrompt, $contextPrompt, $question, $history);
            if ($answer !== null) {
                return $answer;
            }
        }

        if ($this->readOpenAiApiKey() !== null) {
            $answer = $this->requestAiText($systemPrompt, $contextPrompt);
            if ($answer !== null) {
                return $answer;
            }
        }

        if ($this->hasConfiguredOllamaProvider()) {
            return $this->requestOllamaText($systemPrompt, $contextPrompt, $question, $history);
        }

        return null;
    }

    /**
     * @param array<string, mixed> $context
     */
    private function buildContextPrompt(array $context): string
    {
        $json = json_encode($context, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        if (!is_string($json)) {
            $json = '{}';
        }

        return "Trip context data:\n" . $json;
    }

    private function adminSystemPrompt(string $tool): string
    {
        $base = "You are a professional travel operations assistant. "
            . "Use only the provided trip context. "
            . "Do not invent missing facts. "
            . "Keep output concise, practical, and structured with short headings and bullet points.";

        $task = match ($tool) {
            'description' => "Generate one polished trip description suitable for admin editing and user-facing display.",
            'recommendations' => "Generate destination-specific recommendations and practical tips for this trip.",
            'budget_plan' => "Generate a simple budget plan with clear categories and estimated allocations.",
            'feasibility_check' => "Review feasibility and list potential concerns: schedule load, budget pressure, weather or practical risks, plus fixes.",
            default => "Generate a helpful trip summary.",
        };

        return $base . ' ' . $task;
    }

    private function userSystemPrompt(string $question): string
    {
        return "You are a helpful travel assistant answering a user inside a specific trip context. "
            . "Answer only using provided trip data and practical travel knowledge. "
            . "Be specific, friendly, and concise. Keep answers short (max about 120 words) unless the user asks for detail. "
            . "Question: " . $question;
    }

    private function requestAiText(string $systemPrompt, string $userPrompt): ?string
    {
        $apiKey = $this->readOpenAiApiKey();
        if ($apiKey === null || trim($apiKey) === '') {
            $this->setLastProviderErrorIfEmpty('OpenAI API key is missing or invalid.');
            return null;
        }

        $model = $this->readEnvValue('OPENAI_MODEL') ?: 'gpt-4.1-mini';

        try {
            $response = $this->httpClient->request('POST', 'https://api.openai.com/v1/responses', [
                'headers' => [
                    'Authorization' => 'Bearer ' . trim($apiKey),
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'proxy' => null,
                'no_proxy' => '*',
                'json' => [
                    'model' => $model,
                    'temperature' => 0.3,
                    'max_output_tokens' => 700,
                    'input' => [
                        ['role' => 'system', 'content' => [['type' => 'input_text', 'text' => $systemPrompt]]],
                        ['role' => 'user', 'content' => [['type' => 'input_text', 'text' => $userPrompt]]],
                    ],
                ],
                'timeout' => 18,
            ]);

            if ($response->getStatusCode() >= 300) {
                $payload = $response->toArray(false);
                $apiMessage = is_string($payload['error']['message'] ?? null) ? trim($payload['error']['message']) : null;
                $this->setLastProviderErrorIfEmpty($apiMessage !== null && $apiMessage !== '' ? $apiMessage : 'OpenAI provider request failed.');
                return null;
            }

            $payload = $response->toArray(false);
            if (is_string($payload['output_text'] ?? null) && trim($payload['output_text']) !== '') {
                return trim($payload['output_text']);
            }

            $output = $payload['output'] ?? null;
            if (!is_array($output)) {
                return null;
            }

            $chunks = [];
            foreach ($output as $item) {
                if (!is_array($item)) {
                    continue;
                }
                $content = $item['content'] ?? null;
                if (!is_array($content)) {
                    continue;
                }
                foreach ($content as $segment) {
                    if (!is_array($segment)) {
                        continue;
                    }
                    $text = $segment['text'] ?? null;
                    if (is_string($text) && trim($text) !== '') {
                        $chunks[] = trim($text);
                    }
                }
            }

            if ($chunks === []) {
                return null;
            }

            return trim(implode("\n\n", $chunks));
        } catch (\Throwable) {
            $this->setLastProviderErrorIfEmpty('OpenAI provider request failed due to a network/runtime error.');
            return null;
        }
    }

    /**
     * @param list<array{role: string, content: string}> $history
     */
    private function requestGeminiText(
        string $systemPrompt,
        string $contextPrompt,
        string $question,
        array $history = [],
    ): ?string {
        $apiKey = $this->readGeminiApiKey();
        if ($apiKey === null || trim($apiKey) === '') {
            $this->setLastProviderErrorIfEmpty('Gemini API key is missing or invalid.');
            return null;
        }

        $model = $this->normalizeGeminiModelName($this->readEnvValue('GEMINI_MODEL') ?: 'gemini-2.0-flash');
        $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/' . urlencode($model) . ':generateContent';

        $parts = [
            ['text' => "System:\n" . $systemPrompt],
            ['text' => "\n\n" . $contextPrompt],
        ];

        foreach (array_slice($history, -4) as $turn) {
            if (!is_array($turn)) {
                continue;
            }
            $role = strtolower(trim((string) ($turn['role'] ?? '')));
            $content = trim((string) ($turn['content'] ?? ''));
            if ($content === '' || !in_array($role, ['user', 'assistant'], true)) {
                continue;
            }
            $speaker = $role === 'assistant' ? 'Assistant' : 'User';
            $parts[] = ['text' => sprintf("\n%s: %s", $speaker, $content)];
        }

        $parts[] = ['text' => "\n\nUser question: " . $question];

        try {
            $response = $this->httpClient->request('POST', $baseUrl . '?key=' . urlencode($apiKey), [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'proxy' => null,
                'no_proxy' => '*',
                'json' => [
                    'contents' => [
                        [
                            'role' => 'user',
                            'parts' => $parts,
                        ],
                    ],
                    'generationConfig' => [
                        'temperature' => 0.3,
                        'maxOutputTokens' => 700,
                    ],
                ],
                'timeout' => 18,
            ]);

            if ($response->getStatusCode() >= 300) {
                $payload = $response->toArray(false);
                $apiMessage = is_string($payload['error']['message'] ?? null) ? trim($payload['error']['message']) : null;
                $this->setLastProviderErrorIfEmpty($apiMessage !== null && $apiMessage !== '' ? $apiMessage : 'Gemini provider request failed.');
                return null;
            }

            $payload = $response->toArray(false);
            $candidates = is_array($payload['candidates'] ?? null) ? $payload['candidates'] : [];
            foreach ($candidates as $candidate) {
                if (!is_array($candidate)) {
                    continue;
                }
                $content = is_array($candidate['content'] ?? null) ? $candidate['content'] : [];
                $candidateParts = is_array($content['parts'] ?? null) ? $content['parts'] : [];
                $chunks = [];
                foreach ($candidateParts as $part) {
                    if (!is_array($part)) {
                        continue;
                    }
                    $text = $part['text'] ?? null;
                    if (is_string($text) && trim($text) !== '') {
                        $chunks[] = trim($text);
                    }
                }
                if ($chunks !== []) {
                    return trim(implode("\n\n", $chunks));
                }
            }

            return null;
        } catch (\Throwable) {
            $this->setLastProviderErrorIfEmpty('Gemini provider request failed due to a network/runtime error.');
            return null;
        }
    }

    /**
     * @param list<array{role: string, content: string}> $history
     */
    private function requestOllamaText(
        string $systemPrompt,
        string $contextPrompt,
        string $question = '',
        array $history = [],
    ): ?string {
        $baseUrl = $this->readOllamaBaseUrl();
        $model = $this->readEnvValue('OLLAMA_MODEL') ?: 'llama3.2';
        $promptUserMessage = $question !== ''
            ? ($contextPrompt . "\n\nUser question: " . $question)
            : $contextPrompt;

        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
        ];
        foreach (array_slice($history, -8) as $turn) {
            if (!is_array($turn)) {
                continue;
            }
            $role = strtolower(trim((string) ($turn['role'] ?? '')));
            $content = trim((string) ($turn['content'] ?? ''));
            if ($content === '' || !in_array($role, ['user', 'assistant'], true)) {
                continue;
            }
            $messages[] = [
                'role' => $role === 'assistant' ? 'assistant' : 'user',
                'content' => $content,
            ];
        }
        $messages[] = ['role' => 'user', 'content' => $promptUserMessage];

        try {
            $response = $this->httpClient->request('POST', rtrim($baseUrl, '/') . '/api/chat', [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'proxy' => null,
                'no_proxy' => '*',
                'json' => [
                    'model' => $model,
                    'messages' => $messages,
                    'stream' => false,
                    'options' => [
                        'temperature' => 0.3,
                        'num_predict' => 120,
                    ],
                ],
                // Local CPU inference can take longer, especially on first call after model load.
                'timeout' => 300,
                'max_duration' => 300,
            ]);

            if ($response->getStatusCode() >= 300) {
                $payload = $response->toArray(false);
                $apiMessage = is_string($payload['error'] ?? null) ? trim($payload['error']) : null;
                $this->setLastProviderErrorIfEmpty(
                    $apiMessage !== null && $apiMessage !== ''
                        ? ('Ollama error: ' . $apiMessage)
                        : 'Ollama provider request failed.'
                );
                return null;
            }

            $payload = $response->toArray(false);
            $content = $payload['message']['content'] ?? null;
            if (is_string($content) && trim($content) !== '') {
                return trim($content);
            }

            return null;
        } catch (\Throwable $e) {
            $message = trim($e->getMessage());
            $this->setLastProviderErrorIfEmpty(
                $message !== ''
                    ? ('Ollama request failed: ' . $message)
                    : sprintf('Ollama is not reachable at %s. Ensure Ollama is installed and running.', $baseUrl)
            );
            return null;
        }
    }

    /**
     * @param array<string, mixed> $context
     */
    private function adminFallback(array $context, string $tool): string
    {
        $tripName = (string) ($context['tripName'] ?? 'This trip');
        $origin = (string) ($context['origin'] ?? '');
        $destination = (string) ($context['destination'] ?? '');
        $route = trim(($origin !== '' ? $origin : 'Origin TBD') . ' -> ' . ($destination !== '' ? $destination : 'Destination TBD'));
        $dateRange = trim((string) ($context['dateRange'] ?? 'Dates to be confirmed'));
        $budget = (float) ($context['budgetAmount'] ?? 0.0);
        $currency = (string) ($context['currency'] ?? 'USD');
        $activities = is_array($context['activities'] ?? null) ? $context['activities'] : [];
        $warnings = is_array($context['weatherWarnings'] ?? null) ? $context['weatherWarnings'] : [];

        if ($tool === 'description') {
            $line1 = sprintf('%s is a curated journey from %s designed for travelers seeking a balanced and practical experience.', $tripName, $route);
            $line2 = sprintf('The trip runs %s with a target budget of %s %s and includes opportunities for cultural discovery, local activities, and flexible planning.', $dateRange, number_format($budget, 2, '.', ','), $currency);
            $line3 = count($activities) > 0
                ? sprintf('Current plan already includes %d linked activities, making it suitable for both structured and optional exploration.', count($activities))
                : 'No activities are linked yet, so the plan remains flexible for custom scheduling.';

            return $line1 . "\n\n" . $line2 . "\n\n" . $line3;
        }

        if ($tool === 'recommendations') {
            $tips = [
                sprintf('- Prioritize 2-3 signature experiences in %s before filling smaller time slots.', $destination !== '' ? $destination : 'the destination'),
                '- Keep one light buffer block per day for delays or spontaneous stops.',
                '- Pre-book high-demand tickets and transport where possible.',
                '- Group activities by area to reduce transfer time and transport cost.',
            ];
            if ($warnings !== []) {
                $tips[] = '- Weather watch: ' . (string) $warnings[0];
            }

            return "Recommended actions:\n" . implode("\n", $tips);
        }

        if ($tool === 'budget_plan') {
            if ($budget <= 0) {
                return "Budget planning suggestion:\n"
                    . "- Set a total budget first to unlock realistic planning.\n"
                    . "- Start with fixed costs (transport + accommodation).\n"
                    . "- Keep 10-15% reserve for unexpected expenses.";
            }

            $transport = $budget * 0.35;
            $activitiesBudget = $budget * 0.25;
            $food = $budget * 0.2;
            $reserve = $budget * 0.15;
            $local = $budget - ($transport + $activitiesBudget + $food + $reserve);

            return "Budget plan suggestion:\n"
                . sprintf("- Transportation: %s %s\n", number_format($transport, 2, '.', ','), $currency)
                . sprintf("- Activities: %s %s\n", number_format($activitiesBudget, 2, '.', ','), $currency)
                . sprintf("- Food & daily needs: %s %s\n", number_format($food, 2, '.', ','), $currency)
                . sprintf("- Reserve fund: %s %s\n", number_format($reserve, 2, '.', ','), $currency)
                . sprintf("- Local transfers/misc: %s %s", number_format(max(0.0, $local), 2, '.', ','), $currency);
        }

        $concerns = [];
        $activityCount = count($activities);
        $durationDays = max(1, (int) ($context['durationDays'] ?? 1));
        if ($activityCount > ($durationDays * 2)) {
            $concerns[] = '- Activity density is high for this date range; reduce or prioritize top activities.';
        }
        if ($budget > 0 && $budget / $durationDays < 45) {
            $concerns[] = '- Daily budget appears tight; consider fewer paid activities or cheaper transport options.';
        }
        if ($warnings !== []) {
            $concerns[] = '- Weather caution: ' . (string) $warnings[0];
        }
        if ($concerns === []) {
            $concerns[] = '- No major conflicts detected based on current data. Keep monitoring timings and bookings.';
        }

        return "Feasibility review:\n" . implode("\n", $concerns);
    }

    /**
     * @param array<string, mixed> $context
     */
    private function userFallback(array $context, string $questionKey): string
    {
        $destination = (string) ($context['destination'] ?? 'this destination');
        $seasonHint = (string) ($context['seasonHint'] ?? 'current season');
        $budget = (float) ($context['budgetAmount'] ?? 0.0);
        $currency = (string) ($context['currency'] ?? 'USD');
        $warnings = is_array($context['weatherWarnings'] ?? null) ? $context['weatherWarnings'] : [];

        return match ($questionKey) {
            'wear' => "Wear in layers and prioritize comfort for walking.\n"
                . "For {$destination}, plan breathable daytime clothes plus a light outer layer for evenings.\n"
                . "Season note: {$seasonHint}.",
            'risks' => "Main cautions:\n"
                . "- Keep digital/printed copies of key documents.\n"
                . "- Watch transport timing between activities.\n"
                . ($warnings !== [] ? '- Weather-related caution: ' . (string) $warnings[0] : '- Check local weather daily before outdoor plans.'),
            'prepare' => "Before departure:\n"
                . "- Confirm bookings and transfer windows.\n"
                . "- Prepare essentials (ID, payment method, medicine, charger, offline maps).\n"
                . "- Keep a small daily spending cap to stay on budget.",
            'best_activities' => "Best activity types for {$destination} usually include:\n"
                . "- Local highlights and cultural spots\n"
                . "- One signature experience\n"
                . "- One flexible low-cost backup option each day",
            'low_budget' => $budget > 0
                ? sprintf("This trip can be budget-friendly with planning.\nCurrent total budget is %s %s.\nUse free attractions, grouped routes, and capped daily spend.", number_format($budget, 2, '.', ','), $currency)
                : "Budget suitability is hard to assess because no budget is set yet.\nSet a target amount and I can give a clearer low-budget strategy.",
            'avoid' => "Avoid:\n"
                . "- Overloading each day with too many paid activities\n"
                . "- Last-minute transport decisions in peak times\n"
                . "- Ignoring weather and safety updates for outdoor plans",
            'highlights' => "Destination highlights to prioritize in {$destination}:\n"
                . "- One iconic landmark\n"
                . "- One local neighborhood/market experience\n"
                . "- One scenic or cultural activity during your best weather window",
            default => "Prepare your essentials, verify bookings, and keep one flexible backup plan each day.",
        };
    }

    private function inferFallbackKeyFromMessage(string $message): string
    {
        $text = strtolower(trim($message));
        if ($text === '') {
            return 'prepare';
        }

        return match (true) {
            str_contains($text, 'wear') || str_contains($text, 'cloth') || str_contains($text, 'outfit') => 'wear',
            str_contains($text, 'risk') || str_contains($text, 'safe') || str_contains($text, 'danger') || str_contains($text, 'caution') => 'risks',
            str_contains($text, 'budget') || str_contains($text, 'cheap') || str_contains($text, 'cost') || str_contains($text, 'money') => 'low_budget',
            str_contains($text, 'avoid') || str_contains($text, 'dont') || str_contains($text, 'don\'t') => 'avoid',
            str_contains($text, 'highlight') || str_contains($text, 'best place') || str_contains($text, 'must see') => 'highlights',
            str_contains($text, 'activity') || str_contains($text, 'things to do') => 'best_activities',
            default => 'prepare',
        };
    }

    private function readEnvValue(string $key): ?string
    {
        $value = $_SERVER[$key] ?? $_ENV[$key] ?? getenv($key);
        if (!is_string($value)) {
            return null;
        }

        return trim($value) !== '' ? trim($value) : null;
    }

    private function readGeminiApiKey(): ?string
    {
        return $this->sanitizeApiKey(
            $this->readEnvValue('GEMINI_API_KEY')
            ?? $this->readEnvValue('GOOGLE_API_KEY')
        );
    }

    private function readOpenAiApiKey(): ?string
    {
        return $this->sanitizeApiKey($this->readEnvValue('OPENAI_API_KEY'));
    }

    private function hasConfiguredOllamaProvider(): bool
    {
        return $this->readBoolEnv('OLLAMA_ENABLED', false);
    }

    private function readOllamaBaseUrl(): string
    {
        return $this->readEnvValue('OLLAMA_BASE_URL') ?: 'http://127.0.0.1:11434';
    }

    private function readBoolEnv(string $key, bool $default = false): bool
    {
        $value = $this->readEnvValue($key);
        if ($value === null) {
            return $default;
        }

        return in_array(strtolower(trim($value)), ['1', 'true', 'yes', 'on'], true);
    }

    private function sanitizeApiKey(?string $apiKey): ?string
    {
        if ($apiKey === null) {
            return null;
        }

        $normalized = trim($apiKey);
        if ($normalized === '') {
            return null;
        }

        $upper = strtoupper($normalized);
        $placeholderFragments = [
            'PUT_YOUR_KEY_HERE',
            'YOUR_API_KEY',
            'CHANGE_ME',
            'REPLACE_ME',
            'EXAMPLE_KEY',
            'PLACEHOLDER',
        ];
        foreach ($placeholderFragments as $fragment) {
            if (str_contains($upper, $fragment)) {
                return null;
            }
        }

        return $normalized;
    }

    private function normalizeGeminiModelName(string $model): string
    {
        $normalized = trim($model);
        if (str_starts_with($normalized, 'models/')) {
            $normalized = substr($normalized, 7);
        }

        return $normalized !== '' ? $normalized : 'gemini-2.0-flash';
    }

    private function setLastProviderErrorIfEmpty(string $message): void
    {
        $message = trim($message);
        if ($message === '') {
            return;
        }
        if ($this->lastProviderError === null || trim($this->lastProviderError) === '') {
            $this->lastProviderError = $message;
        }
    }
}
