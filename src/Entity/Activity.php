<?php

namespace App\Entity;

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
    private ?string $title = null;

    #[ORM\Column(length: 100, nullable: true)]
    #[Assert\Length(max: 100)]
    #[Assert\Regex(pattern: '/^[^<>]*$/u', message: 'Type contains invalid characters.')]
    private ?string $type = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Assert\Length(max: 4000)]
    #[Assert\Regex(pattern: '/^[^<>]*$/u', message: 'Description contains invalid characters.')]
    private ?string $description = null;

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

    #[ORM\Column(length: 100, nullable: true)]
    #[Assert\Length(max: 100)]
    #[Assert\Regex(pattern: '/^[^<>]*$/u', message: 'Transport type contains invalid characters.')]
    private ?string $transportType = null;

    #[ORM\Column(nullable: true)]
    #[Assert\PositiveOrZero(message: 'Cost cannot be negative.')]
    #[Assert\LessThanOrEqual(value: 1000000000)]
    private ?float $costAmount = 0.0;

    #[ORM\Column(length: 10, options: ['default' => 'USD'])]
    #[Assert\NotBlank(message: 'Currency is required.')]
    #[Assert\Regex(pattern: '/^[A-Z]{3,10}$/', message: 'Currency must be uppercase letters (e.g. USD).')]
    private string $currency = 'USD';

    #[ORM\Column(nullable: true)]
    #[Assert\PositiveOrZero(message: 'XP earned cannot be negative.')]
    #[Assert\LessThanOrEqual(value: 1000000000)]
    private ?int $xpEarned = 0;

    #[ORM\Column(length: 50, options: ['default' => 'PLANNED'])]
    #[Assert\NotBlank(message: 'Status is required.')]
    #[Assert\Choice(choices: self::ALLOWED_STATUSES, message: 'Select a valid activity status.')]
    private string $status = 'PLANNED';

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class)]
    #[ORM\JoinTable(name: 'trip_activity_participants')]
    private Collection $participants;

    public function __construct()
    {
        $this->participants = new ArrayCollection();
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
    }

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $now = new \DateTimeImmutable();
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

    public function getTitle(): ?string
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

    public function getTransportType(): ?string
    {
        return $this->transportType;
    }

    public function setTransportType(?string $transportType): static
    {
        $this->transportType = null === $transportType ? null : trim($transportType);

        return $this;
    }

    public function getCostAmount(): ?float
    {
        return $this->costAmount;
    }

    public function setCostAmount(?float $costAmount): static
    {
        $this->costAmount = null === $costAmount ? null : max(0.0, $costAmount);

        return $this;
    }

    public function getCurrency(): string
    {
        return $this->currency;
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
        return $this->status;
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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
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
