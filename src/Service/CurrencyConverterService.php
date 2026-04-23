<?php

namespace App\Service;

use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CurrencyConverterService
{
    /**
     * @var array<string, float>
     */
    private const USD_BASE_FALLBACK_RATES = [
        'USD' => 1.0,
        'EUR' => 0.92,
        'GBP' => 0.79,
        'TND' => 3.12,
        'EGP' => 48.3,
        'NGN' => 1320.0,
        'AED' => 3.67,
        'SAR' => 3.75,
    ];

    /**
     * @var array<string, string>
     */
    private const LABELS = [
        'USD' => 'USD ($)',
        'EUR' => 'EUR',
        'GBP' => 'GBP',
        'TND' => 'TND (DT)',
        'EGP' => 'EGP',
        'NGN' => 'NGN',
        'AED' => 'AED',
        'SAR' => 'SAR',
    ];

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly CacheInterface $cache,
    ) {
    }

    public function normalizeCurrency(?string $currency): string
    {
        $normalized = strtoupper(trim((string) $currency));

        return array_key_exists($normalized, self::LABELS) ? $normalized : 'USD';
    }

    public function convert(float $amount, string $fromCurrency, string $toCurrency): float
    {
        $from = $this->normalizeCurrency($fromCurrency);
        $to = $this->normalizeCurrency($toCurrency);

        if ($from === $to) {
            return $amount;
        }

        $rates = $this->loadUsdBaseRates();
        $fromRate = $rates[$from] ?? self::USD_BASE_FALLBACK_RATES[$from] ?? 1.0;
        $toRate = $rates[$to] ?? self::USD_BASE_FALLBACK_RATES[$to] ?? 1.0;
        if ($fromRate <= 0.0 || $toRate <= 0.0) {
            return $amount;
        }

        $amountInUsd = $amount / $fromRate;

        return $amountInUsd * $toRate;
    }

    public function formatAmount(float $amount, string $currency): string
    {
        $normalized = $this->normalizeCurrency($currency);

        return sprintf('%s%s', $this->getSymbol($normalized), number_format($amount, 2, '.', ','));
    }

    public function getSymbol(string $currency): string
    {
        return match ($this->normalizeCurrency($currency)) {
            'EUR' => 'EUR ',
            'GBP' => 'GBP ',
            'TND' => 'DT ',
            'EGP' => 'EGP ',
            'NGN' => 'NGN ',
            'AED' => 'AED ',
            'SAR' => 'SAR ',
            default => '$',
        };
    }

    /**
     * @return array<string, string>
     */
    public function getSupportedCurrenciesWithLabels(): array
    {
        return self::LABELS;
    }

    /**
     * @return array<string, float>
     */
    private function loadUsdBaseRates(): array
    {
        try {
            return $this->cache->get('currency.usd_base_rates.v1', function (ItemInterface $item): array {
                $item->expiresAfter(21600);

                return $this->fetchLiveUsdBaseRates();
            });
        } catch (\Throwable) {
            return self::USD_BASE_FALLBACK_RATES;
        }
    }

    /**
     * @return array<string, float>
     */
    private function fetchLiveUsdBaseRates(): array
    {
        $response = $this->httpClient->request('GET', 'https://open.er-api.com/v6/latest/USD', [
            'timeout' => 4,
            'proxy' => null,
            'no_proxy' => '*',
        ]);
        if ($response->getStatusCode() !== 200) {
            return self::USD_BASE_FALLBACK_RATES;
        }

        $payload = $response->toArray(false);
        $rawRates = is_array($payload['rates'] ?? null) ? $payload['rates'] : [];
        if ($rawRates === []) {
            return self::USD_BASE_FALLBACK_RATES;
        }

        $rates = self::USD_BASE_FALLBACK_RATES;
        foreach (array_keys(self::LABELS) as $code) {
            $value = $rawRates[$code] ?? null;
            if (is_numeric($value) && (float) $value > 0.0) {
                $rates[$code] = (float) $value;
            }
        }

        return $rates;
    }
}
