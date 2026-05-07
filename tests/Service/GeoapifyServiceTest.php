<?php

namespace App\Tests\Service;

use App\Entity\Property;
use App\Service\GeoapifyService;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class GeoapifyServiceTest extends TestCase
{
    private $httpClient;
    private $cache;
    private GeoapifyService $geoapifyService;

    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(HttpClientInterface::class);
        $this->cache = $this->createMock(CacheInterface::class);
    }

    private function createService(string $apiKey = 'test_key'): void
    {
        $this->geoapifyService = new GeoapifyService(
            $this->httpClient,
            $this->cache,
            $apiKey, // autocompleteApiKey
            $apiKey, // placesApiKey
            $apiKey, // routingApiKey
            'https://api.geoapify.com/v1/geocode/autocomplete',
            'https://api.geoapify.com/v2/places',
            'https://api.geoapify.com/v1/routing'
        );
    }

    public function testGeocodePropertyUpdatesCoordinates(): void
    {
        $this->createService();

        $property = new Property();
        $property->setAddress('10 Rue de Rivoli');
        $property->setCity('Paris');
        $property->setCountry('France');

        // Mock Cache to just execute the callback
        $this->cache->expects($this->once())
            ->method('get')
            ->willReturnCallback(function ($key, $callback) {
                $item = $this->createMock(ItemInterface::class);
                return $callback($item);
            });

        $responseMock = $this->createMock(ResponseInterface::class);
        $responseMock->method('getStatusCode')->willReturn(200);
        $responseMock->method('toArray')->willReturn([
            'results' => [
                [
                    'lat' => 48.8566,
                    'lon' => 2.3522,
                    'formatted' => '10 Rue de Rivoli, Paris, France',
                    'city' => 'Paris',
                    'country' => 'France',
                    'postcode' => '75004',
                ]
            ]
        ]);

        $this->httpClient->expects($this->once())
            ->method('request')
            ->willReturn($responseMock);

        $this->geoapifyService->geocodeProperty($property);

        $this->assertEquals(48.8566, $property->getLatitude());
        $this->assertEquals(2.3522, $property->getLongitude());
    }

    public function testGeocodePropertyWithEmptyResponse(): void
    {
        $this->createService();

        $property = new Property();
        $property->setAddress('Unknown Place 123');

        $this->cache->expects($this->once())
            ->method('get')
            ->willReturnCallback(function ($key, $callback) {
                $item = $this->createMock(ItemInterface::class);
                return $callback($item);
            });

        $responseMock = $this->createMock(ResponseInterface::class);
        $responseMock->method('getStatusCode')->willReturn(200);
        $responseMock->method('toArray')->willReturn(['results' => []]);

        $this->httpClient->method('request')->willReturn($responseMock);

        $this->geoapifyService->geocodeProperty($property);

        // Coordinates should remain null if nothing found
        $this->assertNull($property->getLatitude());
        $this->assertNull($property->getLongitude());
    }
    
    public function testGeocodePropertyHandlesHttpError(): void
    {
        $this->createService();

        $property = new Property();
        $property->setAddress('Error Test');

        $this->cache->expects($this->once())
            ->method('get')
            ->willReturnCallback(function ($key, $callback) {
                $item = $this->createMock(ItemInterface::class);
                return $callback($item);
            });

        $responseMock = $this->createMock(ResponseInterface::class);
        $responseMock->method('getStatusCode')->willReturn(500);

        $this->httpClient->method('request')->willReturn($responseMock);

        $this->geoapifyService->geocodeProperty($property);

        $this->assertNull($property->getLatitude());
    }

    public function testAutocompleteReturnsValidArray(): void
    {
        $this->createService();

        $this->cache->expects($this->once())
            ->method('get')
            ->willReturnCallback(function ($key, $callback) {
                $item = $this->createMock(ItemInterface::class);
                return $callback($item);
            });

        $responseMock = $this->createMock(ResponseInterface::class);
        $responseMock->method('getStatusCode')->willReturn(200);
        $responseMock->method('toArray')->willReturn([
            'results' => [
                [
                    'formatted' => 'Times Square, NYC',
                    'address_line1' => 'Times Square',
                    'city' => 'New York',
                    'country' => 'USA',
                    'postcode' => '10036',
                    'lat' => 40.7580,
                    'lon' => -73.9855
                ]
            ]
        ]);

        $this->httpClient->method('request')->willReturn($responseMock);

        $result = $this->geoapifyService->autocomplete('Times Square');

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertEquals('Times Square, NYC', $result[0]['formatted']);
        $this->assertEquals(40.7580, $result[0]['latitude']);
    }

    public function testAutocompleteWithInvalidOrMissingApiKey(): void
    {
        $this->createService(''); // Empty API key

        $result = $this->geoapifyService->autocomplete('Test');

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }
}
