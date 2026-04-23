<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class GeocodingService
{
    private const ALLOWED_PLACE_CODES = [
        'PPLC', 'PPLA', 'PPLA2', 'PPLA3', 'PPLA4', 'PPL', 'PPLX', 'PPLG',
    ];

    public function __construct(
        private readonly HttpClientInterface $httpClient,
    ) {
    }

    /**
     * @return array{lat: float, lng: float}|null
     */
    public function geocode(string $query): ?array
    {
        $query = trim($query);
        if ($query === '') {
            return null;
        }

        $suggestions = $this->suggestPlaces($query, 1);
        if ($suggestions !== []) {
            return [
                'lat' => (float) $suggestions[0]['lat'],
                'lng' => (float) $suggestions[0]['lng'],
            ];
        }

        try {
            $response = $this->httpClient->request('GET', 'https://nominatim.openstreetmap.org/search', [
                'query' => [
                    'q' => $query,
                    'format' => 'jsonv2',
                    'limit' => 1,
                    'addressdetails' => 0,
                ],
                'headers' => [
                    'Accept' => 'application/json',
                    'User-Agent' => 'TravelXP/1.0 (Symfony)',
                ],
                'timeout' => 5,
            ]);

            if ($response->getStatusCode() !== 200) {
                return null;
            }

            $payload = $response->toArray(false);
            if (!is_array($payload) || $payload === []) {
                return null;
            }

            $first = $payload[0] ?? null;
            if (!is_array($first) || !isset($first['lat'], $first['lon'])) {
                return null;
            }

            $lat = (float) $first['lat'];
            $lng = (float) $first['lon'];
            if ($lat < -90 || $lat > 90 || $lng < -180 || $lng > 180) {
                return null;
            }

            return ['lat' => $lat, 'lng' => $lng];
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * @return list<array{value: string, label: string, lat: float, lng: float}>
     */
    public function suggestPlaces(string $query, int $limit = 8): array
    {
        $query = trim($query);
        if ($query === '') {
            return [];
        }

        $limit = max(1, min(12, $limit));
        $providers = [
            $this->suggestFromPhoton($query, $limit),
            $this->suggestFromOpenMeteo($query, $limit),
            $this->suggestFromNominatim($query, $limit),
        ];

        foreach ($providers as $results) {
            if ($results !== []) {
                return $results;
            }
        }

        return [];
    }

    /**
     * @return list<array{value: string, label: string, lat: float, lng: float}>
     */
    private function suggestFromPhoton(string $query, int $limit): array
    {
        try {
            $response = $this->httpClient->request('GET', 'https://photon.komoot.io/api/', [
                'query' => [
                    'q' => $query,
                    'lang' => 'en',
                    'limit' => $limit,
                    'osm_tag' => 'place:city',
                ],
                'headers' => [
                    'Accept' => 'application/json',
                    'User-Agent' => 'TravelXP/1.0 (Symfony)',
                ],
                'timeout' => 5,
            ]);

            if ($response->getStatusCode() !== 200) {
                return [];
            }

            $payload = $response->toArray(false);
            $features = is_array($payload['features'] ?? null) ? $payload['features'] : [];
            if ($features === []) {
                return [];
            }

            $results = [];
            $queryLower = mb_strtolower($query);
            foreach ($features as $feature) {
                if (!is_array($feature)) {
                    continue;
                }
                $geometry = is_array($feature['geometry'] ?? null) ? $feature['geometry'] : [];
                $coords = is_array($geometry['coordinates'] ?? null) ? $geometry['coordinates'] : [];
                if (count($coords) < 2) {
                    continue;
                }
                $properties = is_array($feature['properties'] ?? null) ? $feature['properties'] : [];

                $lat = (float) ($coords[1] ?? 0);
                $lng = (float) ($coords[0] ?? 0);
                if ($lat < -90 || $lat > 90 || $lng < -180 || $lng > 180) {
                    continue;
                }

                $name = trim((string) ($properties['name'] ?? $properties['city'] ?? ''));
                $city = trim((string) ($properties['city'] ?? $name));
                $state = trim((string) ($properties['state'] ?? ''));
                $country = trim((string) ($properties['country'] ?? ''));

                if ($city === '' || $country === '') {
                    continue;
                }
                if (!str_starts_with(mb_strtolower($city), $queryLower)) {
                    continue;
                }

                $parts = array_values(array_filter([$city, $state, $country], static fn (string $v): bool => $v !== ''));
                $label = implode(', ', $parts);
                $value = implode(', ', [$city, $country]);

                $results[] = [
                    'value' => $value,
                    'label' => $label,
                    'lat' => $lat,
                    'lng' => $lng,
                ];
            }

            return array_values(array_slice($results, 0, $limit));
        } catch (\Throwable) {
            return [];
        }
    }

    /**
     * @return list<array{value: string, label: string, lat: float, lng: float}>
     */
    private function suggestFromOpenMeteo(string $query, int $limit): array
    {
        try {
            $response = $this->httpClient->request('GET', 'https://geocoding-api.open-meteo.com/v1/search', [
                'query' => [
                    'name' => $query,
                    'count' => $limit * 2,
                    'language' => 'en',
                    'format' => 'json',
                ],
                'headers' => [
                    'Accept' => 'application/json',
                    'User-Agent' => 'TravelXP/1.0 (Symfony)',
                ],
                'timeout' => 5,
            ]);

            if ($response->getStatusCode() !== 200) {
                return [];
            }

            $payload = $response->toArray(false);
            $rawResults = is_array($payload['results'] ?? null) ? $payload['results'] : [];
            if ($rawResults === []) {
                return [];
            }

            $needle = mb_strtolower($query);
            $normalized = [];
            foreach ($rawResults as $row) {
                if (!is_array($row) || !isset($row['latitude'], $row['longitude'], $row['name'])) {
                    continue;
                }

                $lat = (float) $row['latitude'];
                $lng = (float) $row['longitude'];
                if ($lat < -90 || $lat > 90 || $lng < -180 || $lng > 180) {
                    continue;
                }

                $name = trim((string) $row['name']);
                $admin1 = trim((string) ($row['admin1'] ?? ''));
                $country = trim((string) ($row['country'] ?? ''));
                $countryCode = strtoupper(trim((string) ($row['country_code'] ?? '')));

                $featureCode = strtoupper(trim((string) ($row['feature_code'] ?? '')));
                $population = (int) ($row['population'] ?? 0);
                if (!in_array($featureCode, self::ALLOWED_PLACE_CODES, true)) {
                    continue;
                }

                $parts = array_values(array_filter([$name, $admin1, $country], static fn (string $v): bool => $v !== ''));
                $label = implode(', ', $parts);
                if ($label === '') {
                    continue;
                }

                // Keep the value standardized for downstream geocoding/map/weather usage.
                $value = implode(', ', array_values(array_filter([$name, $country], static fn (string $v): bool => $v !== '')));
                if ($value === '') {
                    $value = $label;
                }

                $nameLower = mb_strtolower($name);
                $isPrefix = str_starts_with($nameLower, $needle);
                if (mb_strlen($query) <= 2 && !$isPrefix) {
                    continue;
                }
                $prefixScore = $isPrefix ? 1000 : (str_contains($nameLower, $needle) ? 350 : 0);
                $featureScore = match (true) {
                    str_starts_with($featureCode, 'PPLC') => 120,
                    str_starts_with($featureCode, 'PPLA') => 100,
                    str_starts_with($featureCode, 'PPL') => 80,
                    default => 30,
                };
                // Favor larger known places but keep prefix relevance as top signal.
                $populationScore = (int) min(200, max(0, floor(log(max(1, $population), 10) * 25)));

                // Slight boost for common target countries in this app context.
                $countryBoost = in_array($countryCode, ['US', 'LY', 'EG', 'TN', 'NG', 'MA', 'GB', 'FR', 'IT'], true) ? 25 : 0;

                $score = $prefixScore + $featureScore + $populationScore + $countryBoost;
                $normalized[] = [
                    'value' => $value,
                    'label' => $label,
                    'lat' => $lat,
                    'lng' => $lng,
                    '_score' => $score,
                ];
            }

            if ($normalized === []) {
                return [];
            }

            usort($normalized, static fn (array $a, array $b): int => ($b['_score'] <=> $a['_score']) ?: strcmp($a['label'], $b['label']));

            $seen = [];
            $results = [];
            foreach ($normalized as $item) {
                $key = mb_strtolower($item['label']);
                if (isset($seen[$key])) {
                    continue;
                }
                $seen[$key] = true;
                unset($item['_score']);
                $results[] = $item;
                if (count($results) >= $limit) {
                    break;
                }
            }

            return $results;
        } catch (\Throwable) {
            return [];
        }
    }

    /**
     * @return list<array{value: string, label: string, lat: float, lng: float}>
     */
    private function suggestFromNominatim(string $query, int $limit): array
    {
        try {
            $response = $this->httpClient->request('GET', 'https://nominatim.openstreetmap.org/search', [
                'query' => [
                    'q' => $query,
                    'format' => 'jsonv2',
                    'limit' => $limit * 2,
                    'addressdetails' => 1,
                    'featuretype' => 'city',
                    'dedupe' => 1,
                ],
                'headers' => [
                    'Accept' => 'application/json',
                    'User-Agent' => 'TravelXP/1.0 (Symfony)',
                ],
                'timeout' => 5,
            ]);

            if ($response->getStatusCode() !== 200) {
                return [];
            }

            $payload = $response->toArray(false);
            if (!is_array($payload) || $payload === []) {
                return [];
            }

            $queryLower = mb_strtolower($query);
            $results = [];
            foreach ($payload as $row) {
                if (!is_array($row) || !isset($row['lat'], $row['lon'])) {
                    continue;
                }
                $lat = (float) $row['lat'];
                $lng = (float) $row['lon'];
                if ($lat < -90 || $lat > 90 || $lng < -180 || $lng > 180) {
                    continue;
                }

                $address = is_array($row['address'] ?? null) ? $row['address'] : [];
                $city = trim((string) ($address['city'] ?? $address['town'] ?? $address['municipality'] ?? ''));
                $state = trim((string) ($address['state'] ?? $address['region'] ?? ''));
                $country = trim((string) ($address['country'] ?? ''));

                if ($city === '' || $country === '') {
                    continue;
                }
                if (!str_starts_with(mb_strtolower($city), $queryLower) && mb_strlen($query) <= 2) {
                    continue;
                }

                $label = implode(', ', array_values(array_filter([$city, $state, $country], static fn (string $v): bool => $v !== '')));
                $value = implode(', ', [$city, $country]);

                $results[] = [
                    'value' => $value,
                    'label' => $label,
                    'lat' => $lat,
                    'lng' => $lng,
                ];
            }

            return array_slice($results, 0, $limit);
        } catch (\Throwable) {
            return [];
        }
    }
}
