<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class IpApiLocationResolver
{
    private const ENDPOINT = 'http://ip-api.com/json/';

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * @return array{country: ?string, region: ?string, city: ?string, isp: ?string, latitude: ?float, longitude: ?float}|null
     */
    public function resolve(?string $ipAddress): ?array
    {
        $ipAddress = trim((string) $ipAddress);
        if ('' === $ipAddress || !$this->isPublicIp($ipAddress)) {
            return null;
        }

        try {
            $response = $this->httpClient->request('GET', self::ENDPOINT.rawurlencode($ipAddress), [
                'query' => [
                    'fields' => 'status,message,country,regionName,city,isp,lat,lon',
                ],
                'timeout' => 2.5,
            ]);
            $data = $response->toArray(false);

            if (($data['status'] ?? '') !== 'success') {
                return null;
            }

            return [
                'country' => $this->normalizeNullableString($data['country'] ?? null),
                'region' => $this->normalizeNullableString($data['regionName'] ?? null),
                'city' => $this->normalizeNullableString($data['city'] ?? null),
                'isp' => $this->normalizeNullableString($data['isp'] ?? null),
                'latitude' => isset($data['lat']) && is_numeric($data['lat']) ? (float) $data['lat'] : null,
                'longitude' => isset($data['lon']) && is_numeric($data['lon']) ? (float) $data['lon'] : null,
            ];
        } catch (\Throwable $exception) {
            $this->logger->warning('IP geolocation lookup failed.', [
                'ip' => $ipAddress,
                'message' => $exception->getMessage(),
            ]);

            return null;
        }
    }

    private function isPublicIp(string $ipAddress): bool
    {
        return false !== filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
    }

    private function normalizeNullableString(mixed $value): ?string
    {
        if (!is_scalar($value)) {
            return null;
        }

        $normalized = trim((string) $value);

        return '' === $normalized ? null : $normalized;
    }
}
