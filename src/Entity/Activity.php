<?php

namespace App\Entity;

use App\Entity\Trait\BlameableTrait;
use App\Repository\ActivityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: ActivityRepository::class)]
#[ORM\Table(name: 'activities')]
#[ORM\HasLifecycleCallbacks]
class Activity
{
    use BlameableTrait;
    public const ALLOWED_STATUSES = ['PLANNED', 'ONGOING', 'COMPLETED', 'DONE', 'CANCELLED'];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'activities')]
    #[ORM\JoinColumn(name: 'trip_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?Trip $trip = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(normalizer: 'trim', message: 'Activity title is required.')]
    #[Assert\Length(min: 2, max: 255)]
    #[Assert\Regex(pattern: '/^[^<>]+$/u', message: 'Activity title contains invalid characters.')]
    private string $title = '';

    #[ORM\Column(length: 100, nullable: true)]
    #[Assert\Length(max: 100)]
    #[Assert\Regex(pattern: '/^[^<>]*$/u', message: 'Type contains invalid characters.')]
    private ?string $type = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Assert\Length(max: 4000)]
    #[Assert\Regex(pattern: '/^[^<>]*$/u', message: 'Description contains invalid characters.')]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(max: 255)]
    #[Assert\Regex(pattern: '/^[^<>]*$/u', message: 'Image URL contains invalid characters.')]
    private ?string $imageUrl = null;

    #[ORM\Column(type: 'date_immutable', nullable: true)]
    private ?\DateTimeImmutable $activityDate = null;

    #[ORM\Column(type: 'time_immutable', nullable: true)]
    private ?\DateTimeImmutable $startTime = null;

    #[ORM\Column(type: 'time_immutable', nullable: true)]
    private ?\DateTimeImmutable $endTime = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(max: 255)]
    #[Assert\Regex(pattern: '/^[^<>]*$/u', message: 'Location name contains invalid characters.')]
    private ?string $locationName = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 7, nullable: true)]
    #[Assert\Range(min: -90, max: 90, notInRangeMessage: 'Latitude must be between -90 and 90.')]
    private ?string $locationLatitude = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 7, nullable: true)]
    #[Assert\Range(min: -180, max: 180, notInRangeMessage: 'Longitude must be between -180 and 180.')]
    private ?string $locationLongitude = null;

    #[ORM\Column(length: 100, nullable: true)]
    #[Assert\Length(max: 100)]
    #[Assert\Regex(pattern: '/^[^<>]*$/u', message: 'Transport type contains invalid characters.')]
    private ?string $transportType = null;

    #[ORM\Column(type: 'decimal', precision: 12, scale: 2, nullable: true)]
    #[Assert\PositiveOrZero(message: 'Cost cannot be negative.')]
    #[Assert\LessThanOrEqual(value: 1000000000)]
    private ?string $costAmount = '0.00';

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

    #[ORM\Column(nullable: true)]
    #[Assert\PositiveOrZero(message: 'XP earned cannot be negative.')]
    #[Assert\LessThanOrEqual(value: 1000000000)]
    private ?int $xpEarned = 0;

    #[ORM\Column(length: 50, nullable: true, options: ['default' => 'PLANNED'])]
    #[Assert\NotBlank(message: 'Status is required.')]
    #[Assert\Choice(choices: self::ALLOWED_STATUSES, message: 'Select a valid activity status.')]
    private ?string $status = 'PLANNED';

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class)]
    #[ORM\JoinTable(name: 'trip_activity_participants')]
    private Collection $participants;

    public function __construct()
    {
        $this->participants = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    #[Assert\Callback]
    public function validateActivity(ExecutionContextInterface $context): void
    {
        if (($this->startTime !== null && $this->endTime === null) || ($this->startTime === null && $this->endTime !== null)) {
            $context->buildViolation('Both start time and end time must be provided together.')
                ->atPath($this->startTime === null ? 'startTime' : 'endTime')
                ->addViolation();
        }

        if ($this->startTime !== null && $this->endTime !== null && $this->endTime <= $this->startTime) {
            $context->buildViolation('End time must be later than start time.')
                ->atPath('endTime')
                ->addViolation();
        }

        if (($this->startTime !== null || $this->endTime !== null) && $this->activityDate === null) {
            $context->buildViolation('Activity date is required when time is set.')
                ->atPath('activityDate')
                ->addViolation();
        }

        if ($this->trip !== null && $this->activityDate !== null) {
            $tripStart = $this->trip->getStartDate();
            $tripEnd = $this->trip->getEndDate();
            if ($tripStart !== null && $tripEnd !== null && ($this->activityDate < $tripStart || $this->activityDate > $tripEnd)) {
                $context->buildViolation('Activity date must be within the selected trip date range.')
                    ->atPath('activityDate')
                    ->addViolation();
            }
        }

        if ($this->totalCapacity !== null && $this->availableSeats !== null && $this->availableSeats > $this->totalCapacity) {
            $context->buildViolation('Available seats cannot exceed total capacity.')
                ->atPath('availableSeats')
                ->addViolation();
        }

        if (($this->locationLatitude === null) xor ($this->locationLongitude === null)) {
            $context->buildViolation('Activity coordinates must include both latitude and longitude.')
                ->atPath($this->locationLatitude === null ? 'locationLatitude' : 'locationLongitude')
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
        if (null === $this->activityDate) {
            return;
        }

        $currentStatus = strtoupper(trim((string) $this->status));
        if (in_array($currentStatus, ['CANCELLED', 'DONE', 'COMPLETED'], true)) {
            return;
        }

        $today = new \DateTimeImmutable('today');
        if ($this->activityDate < $today) {
            $this->status = 'COMPLETED';

            return;
        }

        if ($this->activityDate == $today && null !== $this->endTime) {
            $endDateTime = \DateTimeImmutable::createFromFormat(
                'Y-m-d H:i:s',
                $this->activityDate->format('Y-m-d') . ' ' . $this->endTime->format('H:i:s')
            );
            if (false !== $endDateTime && $endDateTime <= new \DateTimeImmutable()) {
                $this->status = 'COMPLETED';
            }
        }
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

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(?string $title): static
    {
        $this->title = null === $title ? null : trim($title);

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): static
    {
        $this->type = null === $type ? null : trim($type);

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

    public function getActivityDate(): ?\DateTimeImmutable
    {
        return $this->activityDate;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(?string $imageUrl): static
    {
        $this->imageUrl = null === $imageUrl ? null : trim($imageUrl);

        return $this;
    }

    public function setActivityDate(?\DateTimeInterface $activityDate): static
    {
        $this->activityDate = null === $activityDate ? null : \DateTimeImmutable::createFromInterface($activityDate);

        return $this;
    }

    public function getStartTime(): ?\DateTimeImmutable
    {
        return $this->startTime;
    }

    public function setStartTime(?\DateTimeInterface $startTime): static
    {
        $this->startTime = null === $startTime ? null : \DateTimeImmutable::createFromInterface($startTime);

        return $this;
    }

    public function getEndTime(): ?\DateTimeImmutable
    {
        return $this->endTime;
    }

    public function setEndTime(?\DateTimeInterface $endTime): static
    {
        $this->endTime = null === $endTime ? null : \DateTimeImmutable::createFromInterface($endTime);

        return $this;
    }

    public function getLocationName(): ?string
    {
        return $this->locationName;
    }

    public function setLocationName(?string $locationName): static
    {
        $this->locationName = null === $locationName ? null : trim($locationName);

        return $this;
    }

    public function getLocationLatitude(): ?string
    {
        return $this->locationLatitude;
    }

    public function setLocationLatitude(null|string|float $locationLatitude): static
    {
        $this->locationLatitude = null === $locationLatitude ? null : (string) $locationLatitude;

        return $this;
    }

    public function getLocationLongitude(): ?string
    {
        return $this->locationLongitude;
    }

    public function setLocationLongitude(null|string|float $locationLongitude): static
    {
        $this->locationLongitude = null === $locationLongitude ? null : (string) $locationLongitude;

        return $this;
    }

    public function getTransportType(): ?string
    {
        return $this->transportType;
    }

    public function setTransportType(?string $transportType): static
    {
        $this->transportType = null === $transportType ? null : trim($transportType);

        return $this;
    }

    public function getCostAmount(): ?string
    {
        return $this->costAmount;
    }

    public function setCostAmount(null|string|float|int $costAmount): static
    {
        if (null === $costAmount) {
            $this->costAmount = null;
        } else {
            $value = is_string($costAmount) ? (float) $costAmount : (float) $costAmount;
            $this->costAmount = number_format(max(0, $value), 2, '.', '');
        }

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

    public function getXpEarned(): ?int
    {
        return $this->xpEarned;
    }

    public function setXpEarned(?int $xpEarned): static
    {
        $this->xpEarned = null === $xpEarned ? null : max(0, $xpEarned);

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

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
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
