<?php

namespace App\Entity\Trait;

use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * Provides createdBy/updatedBy audit fields for tracking which user performed changes.
 *
 * These fields should be set by an event subscriber or security context,
 * NOT by public setters. The setters are intentionally protected.
 */
trait BlameableTrait
{
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'created_by_id', nullable: true, onDelete: 'SET NULL')]
    private ?User $createdBy = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'updated_by_id', nullable: true, onDelete: 'SET NULL')]
    private ?User $updatedBy = null;

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    /**
     * @internal Should be set by a Doctrine event subscriber, not manually.
     */
    protected function setCreatedBy(?User $createdBy): void
    {
        $this->createdBy = $createdBy;
    }

    public function getUpdatedBy(): ?User
    {
        return $this->updatedBy;
    }

    /**
     * @internal Should be set by a Doctrine event subscriber, not manually.
     */
    protected function setUpdatedBy(?User $updatedBy): void
    {
        $this->updatedBy = $updatedBy;
    }

    /**
     * Helper for event subscribers to set blame on persist.
     */
    public function blame(?User $user): void
    {
        $this->createdBy ??= $user;
        $this->updatedBy = $user;
    }
}
