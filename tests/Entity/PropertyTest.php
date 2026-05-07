<?php

namespace App\Tests\Entity;

use App\Entity\Booking;
use App\Entity\Offer;
use App\Entity\Property;
use PHPUnit\Framework\TestCase;

class PropertyEntityTest extends TestCase
{
    private Property $property;

    protected function setUp(): void
    {
        $this->property = new Property();
    }

    public function testSetAndGetTitle(): void
    {
        $this->property->setTitle('  Beach Villa  ');
        $this->assertSame('Beach Villa', $this->property->getTitle());
    }

    public function testSetAndGetLocation(): void
    {
        $this->property->setCity('  Sousse  ');
        $this->property->setCountry('  Tunisia  ');
        $this->assertSame('Sousse', $this->property->getCity());
        $this->assertSame('Tunisia', $this->property->getCountry());
    }

    public function testPricePerNightCannotBeNegative(): void
    {
        $this->property->setPricePerNight(-100);
        $this->assertSame('0.00', $this->property->getPricePerNight());
    }

    public function testPricePerNightFormatsCorrectly(): void
    {
        $this->property->setPricePerNight(125.5);
        $this->assertSame('125.50', $this->property->getPricePerNight());
    }

    public function testBedroomsCannotBeNegative(): void
    {
        $this->property->setBedrooms(-3);
        $this->assertSame(0, $this->property->getBedrooms());
    }

    public function testMaxGuestsMinimumIsOne(): void
    {
        $this->property->setMaxGuests(0);
        $this->assertSame(1, $this->property->getMaxGuests());
    }

    public function testIsActiveDefaultTrue(): void
    {
        $this->assertTrue($this->property->isActive());
    }

    public function testSetIsActive(): void
    {
        $this->property->setIsActive(false);
        $this->assertFalse($this->property->isActive());
    }

    public function testAddAndRemoveOffer(): void
    {
        $offer = new Offer();
        $this->property->addOffer($offer);
        $this->assertCount(1, $this->property->getOffers());
        $this->assertSame($this->property, $offer->getProperty());

        $this->property->removeOffer($offer);
        $this->assertCount(0, $this->property->getOffers());
    }

    public function testAddAndRemoveBooking(): void
    {
        $booking = new Booking();
        $this->property->addBooking($booking);
        $this->assertCount(1, $this->property->getBookings());

        $this->property->removeBooking($booking);
        $this->assertCount(0, $this->property->getBookings());
    }

    public function testOnPrePersistSetsCreatedAt(): void
    {
        $this->property->onPrePersist();
        $this->assertNotNull($this->property->getCreatedAt());
    }

    public function testCoordinates(): void
    {
        $this->property->setLatitude(36.8065);
        $this->property->setLongitude(10.1815);
        $this->assertSame(36.8065, $this->property->getLatitude());
        $this->assertSame(10.1815, $this->property->getLongitude());
    }
}
