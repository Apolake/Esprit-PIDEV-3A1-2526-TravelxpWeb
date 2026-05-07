<?php

namespace App\Tests\Entity;

use App\Entity\Activity;
use App\Entity\Trip;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class TripEntityTest extends TestCase
{
    private Trip $trip;

    protected function setUp(): void
    {
        $this->trip = new Trip();
    }

    // --- Basic getters/setters ---

    public function testSetAndGetTripName(): void
    {
        $this->trip->setTripName('  Paris Adventure  ');
        $this->assertSame('Paris Adventure', $this->trip->getTripName());
    }

    public function testSetAndGetOriginDestination(): void
    {
        $this->trip->setOrigin('  Tunis  ');
        $this->trip->setDestination('  Paris  ');
        $this->assertSame('Tunis', $this->trip->getOrigin());
        $this->assertSame('Paris', $this->trip->getDestination());
    }

    // --- Status logic ---

    public function testDefaultStatusIsPlanned(): void
    {
        $this->trip->setEndDate(new \DateTimeImmutable('+30 days'));
        $this->assertSame('PLANNED', $this->trip->getStatus());
    }

    public function testSetStatusNormalizesToUppercase(): void
    {
        $this->trip->setEndDate(new \DateTimeImmutable('+30 days'));
        $this->trip->setStatus('ongoing');
        $this->assertSame('ONGOING', $this->trip->getStatus());
    }

    public function testInvalidStatusFallsBackToPlanned(): void
    {
        $this->trip->setEndDate(new \DateTimeImmutable('+30 days'));
        $this->trip->setStatus('INVALID_STATUS');
        $this->assertSame('PLANNED', $this->trip->getStatus());
    }

    // --- Capacity logic ---

    public function testTotalCapacityMustBeAtLeastOne(): void
    {
        $this->trip->setTotalCapacity(0);
        $this->assertSame(1, $this->trip->getTotalCapacity());

        $this->trip->setTotalCapacity(-5);
        $this->assertSame(1, $this->trip->getTotalCapacity());
    }

    public function testAvailableSeatsCappedByCapacity(): void
    {
        $this->trip->setTotalCapacity(10);
        $this->trip->setAvailableSeats(15);
        $this->assertSame(10, $this->trip->getAvailableSeats());
    }

    public function testAvailableSeatsCannotBeNegative(): void
    {
        $this->trip->setTotalCapacity(5);
        $this->trip->setAvailableSeats(-3);
        $this->assertSame(0, $this->trip->getAvailableSeats());
    }

    public function testRecalculateAvailableSeats(): void
    {
        $this->trip->setTotalCapacity(10);
        $this->trip->recalculateAvailableSeatsFromJoinedCount(4);
        $this->assertSame(6, $this->trip->getAvailableSeats());
    }

    // --- Budget logic ---

    public function testBudgetCannotBeNegative(): void
    {
        $this->trip->setBudgetAmount(-100.0);
        $this->assertSame(0.0, $this->trip->getBudgetAmount());
    }

    public function testTotalExpensesCannotBeNegative(): void
    {
        $this->trip->setTotalExpenses(-50.0);
        $this->assertSame(0.0, $this->trip->getTotalExpenses());
    }

    // --- Currency ---

    public function testCurrencyDefaultsToUsd(): void
    {
        $this->assertSame('USD', $this->trip->getCurrency());
    }

    public function testSetCurrencyNormalizesToUppercase(): void
    {
        $this->trip->setCurrency('eur');
        $this->assertSame('EUR', $this->trip->getCurrency());
    }

    public function testEmptyCurrencyFallsBackToUsd(): void
    {
        $this->trip->setCurrency('');
        $this->assertSame('USD', $this->trip->getCurrency());
    }

    // --- Participants ---

    public function testAddAndRemoveParticipant(): void
    {
        $user = new User();
        $this->trip->addParticipant($user);
        $this->assertTrue($this->trip->isParticipant($user));
        $this->assertCount(1, $this->trip->getParticipants());

        $this->trip->removeParticipant($user);
        $this->assertFalse($this->trip->isParticipant($user));
        $this->assertCount(0, $this->trip->getParticipants());
    }

    public function testDuplicateParticipantNotAdded(): void
    {
        $user = new User();
        $this->trip->addParticipant($user);
        $this->trip->addParticipant($user);
        $this->assertCount(1, $this->trip->getParticipants());
    }

    // --- Activities ---

    public function testAddAndRemoveActivity(): void
    {
        $activity = new Activity();
        $this->trip->addActivity($activity);
        $this->assertCount(1, $this->trip->getActivities());
        $this->assertSame($this->trip, $activity->getTrip());

        $this->trip->removeActivity($activity);
        $this->assertCount(0, $this->trip->getActivities());
    }

    // --- Lifecycle callbacks ---

    public function testOnPrePersistSetsTimestamps(): void
    {
        $this->trip->setTripName('Test');
        $this->trip->setStartDate(new \DateTimeImmutable('+1 day'));
        $this->trip->setEndDate(new \DateTimeImmutable('+5 days'));
        $this->trip->onPrePersist();

        $this->assertNotNull($this->trip->getCreatedAt());
        $this->assertNotNull($this->trip->getUpdatedAt());
    }

    public function testOnPreUpdateRefreshesUpdatedAt(): void
    {
        $this->trip->setStartDate(new \DateTimeImmutable('+1 day'));
        $this->trip->setEndDate(new \DateTimeImmutable('+5 days'));
        $this->trip->onPrePersist();
        $firstUpdate = $this->trip->getUpdatedAt();

        usleep(1000);
        $this->trip->onPreUpdate();
        $this->assertGreaterThanOrEqual($firstUpdate, $this->trip->getUpdatedAt());
    }

    // --- XP ---

    public function testXpCannotBeNegative(): void
    {
        $this->trip->setTotalXpEarned(-10);
        $this->assertSame(0, $this->trip->getTotalXpEarned());
    }
}
