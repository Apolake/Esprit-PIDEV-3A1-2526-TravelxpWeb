<?php

namespace App\Entity;

use App\Repository\TripWaitingListEntryRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TripWaitingListEntryRepository::class)]
#[ORM\Table(name: 'trip_waiting_list')]
#[ORM\UniqueConstraint(name: 'uniq_trip_waiting_trip_user_status', columns: ['trip_id', 'user_id', 'status'])]
#[ORM\Index(columns: ['status', 'expires_at'], name: 'idx_trip_waiting_status_exp')]
#[ORM\HasLifecycleCallbacks]
class TripWaitingListEntry
{
    public const STATUS_WAITING = 'WAITING';
    public const STATUS_PROMOTED = 'PROMOTED';
    public const STATUS_REJECTED = 'REJECTED';
    public const STATUS_EXPIRED = 'EXPIRED';
    public const STATUS_CANCELLED = 'CANCELLED';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'trip_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?Trip $trip = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?User $user = null;

    #[ORM\Column(length: 20, options: ['default' => self::STATUS_WAITING])]
    #[Assert\Choice(choices: [self::STATUS_WAITING, self::STATUS_PROMOTED, self::STATUS_REJECTED, self::STATUS_EXPIRED, self::STATUS_CANCELLED])]
    private string $status = self::STATUS_WAITING;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $queuedAt = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $expiresAt = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $promotedAt = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $now = new \DateTimeImmutable();
        $this->queuedAt ??= $now;
        $this->createdAt ??= $now;
        $this->updatedAt = $now;
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTrip(): ?Trip
    {
        return $this->trip;
    }

    public function setTrip(?Trip $trip): static
    {
        $this->trip = $trip;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = strtoupper(trim($status));

        return $this;
    }

    public function getQueuedAt(): ?\DateTimeImmutable
    {
        return $this->queuedAt;
    }

    public function setQueuedAt(?\DateTimeImmutable $queuedAt): static
    {
        $this->queuedAt = $queuedAt;

        return $this;
    }

    public function getExpiresAt(): ?\DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(?\DateTimeImmutable $expiresAt): static
    {
        $this->expiresAt = $expiresAt;

        return $this;
    }

    public function getPromotedAt(): ?\DateTimeImmutable
    {
        return $this->promotedAt;
    }

    public function setPromotedAt(?\DateTimeImmutable $promotedAt): static
    {
        $this->promotedAt = $promotedAt;

        return $this;
    }

    public function isExpiredAt(\DateTimeImmutable $at): bool
    {
        return $this->expiresAt !== null && $this->expiresAt <= $at;
    }
}
