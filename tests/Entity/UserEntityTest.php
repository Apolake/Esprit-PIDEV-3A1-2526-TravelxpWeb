<?php

namespace App\Tests\Entity;

use App\Entity\Budget;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserEntityTest extends TestCase
{
    private User $user;

    protected function setUp(): void
    {
        $this->user = new User();
    }

    public function testSetAndGetUsername(): void
    {
        $this->user->setUsername('  john_doe  ');
        $this->assertSame('john_doe', $this->user->getUsername());
    }

    public function testSetEmailNormalizesToLowercase(): void
    {
        $this->user->setEmail('  John@Example.COM  ');
        $this->assertSame('john@example.com', $this->user->getEmail());
    }

    public function testDefaultRoleIsUser(): void
    {
        $roles = $this->user->getRoles();
        $this->assertContains('ROLE_USER', $roles);
    }

    public function testSetRoleAdmin(): void
    {
        $this->user->setRole('admin');
        $this->assertContains('ROLE_ADMIN', $this->user->getRoles());
    }

    public function testXpCannotBeNegative(): void
    {
        $this->user->setXp(-100);
        $this->assertSame(0, $this->user->getXp());
    }

    public function testLevelMinimumIsOne(): void
    {
        $this->user->setLevel(0);
        $this->assertSame(1, $this->user->getLevel());
    }

    public function testStreakCannotBeNegative(): void
    {
        $this->user->setStreak(-5);
        $this->assertSame(0, $this->user->getStreak());
    }

    public function testRecoveryCodesFilterEmptyStrings(): void
    {
        $this->user->setTotpRecoveryCodes(['code1', '', 'code2']);
        $codes = $this->user->getTotpRecoveryCodes();
        $this->assertCount(2, $codes);
    }

    public function testFirebaseUidNormalization(): void
    {
        $this->user->setFirebaseUid('  ');
        $this->assertNull($this->user->getFirebaseUid());
        $this->user->setFirebaseUid('abc123');
        $this->assertSame('abc123', $this->user->getFirebaseUid());
    }

    public function testAddAndRemoveBudget(): void
    {
        $budget = new Budget();
        $this->user->addBudget($budget);
        $this->assertCount(1, $this->user->getBudgets());
        $this->user->removeBudget($budget);
        $this->assertCount(0, $this->user->getBudgets());
    }

    public function testOnPrePersistSetsTimestamps(): void
    {
        $this->user->onPrePersist();
        $this->assertNotNull($this->user->getCreatedAt());
        $this->assertNotNull($this->user->getUpdatedAt());
    }
}
