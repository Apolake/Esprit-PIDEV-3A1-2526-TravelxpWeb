<?php

namespace App\Tests\Entity;

use App\Entity\Booking;
use App\Entity\Payment;
use App\Entity\Property;
use App\Entity\Service;
use PHPUnit\Framework\TestCase;

class BookingEntityTest extends TestCase
{
    private Booking $booking;

    protected function setUp(): void
    {
        $this->booking = new Booking();
    }

    public function testDefaultStatusIsPending(): void
    {
        $this->assertSame(Booking::STATUS_PENDING, $this->booking->getStatus());
    }

    public function testSetStatusNormalizes(): void
    {
        $this->booking->setStatus('CONFIRMED');
        $this->assertSame(Booking::STATUS_CONFIRMED, $this->booking->getStatus());
    }

    public function testInvalidStatusFallsToPending(): void
    {
        $this->booking->setStatus('INVALID');
        $this->assertSame(Booking::STATUS_PENDING, $this->booking->getStatus());
    }

    public function testSetTotalPriceFormatsCorrectly(): void
    {
        $this->booking->setTotalPrice(99.999);
        $this->assertSame('100.00', $this->booking->getTotalPrice());
    }

    public function testTotalPriceCannotBeNegative(): void
    {
        $this->booking->setTotalPrice(-50);
        $this->assertSame('0.00', $this->booking->getTotalPrice());
    }

    public function testSetDurationMinimumIsOne(): void
    {
        $this->booking->setDuration(0);
        $this->assertSame(1, $this->booking->getDuration());
    }

    public function testUserIdMinimumIsOne(): void
    {
        $this->booking->setUserId(0);
        $this->assertSame(1, $this->booking->getUserId());
    }

    public function testPropertyRelation(): void
    {
        $property = new Property();
        $this->booking->setProperty($property);
        $this->assertSame($property, $this->booking->getProperty());
    }

    public function testAddAndRemoveService(): void
    {
        $service = new Service();
        $this->booking->addService($service);
        $this->assertCount(1, $this->booking->getServices());

        $this->booking->removeService($service);
        $this->assertCount(0, $this->booking->getServices());
    }

    public function testIsCancelled(): void
    {
        $this->booking->setStatus('cancelled');
        $this->assertTrue($this->booking->isCancelled());
    }

    public function testIsNotCancelledByDefault(): void
    {
        $this->assertFalse($this->booking->isCancelled());
    }

    public function testIsInPastWithPastDate(): void
    {
        $this->booking->setBookingDate(new \DateTimeImmutable('-5 days'));
        $this->assertTrue($this->booking->isInPast());
    }

    public function testIsNotInPastWithFutureDate(): void
    {
        $this->booking->setBookingDate(new \DateTimeImmutable('+5 days'));
        $this->assertFalse($this->booking->isInPast());
    }

    public function testOnPrePersistSetsCreatedAt(): void
    {
        $this->booking->onPrePersist();
        $this->assertNotNull($this->booking->getCreatedAt());
    }

    public function testCurrencyNormalizesToUppercase(): void
    {
        $this->booking->setCurrency('eur');
        $this->assertSame('EUR', $this->booking->getCurrency());
    }

    public function testInvalidCurrencyFallsToUsd(): void
    {
        $this->booking->setCurrency('123');
        $this->assertSame('USD', $this->booking->getCurrency());
    }

    public function testPricingSnapshotRoundTrip(): void
    {
        $snapshot = ['total' => 250.00, 'currency' => 'USD'];
        $this->booking->setPricingSnapshot($snapshot);
        $this->assertSame($snapshot, $this->booking->getPricingSnapshot());
    }
}
