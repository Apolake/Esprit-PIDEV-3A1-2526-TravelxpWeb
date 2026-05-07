<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class CurrencyConverterService
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly string $exchangeRateApiKey,
        private readonly string $exchangeRateApiUrl,
    ) {
    }

    /**
     * @var array<string, float>
     */
    private const FALLBACK_USD_BASE_RATES = [
        'USD' => 1.0,
        'EUR' => 0.92,
        'GBP' => 0.79,
        'TND' => 3.12,
        'CAD' => 1.38,
        'JPY' => 154.20,
        'MAD' => 9.98,
    ];

    /**
     * @var array<string, string>
     */
    private const LABELS = [
        'USD' => 'USD ($)',
        'EUR' => 'EUR (€)',
        'GBP' => 'GBP (£)',
        'TND' => 'TND (DT)',
        'CAD' => 'CAD (C$)',
        'JPY' => 'JPY (¥)',
        'MAD' => 'MAD (DH)',
    ];

    public function normalizeCurrency(?string $currency): string
    {
        $normalized = strtoupper(trim((string) $currency));

        if (preg_match('/^[A-Z]{3}$/', $normalized) === 1) {
            return $normalized;
        }

        return 'USD';
    }

    public function convert(float $amount, string $fromCurrency, string $toCurrency): float
    {
        return $this->getConversionData($amount, $fromCurrency, $toCurrency)['convertedAmount'];
    }

    public function formatAmount(float $amount, string $currency): string
    {
        $normalized = $this->normalizeCurrency($currency);

        return sprintf('%s%s', $this->getSymbol($normalized), number_format($amount, 2, '.', ','));
    }

    /**
     * @return array<string, string>
     */
    public function getSupportedCurrenciesForFormChoices(): array
    {
        return array_flip(self::LABELS);
    }

    public function getSymbol(string $currency): string
    {
        return match ($this->normalizeCurrency($currency)) {
            'EUR' => '€',
            'GBP' => '£',
            'TND' => 'DT ',
            'CAD' => 'C$',
            'JPY' => '¥',
            'MAD' => 'DH ',
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
     * @return array<string, string>
     */
    public function getSupportedCurrenciesForFormChoices(): array
    {
        $choices = [];
        foreach (self::LABELS as $code => $label) {
            $choices[$label] = $code;
        }

        return $choices;
    }

    /**
     * @return array{from: string, to: string, amount: float, convertedAmount: float, rate: float, provider: string, fallback: bool}
     */
    public function getConversionData(float $amount, string $fromCurrency, string $toCurrency): array
    {
        $from = $this->normalizeCurrency($fromCurrency);
        $to = $this->normalizeCurrency($toCurrency);

        if ($from === $to) {
            return [
                'from' => $from,
                'to' => $to,
                'amount' => $amount,
                'convertedAmount' => $amount,
                'rate' => 1.0,
                'provider' => 'Frankfurter',
                'fallback' => false,
            ];
        }

        $exchangeRateResult = $this->fetchExchangeRateApiRate($from, $to);
        if ($exchangeRateResult !== null) {
            return [
                'from' => $from,
                'to' => $to,
                'amount' => $amount,
                'convertedAmount' => $amount * $exchangeRateResult['rate'],
                'rate' => $exchangeRateResult['rate'],
                'provider' => $exchangeRateResult['provider'],
                'fallback' => false,
            ];
        }

        try {
            $response = $this->httpClient->request('GET', 'https://api.frankfurter.dev/v1/latest', [
                'timeout' => 8,
                'query' => [
                    'base' => $from,
                    'symbols' => $to,
                ],
            ]);

            $payload = $response->toArray(false);
            $rate = (float) ($payload['rates'][$to] ?? 0.0);

            if ($rate > 0) {
                return [
                    'from' => $from,
                    'to' => $to,
                    'amount' => $amount,
                    'convertedAmount' => $amount * $rate,
                    'rate' => $rate,
                    'provider' => 'Frankfurter',
                    'fallback' => false,
                ];
            }
        } catch (\Throwable) {
            // Fall back to bundled reference rates when the live API is unreachable.
        }

        $fallbackRate = $this->getFallbackRate($from, $to);

        return [
            'from' => $from,
            'to' => $to,
            'amount' => $amount,
            'convertedAmount' => $amount * $fallbackRate,
            'rate' => $fallbackRate,
            'provider' => 'TravelXP fallback rates',
            'fallback' => true,
        ];
    }

    private function getFallbackRate(string $fromCurrency, string $toCurrency): float
    {
        $from = $this->normalizeCurrency($fromCurrency);
        $to = $this->normalizeCurrency($toCurrency);

        if ($from === $to) {
            return 1.0;
        }

        $fromRate = self::FALLBACK_USD_BASE_RATES[$from] ?? self::FALLBACK_USD_BASE_RATES['USD'];
        $toRate = self::FALLBACK_USD_BASE_RATES[$to] ?? self::FALLBACK_USD_BASE_RATES['USD'];
        $amountInUsd = 1 / $fromRate;

        return $amountInUsd * $toRate;
    }

    /**
     * @return array{rate: float, provider: string}|null
     */
    private function fetchExchangeRateApiRate(string $fromCurrency, string $toCurrency): ?array
    {
        if ('' === trim($this->exchangeRateApiKey) || '' === trim($this->exchangeRateApiUrl)) {
            return null;
        }

        $url = str_replace(
            ['{key}', '{base}'],
            [$this->exchangeRateApiKey, $fromCurrency],
            $this->exchangeRateApiUrl
        );

        try {
            $response = $this->httpClient->request('GET', $url, [
                'timeout' => 8,
            ]);

            $payload = $response->toArray(false);
            $rates = $payload['conversion_rates'] ?? null;
            if (!is_array($rates)) {
                return null;
            }

            $rate = isset($rates[$toCurrency]) ? (float) $rates[$toCurrency] : 0.0;
            if ($rate <= 0) {
                return null;
            }

            return [
                'rate' => $rate,
                'provider' => 'ExchangeRate API',
            ];
        } catch (\Throwable) {
            return null;
        }
    }
}
