<?php

namespace App\Service;

use App\Entity\Property;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GeoapifyService
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly CacheInterface $cache,
        private readonly string $autocompleteApiKey,
        private readonly string $placesApiKey,
        private readonly string $routingApiKey,
        private readonly string $autocompleteUrl,
        private readonly string $placesUrl,
        private readonly string $routingUrl,
    ) {
    }

    public function hasAutocompleteApiKey(): bool
    {
        return '' !== trim($this->autocompleteApiKey);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function autocomplete(string $query, int $limit = 6): array
    {
        $query = trim($query);
        if (!$this->hasAutocompleteApiKey() || '' === $query) {
            return [];
        }

        $payload = $this->requestJson(
            $this->autocompleteUrl,
            [
                'text' => $query,
                'limit' => max(1, min(10, $limit)),
                'type' => 'street',
                'format' => 'json',
            ],
            1800,
            'autocomplete',
            $this->autocompleteApiKey
        );

        $features = $payload['results'] ?? [];
        if (!is_array($features)) {
            return [];
        }

        $suggestions = [];
        foreach ($features as $feature) {
            if (!is_array($feature)) {
                continue;
            }

            $lat = isset($feature['lat']) ? (float) $feature['lat'] : null;
            $lon = isset($feature['lon']) ? (float) $feature['lon'] : null;
            if (null === $lat || null === $lon) {
                continue;
            }

            $suggestions[] = [
                'formatted' => (string) ($feature['formatted'] ?? ''),
                'address' => (string) ($feature['address_line1'] ?? $feature['formatted'] ?? ''),
                'city' => (string) ($feature['city'] ?? $feature['county'] ?? ''),
                'country' => (string) ($feature['country'] ?? ''),
                'postalCode' => (string) ($feature['postcode'] ?? ''),
                'latitude' => $lat,
                'longitude' => $lon,
            ];
        }

        return $suggestions;
    }

    public function geocodeProperty(Property $property): void
    {
        if (!$this->hasAutocompleteApiKey()) {
            return;
        }

        $query = trim(implode(', ', array_filter([
            $property->getAddress(),
            $property->getCity(),
            $property->getCountry(),
        ], static fn (?string $value): bool => null !== $value && '' !== trim($value))));

        if ('' === $query) {
            $property->setLatitude(null);
            $property->setLongitude(null);

            return;
        }

        $results = $this->autocomplete($query, 1);
        if ([] === $results) {
            return;
        }

        $best = $results[0];
        $property
            ->setLatitude(isset($best['latitude']) ? (float) $best['latitude'] : null)
            ->setLongitude(isset($best['longitude']) ? (float) $best['longitude'] : null);

        if ('' !== trim((string) ($best['address'] ?? ''))) {
            $property->setAddress((string) $best['address']);
        }
        if ('' !== trim((string) ($best['city'] ?? ''))) {
            $property->setCity((string) $best['city']);
        }
        if ('' !== trim((string) ($best['country'] ?? ''))) {
            $property->setCountry((string) $best['country']);
        }
    }

    /**
     * @return array<string, mixed>|null
     */
    public function reverse(float $latitude, float $longitude): ?array
    {
        if (!$this->hasAutocompleteApiKey()) {
            return null;
        }

        $reverseUrl = str_replace('/autocomplete', '/reverse', $this->autocompleteUrl);
        $payload = $this->requestJson(
            $reverseUrl,
            [
                'lat' => $latitude,
                'lon' => $longitude,
                'format' => 'json',
            ],
            1800,
            'reverse',
            $this->autocompleteApiKey
        );

        $results = $payload['results'] ?? [];
        if (!is_array($results) || [] === $results || !is_array($results[0] ?? null)) {
            return null;
        }

        $item = $results[0];

        return [
            'address' => (string) ($item['address_line1'] ?? $item['formatted'] ?? ''),
            'city' => (string) ($item['city'] ?? $item['county'] ?? ''),
            'country' => (string) ($item['country'] ?? ''),
            'postalCode' => (string) ($item['postcode'] ?? ''),
            'latitude' => isset($item['lat']) ? (float) $item['lat'] : $latitude,
            'longitude' => isset($item['lon']) ? (float) $item['lon'] : $longitude,
            'formatted' => (string) ($item['formatted'] ?? ''),
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function nearbyPlaces(float $latitude, float $longitude, int $radiusMeters = 4000): array
    {
        if ('' === trim($this->placesApiKey)) {
            return [];
        }

        $payload = $this->requestJson(
            $this->placesUrl,
            [
                'categories' => 'tourism.sights,catering.restaurant,entertainment',
                'filter' => sprintf('circle:%s,%s,%d', $longitude, $latitude, max(500, min(10000, $radiusMeters))),
                'bias' => sprintf('proximity:%s,%s', $longitude, $latitude),
                'limit' => 30,
                'lang' => 'en',
            ],
            1800,
            'places',
            $this->placesApiKey
        );

        $features = $payload['features'] ?? [];
        if (!is_array($features)) {
            return [];
        }

        $results = [];
        foreach ($features as $feature) {
            if (!is_array($feature)) {
                continue;
            }

            $properties = is_array($feature['properties'] ?? null) ? $feature['properties'] : [];
            $geometry = is_array($feature['geometry'] ?? null) ? $feature['geometry'] : [];
            $coordinates = $geometry['coordinates'] ?? null;

            if (!is_array($coordinates) || count($coordinates) < 2) {
                continue;
            }

            $results[] = [
                'name' => (string) ($properties['name'] ?? 'Unnamed place'),
                'category' => (string) ($properties['categories'][0] ?? ''),
                'latitude' => (float) $coordinates[1],
                'longitude' => (float) $coordinates[0],
                'address' => (string) ($properties['formatted'] ?? ''),
            ];
        }

        return $results;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function route(float $fromLatitude, float $fromLongitude, float $toLatitude, float $toLongitude): ?array
    {
        if ('' === trim($this->routingApiKey)) {
            return null;
        }

        $payload = $this->requestJson(
            $this->routingUrl,
            [
                'waypoints' => sprintf('%s,%s|%s,%s', $fromLatitude, $fromLongitude, $toLatitude, $toLongitude),
                'mode' => 'drive',
                'details' => 'instruction_details',
            ],
            900,
            'route',
            $this->routingApiKey
        );

        $features = $payload['features'] ?? [];
        if (!is_array($features) || [] === $features || !is_array($features[0] ?? null)) {
            return null;
        }

        $feature = $features[0];
        $properties = is_array($feature['properties'] ?? null) ? $feature['properties'] : [];
        $distance = isset($properties['distance']) ? (float) $properties['distance'] : null;
        $timeSeconds = isset($properties['time']) ? (float) $properties['time'] : null;

        if (null === $distance || null === $timeSeconds) {
            return null;
        }

        return [
            'distanceMeters' => $distance,
            'durationSeconds' => $timeSeconds,
            'distanceKm' => round($distance / 1000, 1),
            'durationMinutes' => (int) round($timeSeconds / 60),
            'geometry' => $feature['geometry'] ?? null,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function requestJson(string $url, array $query, int $ttlSeconds, string $prefix, string $apiKey): array
    {
        if ('' === trim($apiKey)) {
            return [];
        }

        $query['apiKey'] = $apiKey;
        ksort($query);

        $cacheKey = sprintf('geoapify_%s_%s', $prefix, md5($url . ':' . json_encode($query, JSON_THROW_ON_ERROR)));

        return $this->cache->get($cacheKey, function (ItemInterface $item) use ($url, $query, $ttlSeconds): array {
            $item->expiresAfter($ttlSeconds);

            try {
                $response = $this->httpClient->request('GET', $url, ['query' => $query]);
                if (200 !== $response->getStatusCode()) {
                    return [];
                }

                $json = $response->toArray(false);

                return is_array($json) ? $json : [];
            } catch (ExceptionInterface) {
                return [];
            }
        });
    }
}
