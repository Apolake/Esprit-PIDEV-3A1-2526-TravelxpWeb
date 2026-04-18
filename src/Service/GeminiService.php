<?php

namespace App\Service;

use App\Entity\Offer;
use App\Entity\Property;
use App\Repository\PropertyRepository;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GeminiService
{
    private const DEFAULT_API_URL = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent';
    private const DEFAULT_MODEL = 'gemini-pro';

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly PropertyRepository $propertyRepository,
        private readonly ?LoggerInterface $logger = null,
    ) {
    }

    /**
     * @param array<int, Offer> $offers
     * @param array<int, array{role:string,content:string}> $history
     */
    public function getRecommendation(string $message, Property $property, array $offers, array $history = []): string
    {
        $message = trim(strip_tags($message));
        if ($message === '') {
            return 'Please ask a question about the property, pricing, or offers.';
        }

        $similarProperties = $this->findSimilarProperties($property);
        $context = $this->buildContext($property, $offers, $similarProperties, $history);
        $apiKey = trim((string) (getenv('GEMINI_API_KEY') ?: getenv('AI_RECOMMENDATION_API_KEY') ?: ''));
        $apiUrl = trim((string) (getenv('GEMINI_API_URL') ?: self::DEFAULT_API_URL));
        $model = trim((string) (getenv('GEMINI_MODEL') ?: self::DEFAULT_MODEL));

        if ($apiKey === '') {
            return $this->buildFallbackResponse($message, $property, $offers, $similarProperties, $history);
        }

        try {
            $prompt = implode("\n\n", [
                'You are a helpful travel assistant.',
                'You are helping a user explore a property and its offers.',
                '',
                'Property information:',
                $context,
                '',
                'Instructions:',
                '- Answer the user question naturally and conversationally',
                '- Do NOT always repeat all property details',
                '- Only mention price or offers if relevant to the question',
                '- Avoid repeating the same sentence structure',
                '- Be friendly, helpful, and concise',
                '- If the user asks about recommendations, suggest the best offer',
                '- If the question is unrelated to offers, do NOT force them',
                '',
                'User question:',
                $message,
                '',
                'Reply in 2-5 short sentences unless the user asks for more detail.',
            ]);

            $response = $this->httpClient->request('POST', $this->normalizeApiUrl($apiUrl, $model), [
                'query' => ['key' => $apiKey],
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'json' => [
                    'contents' => [[
                        'role' => 'user',
                        'parts' => [[
                            'text' => $prompt,
                        ]],
                    ]],
                    'generationConfig' => [
                        'temperature' => 0.9,
                        'topK' => 40,
                        'topP' => 0.95,
                        'maxOutputTokens' => 350,
                    ],
                ],
                'timeout' => 20,
            ]);

            $statusCode = $response->getStatusCode();
            if ($statusCode < 200 || $statusCode >= 300) {
                $this->logger?->warning('Gemini recommendation request failed', [
                    'status' => $statusCode,
                    'api_url' => $apiUrl,
                ]);

                return $this->buildFallbackResponse($message, $property, $offers, $similarProperties, $history);
            }

            $data = $response->toArray(false);
            $reply = '';

            if (isset($data['candidates'][0]['content']['parts']) && is_array($data['candidates'][0]['content']['parts'])) {
                $parts = array_map(
                    static fn (array $part): string => (string) ($part['text'] ?? ''),
                    $data['candidates'][0]['content']['parts']
                );
                $reply = trim(implode('', $parts));
            }

            if ($reply === '') {
                return $this->buildFallbackResponse($message, $property, $offers, $similarProperties, $history);
            }

            $lastAssistantReply = $this->extractLastAssistantReply($history);
            if ($lastAssistantReply !== null && $this->isSemanticallySameReply($reply, $lastAssistantReply)) {
                return $this->buildFallbackResponse($message, $property, $offers, $similarProperties, $history);
            }

            return $reply;
        } catch (\Throwable $exception) {
            $this->logger?->warning('Gemini recommendation fallback used', [
                'error' => $exception->getMessage(),
            ]);

            return $this->buildFallbackResponse($message, $property, $offers, $similarProperties, $history);
        }
    }

    /**
     * @param array<int, Offer> $offers
     * @param list<Property> $similarProperties
     * @param array<int, array{role:string,content:string}> $history
     */
    private function buildContext(Property $property, array $offers, array $similarProperties, array $history): string
    {
        $lines = [
            '- Title: ' . ($property->getTitle() ?? '—'),
            '- Location: ' . trim(($property->getCity() ?? '—') . ', ' . ($property->getCountry() ?? '—')),
            '- Address: ' . ($property->getAddress() ?? '—'),
            '- Price per night: ' . number_format((float) $property->getPricePerNight(), 2, '.', ',') . ' USD',
            '- Bedrooms: ' . $property->getBedrooms(),
            '- Max guests: ' . $property->getMaxGuests(),
            '- Type: ' . ($property->getPropertyType() ?? '—'),
            '- Description: ' . ($property->getDescription() ?: '—'),
        ];

        if ($offers === []) {
            $lines[] = '';
            $lines[] = 'Offers: none available';
        } else {
            $lines[] = '';
            $lines[] = 'Offers:';

            foreach ($offers as $offer) {
                $lines[] = sprintf(
                    '- %s | Discount: %s%% | Dates: %s → %s | Status: %s',
                    $offer->getTitle() ?? 'Untitled offer',
                    number_format((float) $offer->getDiscountPercentage(), 2, '.', ','),
                    $offer->getStartDate()?->format('Y-m-d') ?? '—',
                    $offer->getEndDate()?->format('Y-m-d') ?? '—',
                    $offer->isActive() ? 'Active' : 'Inactive'
                );

                if ($offer->getDescription()) {
                    $lines[] = '  Description: ' . $offer->getDescription();
                }
            }
        }

        if ($history !== []) {
            $lines[] = '';
            $lines[] = 'Conversation so far:';

            foreach ($history as $entry) {
                $role = strtolower((string) ($entry['role'] ?? ''));
                $content = trim(strip_tags((string) ($entry['content'] ?? '')));
                if ($content === '') {
                    continue;
                }

                $lines[] = sprintf('- %s: %s', $role === 'assistant' ? 'model' : 'user', $content);
            }
        }

        if ($similarProperties !== []) {
            $lines[] = '';
            $lines[] = 'Similar properties:';

            foreach ($similarProperties as $similarProperty) {
                $lines[] = sprintf(
                    '- %s | %s, %s | Price: %s USD',
                    $similarProperty->getTitle() ?? 'Untitled property',
                    $similarProperty->getCity() ?? '—',
                    $similarProperty->getCountry() ?? '—',
                    number_format((float) $similarProperty->getPricePerNight(), 2, '.', ',')
                );
            }
        }

        return implode("\n", $lines);
    }

    /**
     * @param array<int, Offer> $offers
     * @param list<Property> $similarProperties
     * @param array<int, array{role:string,content:string}> $history
     */
    private function buildFallbackResponse(string $message, Property $property, array $offers, array $similarProperties, array $history): string
    {
        $lowerMessage = mb_strtolower($message);
        $location = trim(($property->getCity() ?? '—') . ', ' . ($property->getCountry() ?? '—'));
        $price = number_format((float) $property->getPricePerNight(), 2, '.', ',') . ' USD';

        $bestOfferLine = null;
        if ($offers !== []) {
            usort($offers, static fn (Offer $left, Offer $right): int => (float) $right->getDiscountPercentage() <=> (float) $left->getDiscountPercentage());
            $bestOffer = $offers[0];
            $bestOfferLine = sprintf(
                '**%s** gives **%s%% off** (%s to %s).',
                $bestOffer->getTitle() ?? 'Untitled offer',
                number_format((float) $bestOffer->getDiscountPercentage(), 2, '.', ','),
                $bestOffer->getStartDate()?->format('Y-m-d') ?? '—',
                $bestOffer->getEndDate()?->format('Y-m-d') ?? '—'
            );
        }

        if (preg_match('/discount|offer|deal|promotion|best offer/i', $lowerMessage) === 1) {
            $reply = $bestOfferLine !== null
                ? 'Great question. The strongest deal right now is ' . $bestOfferLine
                : 'I checked this property and there are no active offers right now.';

            return $this->ensureDifferentFromPrevious($reply, $history);
        }

        if (preg_match('/price|cost|night|rate/i', $lowerMessage) === 1) {
            $reply = 'The current nightly price is **' . $price . '**.';
            if ($bestOfferLine !== null) {
                $reply .= ' If you want to save more, ' . $bestOfferLine;
            }

            return $this->ensureDifferentFromPrevious($reply, $history);
        }

        if (preg_match('/family|kids|children|guest|people|person|persons|group|4|four/i', $lowerMessage) === 1) {
            $capacityText = $property->getMaxGuests() >= 4
                ? 'Yes, this place can host up to **' . $property->getMaxGuests() . '** guests, so it should work for a family of four.'
                : 'This place hosts up to **' . $property->getMaxGuests() . '** guests, so it may feel tight for a family of four.';

            return $this->ensureDifferentFromPrevious($capacityText, $history);
        }

        $reply = sprintf(
            'This property, **%s**, is in **%s** with a nightly price of **%s**. Tell me what matters most to you and I can narrow it down. ',
            $property->getTitle() ?? 'this listing',
            $location,
            $price
        );

        if ($bestOfferLine !== null && random_int(0, 1) === 1) {
            $reply .= 'Also, ' . $bestOfferLine;
        } elseif ($similarProperties !== []) {
            $similarTitles = array_map(static fn (Property $similarProperty): string => $similarProperty->getTitle() ?? 'Untitled property', $similarProperties);
            $reply .= 'If you want alternatives, I can also compare it with ' . implode(', ', $similarTitles) . '.';
        }

        return $this->ensureDifferentFromPrevious(trim($reply), $history);
    }

    /**
     * @param array<int, array{role:string,content:string}> $history
     */
    private function ensureDifferentFromPrevious(string $reply, array $history): string
    {
        $lastAssistantReply = $this->extractLastAssistantReply($history);
        if ($lastAssistantReply === null || !$this->isSemanticallySameReply($reply, $lastAssistantReply)) {
            return $reply;
        }

        $variants = [
            $reply . ' Want me to compare this with similar listings?',
            'Sure. ' . $reply,
            $reply . ' I can also break down value-for-money if you want.',
        ];

        return $variants[array_rand($variants)];
    }

    /**
     * @param array<int, array{role:string,content:string}> $history
     */
    private function extractLastAssistantReply(array $history): ?string
    {
        for ($index = count($history) - 1; $index >= 0; --$index) {
            $entry = $history[$index] ?? null;
            if (!is_array($entry)) {
                continue;
            }

            if (($entry['role'] ?? '') !== 'assistant') {
                continue;
            }

            $content = trim((string) ($entry['content'] ?? ''));

            return $content !== '' ? $content : null;
        }

        return null;
    }

    private function isSemanticallySameReply(string $left, string $right): bool
    {
        $normalize = static fn (string $value): string => preg_replace('/\s+/', ' ', mb_strtolower(trim(strip_tags($value)))) ?? '';

        return $normalize($left) === $normalize($right);
    }

    private function normalizeApiUrl(string $apiUrl, string $model): string
    {
        if (str_contains($apiUrl, ':generateContent')) {
            return $apiUrl;
        }

        return rtrim($apiUrl, '/') . '/models/' . rawurlencode($model) . ':generateContent';
    }

    /**
     * @return list<Property>
     */
    private function findSimilarProperties(Property $property): array
    {
        $qb = $this->propertyRepository->createQueryBuilder('p')
            ->andWhere('p.id != :currentId')
            ->setParameter('currentId', $property->getId())
            ->setMaxResults(3)
            ->orderBy('p.isActive', 'DESC')
            ->addOrderBy('p.updatedAt', 'DESC');

        if ($property->getCity() !== null) {
            $qb->andWhere('LOWER(p.city) = LOWER(:city)')
                ->setParameter('city', $property->getCity());
        }

        if ($property->getPropertyType() !== null) {
            $qb->addOrderBy('CASE WHEN p.propertyType = :propertyType THEN 0 ELSE 1 END', 'ASC')
                ->setParameter('propertyType', $property->getPropertyType());
        }

        return $qb->getQuery()->getResult();
    }
}