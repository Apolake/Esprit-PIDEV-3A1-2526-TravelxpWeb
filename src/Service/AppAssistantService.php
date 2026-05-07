<?php

namespace App\Service;

use App\Entity\Offer;
use App\Entity\Property;
use App\Entity\Service as TravelService;
use App\Entity\Trip;
use App\Repository\OfferRepository;
use App\Repository\PropertyRepository;
use App\Repository\ServiceRepository;
use App\Repository\TripRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class AppAssistantService
{
    private const DEFAULT_MODEL = 'gemini-2.5-flash';

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly PropertyRepository $propertyRepository,
        private readonly ServiceRepository $serviceRepository,
        private readonly OfferRepository $offerRepository,
        private readonly TripRepository $tripRepository,
        private readonly CacheInterface $cache,
        private readonly LoggerInterface $logger,
        #[Autowire('%env(default::GEMINI_API_KEY)%')]
        private readonly ?string $geminiApiKey,
        #[Autowire('%env(default::GEMINI_MODEL)%')]
        private readonly ?string $geminiModel,
    ) {
    }

    /**
     * @param list<array{role:mixed,content:mixed}> $history
     */
    public function ask(
        string $message,
        array $history,
        string $baseUrl,
        bool $isAuthenticated,
        bool $isAdmin,
    ): string {
        $apiKey = trim((string) $this->geminiApiKey);
        if ($apiKey === '') {
            throw new \RuntimeException('Assistant is not configured. Set GEMINI_API_KEY.');
        }

        $model = $this->normalizeModel($this->geminiModel);
        $context = $this->buildAssistantContext($baseUrl, $isAuthenticated, $isAdmin);

        $payload = [
            'systemInstruction' => [
                'parts' => [[
                    'text' => $this->buildSystemInstruction($context),
                ]],
            ],
            'contents' => $this->buildContents($message, $history),
            'generationConfig' => [
                'temperature' => 0.3,
                'maxOutputTokens' => 700,
                'topP' => 0.9,
            ],
        ];

        try {
            $response = $this->httpClient->request(
                'POST',
                sprintf('https://generativelanguage.googleapis.com/v1beta/models/%s:generateContent', rawurlencode($model)),
                [
                    'query' => ['key' => $apiKey],
                    'json' => $payload,
                    'timeout' => 20,
                ]
            );

            $statusCode = $response->getStatusCode();
            $decoded = $response->toArray(false);

            if ($statusCode >= 400) {
                $errorMessage = $this->extractGeminiError($decoded);
                $this->logger->error('Gemini assistant request failed.', [
                    'status_code' => $statusCode,
                    'error' => $errorMessage,
                ]);

                throw new \RuntimeException($errorMessage !== '' ? $errorMessage : 'Assistant provider returned an error.');
            }

            $reply = $this->extractReply($decoded);
            if ($reply === '') {
                return 'I could not generate an answer right now. Please try again.';
            }

            return $reply;
        } catch (\Throwable $exception) {
            $this->logger->error('Assistant request exception.', [
                'exception_class' => $exception::class,
                'message' => $exception->getMessage(),
            ]);

            throw new \RuntimeException('Assistant is temporarily unavailable. Please retry in a moment.');
        }
    }

    private function normalizeModel(?string $model): string
    {
        $normalized = trim((string) $model);
        if ($normalized === '') {
            return self::DEFAULT_MODEL;
        }

        if (str_starts_with($normalized, 'models/')) {
            $normalized = substr($normalized, 7);
        }

        return $normalized !== '' ? $normalized : self::DEFAULT_MODEL;
    }

    /**
     * @param array<string, mixed> $context
     */
    private function buildSystemInstruction(array $context): string
    {
        $contextJson = json_encode($context, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        if (!is_string($contextJson)) {
            $contextJson = '{}';
        }

        return <<<TXT
You are TravelXP Assistant for the TravelXP web app.
Your goals:
1) Help users navigate the app with exact links and steps.
2) Answer questions about available public catalog items (properties, offers, services, trips).
3) Be concise and practical.

Privacy and safety rules:
- Never reveal or infer personal/sensitive data.
- Do not provide user emails, wallets, budgets, bookings, payment history, or profile details.
- If asked about private account data, refuse and direct the user to Profile, Budgets, Bookings, or Payments pages.
- Use ONLY the CONTEXT_JSON data below for catalog facts.
- If a fact is missing in CONTEXT_JSON, say you are unsure and point to the relevant page.

Navigation rules:
- Prefer absolute links from the app base URL.
- Mention access requirements (public/user/admin) when relevant.
- This app runs locally; links should stay in this app.

Tone rules:
- Friendly, direct, actionable.
- No markdown tables.

CONTEXT_JSON:
$contextJson
TXT;
    }

    /**
     * @param list<array{role:mixed,content:mixed}> $history
     *
     * @return list<array{role:string,parts:list<array{text:string}>}>
     */
    private function buildContents(string $message, array $history): array
    {
        $contents = [];

        foreach (array_slice($history, -10) as $item) {
            if (!is_array($item)) {
                continue;
            }

            $role = (string) ($item['role'] ?? '');
            $content = trim((string) ($item['content'] ?? ''));
            if ($content === '') {
                continue;
            }

            $geminiRole = $role === 'assistant' ? 'model' : 'user';
            if (!in_array($geminiRole, ['user', 'model'], true)) {
                continue;
            }

            $contents[] = [
                'role' => $geminiRole,
                'parts' => [[
                    'text' => mb_substr($content, 0, 1200),
                ]],
            ];
        }

        $contents[] = [
            'role' => 'user',
            'parts' => [[
                'text' => mb_substr(trim($message), 0, 1200),
            ]],
        ];

        return $contents;
    }

    /**
     * @return array<string, mixed>
     */
    private function buildAssistantContext(string $baseUrl, bool $isAuthenticated, bool $isAdmin): array
    {
        $normalizedBaseUrl = rtrim($baseUrl, '/');
        if ($normalizedBaseUrl === '') {
            $normalizedBaseUrl = 'http://127.0.0.1:8000';
        }

        return [
            'app' => [
                'name' => 'TravelXP',
                'base_url' => $normalizedBaseUrl,
                'current_user' => [
                    'authenticated' => $isAuthenticated,
                    'is_admin' => $isAdmin,
                ],
            ],
            'navigation' => $this->buildNavigation($normalizedBaseUrl),
            'public_catalog' => $this->getPublicCatalogSnapshot(),
        ];
    }

    /**
     * @return list<array{label:string,url:string,access:string,purpose:string}>
     */
    private function buildNavigation(string $baseUrl): array
    {
        return [
            ['label' => 'Home', 'url' => $baseUrl.'/', 'access' => 'public', 'purpose' => 'Main landing page and quick overview.'],
            ['label' => 'Login', 'url' => $baseUrl.'/login', 'access' => 'public', 'purpose' => 'Sign in to access wallet, budgets, and profile.'],
            ['label' => 'Register', 'url' => $baseUrl.'/register', 'access' => 'public', 'purpose' => 'Create a new account.'],
            ['label' => 'Properties', 'url' => $baseUrl.'/properties', 'access' => 'public', 'purpose' => 'Browse available stays and property details.'],
            ['label' => 'Offers', 'url' => $baseUrl.'/offers', 'access' => 'public', 'purpose' => 'See active discounts and promo windows.'],
            ['label' => 'Services', 'url' => $baseUrl.'/services', 'access' => 'public', 'purpose' => 'Browse service providers and pricing.'],
            ['label' => 'Trips', 'url' => $baseUrl.'/trips', 'access' => 'public', 'purpose' => 'Explore trips and join/leave when logged in.'],
            ['label' => 'Activities', 'url' => $baseUrl.'/activities', 'access' => 'public', 'purpose' => 'Explore activities and join/leave when logged in.'],
            ['label' => 'Bookings', 'url' => $baseUrl.'/bookings', 'access' => 'user', 'purpose' => 'Manage your bookings.'],
            ['label' => 'Budgets', 'url' => $baseUrl.'/budgets', 'access' => 'user', 'purpose' => 'Manage travel budgets and expenses.'],
            ['label' => 'Payments', 'url' => $baseUrl.'/payments/history', 'access' => 'user', 'purpose' => 'Wallet recharge and payment history.'],
            ['label' => 'Profile', 'url' => $baseUrl.'/profile', 'access' => 'user', 'purpose' => 'Profile settings and 2FA management.'],
            ['label' => 'Admin Menu', 'url' => $baseUrl.'/admin/users/', 'access' => 'admin', 'purpose' => 'Admin area for users and app resources.'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function getPublicCatalogSnapshot(): array
    {
        return $this->cache->get('assistant_public_catalog_v1', function (ItemInterface $item): array {
            $item->expiresAfter(45);

            $today = new \DateTimeImmutable('today');

            $properties = $this->propertyRepository->findBy(['isActive' => true], ['updatedAt' => 'DESC'], 10);
            $services = $this->serviceRepository->findBy(['isAvailable' => true], ['updatedAt' => 'DESC'], 10);

            $offers = $this->offerRepository->createQueryBuilder('o')
                ->leftJoin('o.property', 'p')
                ->addSelect('p')
                ->andWhere('o.isActive = :active')
                ->andWhere('o.startDate <= :today')
                ->andWhere('o.endDate >= :today')
                ->setParameter('active', true)
                ->setParameter('today', $today)
                ->orderBy('o.discountPercentage', 'DESC')
                ->addOrderBy('o.id', 'DESC')
                ->setMaxResults(10)
                ->getQuery()
                ->getResult();

            $trips = $this->tripRepository->createQueryBuilder('t')
                ->andWhere('t.status != :cancelled')
                ->andWhere('t.endDate >= :today')
                ->setParameter('cancelled', 'CANCELLED')
                ->setParameter('today', $today)
                ->orderBy('t.startDate', 'ASC')
                ->addOrderBy('t.id', 'DESC')
                ->setMaxResults(10)
                ->getQuery()
                ->getResult();

            $activeOfferCount = (int) $this->offerRepository->createQueryBuilder('o')
                ->select('COUNT(o.id)')
                ->andWhere('o.isActive = :active')
                ->andWhere('o.startDate <= :today')
                ->andWhere('o.endDate >= :today')
                ->setParameter('active', true)
                ->setParameter('today', $today)
                ->getQuery()
                ->getSingleScalarResult();

            $upcomingTripCount = (int) $this->tripRepository->createQueryBuilder('t')
                ->select('COUNT(t.id)')
                ->andWhere('t.status != :cancelled')
                ->andWhere('t.endDate >= :today')
                ->setParameter('cancelled', 'CANCELLED')
                ->setParameter('today', $today)
                ->getQuery()
                ->getSingleScalarResult();

            return [
                'counts' => [
                    'active_properties' => (int) $this->propertyRepository->count(['isActive' => true]),
                    'active_offers' => $activeOfferCount,
                    'available_services' => (int) $this->serviceRepository->count(['isAvailable' => true]),
                    'upcoming_trips' => $upcomingTripCount,
                ],
                'properties' => array_map(
                    static fn (Property $property): array => [
                        'id' => $property->getId(),
                        'title' => (string) $property->getTitle(),
                        'type' => (string) $property->getPropertyType(),
                        'city' => (string) $property->getCity(),
                        'country' => (string) $property->getCountry(),
                        'price_per_night_usd' => (float) $property->getPricePerNight(),
                        'max_guests' => $property->getMaxGuests(),
                    ],
                    $properties
                ),
                'offers' => array_map(
                    static fn (Offer $offer): array => [
                        'id' => $offer->getId(),
                        'title' => (string) $offer->getTitle(),
                        'discount_percentage' => (float) $offer->getDiscountPercentage(),
                        'property' => (string) ($offer->getProperty()?->getTitle() ?? ''),
                        'valid_from' => $offer->getStartDate()?->format('Y-m-d'),
                        'valid_to' => $offer->getEndDate()?->format('Y-m-d'),
                    ],
                    array_filter($offers, static fn (mixed $offer): bool => $offer instanceof Offer)
                ),
                'services' => array_map(
                    static fn (TravelService $service): array => [
                        'id' => $service->getId(),
                        'provider' => (string) $service->getProviderName(),
                        'type' => (string) $service->getServiceType(),
                        'price_usd' => (float) $service->getPrice(),
                        'eco_friendly' => $service->isEcoFriendly(),
                    ],
                    $services
                ),
                'trips' => array_map(
                    static fn (Trip $trip): array => [
                        'id' => $trip->getId(),
                        'name' => (string) $trip->getTripName(),
                        'origin' => (string) ($trip->getOrigin() ?? ''),
                        'destination' => (string) ($trip->getDestination() ?? ''),
                        'status' => $trip->getStatus(),
                        'start_date' => $trip->getStartDate()?->format('Y-m-d'),
                        'end_date' => $trip->getEndDate()?->format('Y-m-d'),
                    ],
                    array_filter($trips, static fn (mixed $trip): bool => $trip instanceof Trip)
                ),
            ];
        });
    }

    /**
     * @param array<string, mixed> $response
     */
    private function extractReply(array $response): string
    {
        $candidates = $response['candidates'] ?? [];
        if (!is_array($candidates)) {
            return '';
        }

        foreach ($candidates as $candidate) {
            if (!is_array($candidate)) {
                continue;
            }

            $parts = $candidate['content']['parts'] ?? null;
            if (!is_array($parts)) {
                continue;
            }

            $chunks = [];
            foreach ($parts as $part) {
                if (!is_array($part)) {
                    continue;
                }

                $text = trim((string) ($part['text'] ?? ''));
                if ($text !== '') {
                    $chunks[] = $text;
                }
            }

            if ($chunks !== []) {
                return trim(implode("\n", $chunks));
            }
        }

        return '';
    }

    /**
     * @param array<string, mixed> $response
     */
    private function extractGeminiError(array $response): string
    {
        $error = $response['error'] ?? null;
        if (!is_array($error)) {
            return '';
        }

        return trim((string) ($error['message'] ?? ''));
    }
}
