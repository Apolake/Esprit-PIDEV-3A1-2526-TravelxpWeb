<?php

namespace App\Tests\Service;

use App\Service\CurrencyConverterService;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class CurrencyConverterServiceTest extends TestCase
{
    private $httpClient;
    private CurrencyConverterService $currencyConverter;

    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(HttpClientInterface::class);
        $this->currencyConverter = new CurrencyConverterService(
            $this->httpClient,
            'api_key',
            'url'
        );
    }

    public function testNormalizeCurrency(): void
    {
        $this->assertEquals('EUR', $this->currencyConverter->normalizeCurrency('eur'));
        $this->assertEquals('USD', $this->currencyConverter->normalizeCurrency('usd '));
        $this->assertEquals('GBP', $this->currencyConverter->normalizeCurrency('GBP'));
        
        // Invalid falls back to USD
        $this->assertEquals('USD', $this->currencyConverter->normalizeCurrency('123'));
        $this->assertEquals('USD', $this->currencyConverter->normalizeCurrency(null));
    }

    public function testGetSymbol(): void
    {
        $this->assertEquals('€', $this->currencyConverter->getSymbol('EUR'));
        $this->assertEquals('$', $this->currencyConverter->getSymbol('USD'));
        $this->assertEquals('£', $this->currencyConverter->getSymbol('GBP'));
        $this->assertEquals('DT ', $this->currencyConverter->getSymbol('TND'));
    }

    public function testFormatAmount(): void
    {
        $this->assertEquals('€150.50', $this->currencyConverter->formatAmount(150.5, 'EUR'));
        $this->assertEquals('$1,000.00', $this->currencyConverter->formatAmount(1000, 'USD'));
    }

    public function testConvertUsesFallbackOnHttpError(): void
    {
        // Mock response causing an exception or acting as failure would trigger fallback internally
        // In the provided code, convert() calls getConversionData(). 
        // We'll test assuming getConversionData falls back.
        
        $responseMock = $this->createMock(ResponseInterface::class);
        $responseMock->method('getStatusCode')->willReturn(500);

        $this->httpClient->method('request')->willReturn($responseMock);

        // Based on FALLBACK_USD_BASE_RATES: EUR -> USD = 1 / 0.92, USD -> EUR = 0.92
        // Amount: 100 USD to EUR => 100 * 0.92 = 92
        $result = $this->currencyConverter->convert(100, 'USD', 'EUR');
        
        $this->assertEquals(92.0, $result);
    }
}
