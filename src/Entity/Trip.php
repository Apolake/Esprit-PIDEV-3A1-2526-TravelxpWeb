<?php

namespace App\Entity;

use App\Repository\TripRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: TripRepository::class)]
#[ORM\Table(name: 'trips')]
#[ORM\HasLifecycleCallbacks]
class Trip
{
    public const ALLOWED_STATUSES = ['PLANNED', 'ONGOING', 'COMPLETED', 'DONE', 'CANCELLED'];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    #[Assert\Positive(message: 'Owner user ID must be positive.')]
    private ?int $userId = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(normalizer: 'trim', message: 'Trip name is required.')]
    #[Assert\Length(min: 2, max: 255)]
    #[Assert\Regex(pattern: '/^[^<>]+$/u', message: 'Trip name contains invalid characters.')]
    private string $tripName = '';

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(max: 255)]
    #[Assert\Regex(pattern: '/^[^<>]*$/u', message: 'Origin contains invalid characters.')]
    private ?string $origin = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(max: 255)]
    #[Assert\Regex(pattern: '/^[^<>]*$/u', message: 'Destination contains invalid characters.')]
    private ?string $destination = null;

    #[ORM\Column(nullable: true)]
    #[Assert\Range(min: -90, max: 90, notInRangeMessage: 'Latitude must be between -90 and 90.')]
    private ?float $destinationLatitude = null;

    #[ORM\Column(nullable: true)]
    #[Assert\Range(min: -180, max: 180, notInRangeMessage: 'Longitude must be between -180 and 180.')]
    private ?float $destinationLongitude = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Assert\Length(max: 4000)]
    #[Assert\Regex(pattern: '/^[^<>]*$/u', message: 'Description contains invalid characters.')]
    private ?string $description = null;

    #[ORM\Column(type: 'date_immutable')]
    #[Assert\NotNull(message: 'Start date is required.')]
    private \DateTimeImmutable $startDate;

    #[ORM\Column(type: 'date_immutable')]
    #[Assert\NotNull(message: 'End date is required.')]
    private \DateTimeImmutable $endDate;

    #[ORM\Column(length: 50, nullable: true, options: ['default' => 'PLANNED'])]
    #[Assert\NotBlank(message: 'Status is required.')]
    #[Assert\Choice(choices: self::ALLOWED_STATUSES, message: 'Select a valid trip status.')]
    private ?string $status = 'PLANNED';

    #[ORM\Column(type: 'decimal', precision: 12, scale: 2, nullable: true)]
    #[Assert\PositiveOrZero(message: 'Budget cannot be negative.')]
    #[Assert\LessThanOrEqual(value: 1000000000)]
    private ?string $budgetAmount = '0.00';

    #[ORM\Column(options: ['default' => 1])]
    #[Assert\NotNull(message: 'Total capacity is required.')]
    #[Assert\Positive(message: 'Total capacity must be greater than zero.')]
    private int $totalCapacity = 1;

    #[ORM\Column(options: ['default' => 1])]
    #[Assert\NotNull(message: 'Available seats are required.')]
    #[Assert\PositiveOrZero(message: 'Available seats cannot be negative.')]
    private int $availableSeats = 1;

    #[ORM\Column(length: 10, nullable: true, options: ['default' => 'USD'])]
    #[Assert\NotBlank(message: 'Currency is required.')]
    #[Assert\Regex(pattern: '/^[A-Z]{3,10}$/', message: 'Currency must be uppercase letters (e.g. USD).')]
    private ?string $currency = 'USD';

    #[ORM\Column(type: 'decimal', precision: 12, scale: 2, nullable: true)]
    #[Assert\PositiveOrZero(message: 'Total expenses cannot be negative.')]
    #[Assert\LessThanOrEqual(value: 1000000000)]
    private ?string $totalExpenses = '0.00';

    #[ORM\Column(nullable: true)]
    #[Assert\PositiveOrZero(message: 'Total XP earned cannot be negative.')]
    #[Assert\LessThanOrEqual(value: 1000000000)]
    private ?int $totalXpEarned = 0;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Assert\Length(max: 4000)]
    #[Assert\Regex(pattern: '/^[^<>]*$/u', message: 'Notes contain invalid characters.')]
    private ?string $notes = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(max: 255)]
    #[Assert\Url(message: 'Cover image URL is not valid.')]
    private ?string $coverImageUrl = null;

    #[ORM\Column(nullable: true)]
    #[Assert\Positive(message: 'Parent trip ID must be positive.')]
    private ?int $parentId = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    /**
     * @var Collection<int, Activity>
     */
    #[ORM\OneToMany(targetEntity: Activity::class, mappedBy: 'trip')]
    private Collection $activities;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class)]
    #[ORM\JoinTable(name: 'trip_participants')]
    private Collection $participants;

    public function __construct()
    {
        $this->activities = new ArrayCollection();
        $this->participants = new ArrayCollection();
        $this->startDate = new \DateTimeImmutable();
        $this->endDate = new \DateTimeImmutable();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    #[Assert\Callback]
    public function validateTrip(ExecutionContextInterface $context): void
    {
        if ($this->startDate !== null && $this->endDate !== null && $this->endDate < $this->startDate) {
            $context->buildViolation('End date must be on or after start date.')
                ->atPath('endDate')
                ->addViolation();
        }

        if (
            $this->budgetAmount !== null
            && $this->totalExpenses !== null
            && $this->budgetAmount > 0
            && $this->totalExpenses > $this->budgetAmount
        ) {
            $context->buildViolation('Total expenses cannot exceed budget amount.')
                ->atPath('totalExpenses')
                ->addViolation();
        }

        if (
            $this->origin !== null
            && $this->destination !== null
            && trim($this->origin) !== ''
            && trim($this->destination) !== ''
            && mb_strtolower(trim($this->origin)) === mb_strtolower(trim($this->destination))
        ) {
            $context->buildViolation('Origin and destination cannot be the same.')
                ->atPath('destination')
                ->addViolation();
        }

        if ($this->id !== null && $this->parentId !== null && $this->id === $this->parentId) {
            $context->buildViolation('A trip cannot be its own parent.')
                ->atPath('parentId')
                ->addViolation();
        }

        if ($this->totalCapacity !== null && $this->availableSeats !== null && $this->availableSeats > $this->totalCapacity) {
            $context->buildViolation('Available seats cannot exceed total capacity.')
                ->atPath('availableSeats')
                ->addViolation();
        }

        if (($this->destinationLatitude === null) xor ($this->destinationLongitude === null)) {
            $context->buildViolation('Destination coordinates must include both latitude and longitude.')
                ->atPath($this->destinationLatitude === null ? 'destinationLatitude' : 'destinationLongitude')
                ->addViolation();
        }
    }

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $this->applyTemporalStatus();
        $this->totalCapacity = max(1, (int) ($this->totalCapacity ?? 1));
        if (null === $this->availableSeats) {
            $this->availableSeats = $this->totalCapacity;
        }
        $this->availableSeats = max(0, min($this->availableSeats, $this->totalCapacity));
        $now = new \DateTimeImmutable();
        $this->createdAt ??= $now;
        $this->updatedAt = $now;
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->applyTemporalStatus();
        $this->totalCapacity = max(1, (int) ($this->totalCapacity ?? 1));
        $this->availableSeats = max(0, min((int) ($this->availableSeats ?? 0), $this->totalCapacity));
        $this->updatedAt = new \DateTimeImmutable();
    }

    private function applyTemporalStatus(): void
    {
        if (null === $this->endDate) {
            return;
        }

        $currentStatus = strtoupper(trim((string) $this->status));
        if (in_array($currentStatus, ['CANCELLED', 'DONE', 'COMPLETED'], true)) {
            return;
        }

        $today = new \DateTimeImmutable('today');
        if ($this->endDate < $today) {
            $this->status = 'COMPLETED';
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(?int $userId): static
    {
        $this->userId = $userId;

        return $this;
    }

    public function getTripName(): string
    {
        return $this->tripName;
    }

    public function setTripName(?string $tripName): static
    {
        $this->tripName = null === $tripName ? null : trim($tripName);

        return $this;
    }

    public function getOrigin(): ?string
    {
        return $this->origin;
    }

    public function setOrigin(?string $origin): static
    {
        $this->origin = null === $origin ? null : trim($origin);

        return $this;
    }

    public function getDestination(): ?string
    {
        return $this->destination;
    }

    public function setDestination(?string $destination): static
    {
        $this->destination = null === $destination ? null : trim($destination);

        return $this;
    }

    public function getDestinationLatitude(): ?float
    {
        return $this->destinationLatitude;
    }

    public function setDestinationLatitude(?float $destinationLatitude): static
    {
        $this->destinationLatitude = $destinationLatitude;

        return $this;
    }

    public function getDestinationLongitude(): ?float
    {
        return $this->destinationLongitude;
    }

    public function setDestinationLongitude(?float $destinationLongitude): static
    {
        $this->destinationLongitude = $destinationLongitude;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getStartDate(): \DateTimeImmutable
    {
        return $this->startDate;
    }

    public function setStartDate(?\DateTimeInterface $startDate): static
    {
        $this->startDate = null === $startDate ? null : \DateTimeImmutable::createFromInterface($startDate);

        return $this;
    }

    public function getEndDate(): \DateTimeImmutable
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTimeInterface $endDate): static
    {
        $this->endDate = null === $endDate ? null : \DateTimeImmutable::createFromInterface($endDate);

        return $this;
    }

    public function getStatus(): string
    {
        $this->applyTemporalStatus();
        $value = strtoupper(trim((string) $this->status));

        return in_array($value, self::ALLOWED_STATUSES, true) ? $value : 'PLANNED';
    }

    public function setStatus(?string $status): static
    {
        $normalized = strtoupper(trim((string) $status));
        if (!in_array($normalized, self::ALLOWED_STATUSES, true)) {
            $normalized = 'PLANNED';
        }

        $this->status = $normalized;

        return $this;
    }

    public function getBudgetAmount(): ?string
    {
        return $this->budgetAmount;
    }

    public function setBudgetAmount(null|string|float|int $budgetAmount): static
    {
        if (null === $budgetAmount) {
            $this->budgetAmount = null;
        } else {
            $value = is_string($budgetAmount) ? (float) $budgetAmount : (float) $budgetAmount;
            $this->budgetAmount = number_format(max(0, $value), 2, '.', '');
        }

        return $this;
    }

    public function getCurrency(): string
    {
        $value = strtoupper(trim((string) $this->currency));

        return '' === $value ? 'USD' : $value;
    }

    public function setCurrency(?string $currency): static
    {
        $normalized = strtoupper(trim((string) $currency));
        $this->currency = '' === $normalized ? 'USD' : $normalized;

        return $this;
    }

    public function getTotalCapacity(): int
    {
        return max(1, (int) ($this->totalCapacity ?? 1));
    }

    public function setTotalCapacity(?int $totalCapacity): static
    {
        $this->totalCapacity = max(1, (int) ($totalCapacity ?? 1));
        if (null === $this->availableSeats || $this->availableSeats > $this->totalCapacity) {
            $this->availableSeats = $this->totalCapacity;
        }

        return $this;
    }

    public function getAvailableSeats(): int
    {
        return max(0, (int) ($this->availableSeats ?? 0));
    }

    public function setAvailableSeats(?int $availableSeats): static
    {
        $capacity = $this->getTotalCapacity();
        $this->availableSeats = max(0, min((int) ($availableSeats ?? 0), $capacity));

        return $this;
    }

    public function recalculateAvailableSeatsFromJoinedCount(int $joinedCount): static
    {
        $this->setAvailableSeats($this->getTotalCapacity() - max(0, $joinedCount));

        return $this;
    }

    public function getTotalExpenses(): ?string
    {
        return $this->totalExpenses;
    }

    public function setTotalExpenses(null|string|float|int $totalExpenses): static
    {
        if (null === $totalExpenses) {
            $this->totalExpenses = null;
        } else {
            $value = is_string($totalExpenses) ? (float) $totalExpenses : (float) $totalExpenses;
            $this->totalExpenses = number_format(max(0, $value), 2, '.', '');
        }

        return $this;
    }

    public function getTotalXpEarned(): ?int
    {
        return $this->totalXpEarned;
    }

    public function setTotalXpEarned(?int $totalXpEarned): static
    {
        $this->totalXpEarned = null === $totalXpEarned ? null : max(0, $totalXpEarned);

        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): static
    {
        $this->notes = $notes;

        return $this;
    }

    public function getCoverImageUrl(): ?string
    {
        return $this->coverImageUrl;
    }

    public function setCoverImageUrl(?string $coverImageUrl): static
    {
        $this->coverImageUrl = null === $coverImageUrl ? null : trim($coverImageUrl);

        return $this;
    }

    public function getParentId(): ?int
    {
        return $this->parentId;
    }

    public function setParentId(?int $parentId): static
    {
        $this->parentId = $parentId;

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * @return Collection<int, Activity>
     */
    public function getActivities(): Collection
    {
        return $this->activities;
    }

    public function addActivity(Activity $activity): static
    {
        if (!$this->activities->contains($activity)) {
            $this->activities->add($activity);
            $activity->setTrip($this);
        }

        return $this;
    }

    public function removeActivity(Activity $activity): static
    {
        if ($this->activities->removeElement($activity) && $activity->getTrip() === $this) {
            $activity->setTrip(null);
        }

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function addParticipant(User $user): static
    {
        if (!$this->participants->contains($user)) {
            $this->participants->add($user);
        }

        return $this;
    }

    public function removeParticipant(User $user): static
    {
        $this->participants->removeElement($user);

        return $this;
    }

    public function isParticipant(User $user): bool
    {
        return $this->participants->contains($user);
    }
}
