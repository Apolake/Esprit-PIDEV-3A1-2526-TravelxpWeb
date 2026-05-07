<?php

namespace App\Tests\Service;

use App\Entity\Booking;
use App\Entity\Property;
use App\Service\BookingPricingService;
use PHPUnit\Framework\TestCase;

class BookingPricingServiceTest extends TestCase
{
    private BookingPricingService $service;

    protected function setUp(): void
    {
        $this->service = new BookingPricingService();
    }

    public function testBuildPricingSnapshotReturnsExpectedKeys(): void
    {
        $property = new Property();
        $property->setPricePerNight(100);

        $booking = new Booking();
        $booking->setProperty($property);
        $booking->setBookingDate(new \DateTimeImmutable('+90 days'));
        $booking->setDuration(3);

        $snapshot = $this->service->buildPricingSnapshot($booking);

        $this->assertArrayHasKey('total', $snapshot);
        $this->assertArrayHasKey('currency', $snapshot);
        $this->assertArrayHasKey('duration', $snapshot);
        $this->assertArrayHasKey('baseNightlyRate', $snapshot);
        $this->assertArrayHasKey('dynamicNightlyRate', $snapshot);
        $this->assertArrayHasKey('narrative', $snapshot);
        $this->assertSame(3, $snapshot['duration']);
        $this->assertSame(100.0, $snapshot['baseNightlyRate']);
    }

    public function testApplyPricingSetsBookingFields(): void
    {
        $property = new Property();
        $property->setPricePerNight(200);

        $booking = new Booking();
        $booking->setProperty($property);
        $booking->setBookingDate(new \DateTimeImmutable('+90 days'));
        $booking->setDuration(2);

        $this->service->applyPricing($booking);

        $this->assertNotSame('0.00', $booking->getTotalPrice());
        $this->assertNotNull($booking->getPricingSnapshot());
    }

    public function testZeroPricePropertyReturnsZeroTotal(): void
    {
        $property = new Property();
        $property->setPricePerNight(0);

        $booking = new Booking();
        $booking->setProperty($property);
        $booking->setBookingDate(new \DateTimeImmutable('+30 days'));
        $booking->setDuration(5);

        $snapshot = $this->service->buildPricingSnapshot($booking);
        $this->assertSame(0.0, $snapshot['total']);
    }

    public function testNullPropertyHandled(): void
    {
        $booking = new Booking();
        $booking->setBookingDate(new \DateTimeImmutable('+10 days'));
        $booking->setDuration(1);

        $snapshot = $this->service->buildPricingSnapshot($booking);
        $this->assertSame(0.0, $snapshot['baseNightlyRate']);
        $this->assertSame(0.0, $snapshot['total']);
    }
}
