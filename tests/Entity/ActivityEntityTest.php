<?php

namespace App\Tests\Entity;

use App\Entity\Activity;
use App\Entity\Trip;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class ActivityEntityTest extends TestCase
{
    private Activity $activity;

    protected function setUp(): void
    {
        $this->activity = new Activity();
    }

    public function testSetAndGetTitle(): void
    {
        $this->activity->setTitle('  Museum Visit  ');
        $this->assertSame('Museum Visit', $this->activity->getTitle());
    }

    public function testSetAndGetTripRelation(): void
    {
        $trip = new Trip();
        $this->activity->setTrip($trip);
        $this->assertSame($trip, $this->activity->getTrip());
    }

    public function testDefaultStatusIsPlanned(): void
    {
        $this->activity->setActivityDate(new \DateTimeImmutable('+30 days'));
        $this->assertSame('PLANNED', $this->activity->getStatus());
    }

    public function testInvalidStatusFallsBackToPlanned(): void
    {
        $this->activity->setActivityDate(new \DateTimeImmutable('+30 days'));
        $this->activity->setStatus('BOGUS');
        $this->assertSame('PLANNED', $this->activity->getStatus());
    }

    public function testCapacityEnforcement(): void
    {
        $this->activity->setTotalCapacity(5);
        $this->activity->setAvailableSeats(10);
        $this->assertSame(5, $this->activity->getAvailableSeats());
    }

    public function testAvailableSeatsCannotBeNegative(): void
    {
        $this->activity->setTotalCapacity(5);
        $this->activity->setAvailableSeats(-1);
        $this->assertSame(0, $this->activity->getAvailableSeats());
    }

    public function testRecalculateAvailableSeats(): void
    {
        $this->activity->setTotalCapacity(20);
        $this->activity->recalculateAvailableSeatsFromJoinedCount(8);
        $this->assertSame(12, $this->activity->getAvailableSeats());
    }

    public function testCostCannotBeNegative(): void
    {
        $this->activity->setCostAmount(-50.0);
        $this->assertSame(0.0, $this->activity->getCostAmount());
    }

    public function testCurrencyDefaultsToUsd(): void
    {
        $this->assertSame('USD', $this->activity->getCurrency());
    }

    public function testAddAndRemoveParticipant(): void
    {
        $user = new User();
        $this->activity->addParticipant($user);
        $this->assertTrue($this->activity->isParticipant($user));

        $this->activity->removeParticipant($user);
        $this->assertFalse($this->activity->isParticipant($user));
    }

    public function testDuplicateParticipantNotAdded(): void
    {
        $user = new User();
        $this->activity->addParticipant($user);
        $this->activity->addParticipant($user);
        $this->assertCount(1, $this->activity->getParticipants());
    }

    public function testOnPrePersistSetsTimestamps(): void
    {
        $this->activity->setTitle('Test Activity');
        $this->activity->onPrePersist();

        $this->assertNotNull($this->activity->getCreatedAt());
        $this->assertNotNull($this->activity->getUpdatedAt());
    }

    public function testXpCannotBeNegative(): void
    {
        $this->activity->setXpEarned(-10);
        $this->assertSame(0, $this->activity->getXpEarned());
    }
}
