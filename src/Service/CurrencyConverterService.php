<?php

namespace App\Service;

class CurrencyConverterService
{
    /**
     * @var array<string, float>
     */
    private const USD_BASE_RATES = [
        'USD' => 1.0,
        'EUR' => 0.92,
        'GBP' => 0.79,
        'TND' => 3.12,
    ];

    /**
     * @var array<string, string>
     */
    private const LABELS = [
        'USD' => 'USD ($)',
        'EUR' => 'EUR (€)',
        'GBP' => 'GBP (£)',
        'TND' => 'TND (DT)',
    ];

    public function normalizeCurrency(?string $currency): string
    {
        $normalized = strtoupper(trim((string) $currency));

        return array_key_exists($normalized, self::USD_BASE_RATES) ? $normalized : 'USD';
    }

    public function convert(float $amount, string $fromCurrency, string $toCurrency): float
    {
        $from = $this->normalizeCurrency($fromCurrency);
        $to = $this->normalizeCurrency($toCurrency);

        if ($from === $to) {
            return $amount;
        }

        $amountInUsd = $amount / self::USD_BASE_RATES[$from];

        return $amountInUsd * self::USD_BASE_RATES[$to];
    }

    public function formatAmount(float $amount, string $currency): string
    {
        $normalized = $this->normalizeCurrency($currency);

        return sprintf('%s%s', $this->getSymbol($normalized), number_format($amount, 2, '.', ','));
    }

    public function getSymbol(string $currency): string
    {
        return match ($this->normalizeCurrency($currency)) {
            'EUR' => '€',
            'GBP' => '£',
            'TND' => 'DT ',
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
}
