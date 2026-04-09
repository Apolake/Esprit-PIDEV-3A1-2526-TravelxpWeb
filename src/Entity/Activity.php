<?php

namespace App\Entity;

use App\Repository\ActivityRepository;
use Doctrine\DBAL\Types\Types;
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
    #[ORM\Column(type: Types::BIGINT)]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'activities')]
    #[ORM\JoinColumn(name: 'trip_id', referencedColumnName: 'id', nullable: true)]
    private ?Trip $trip = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Activity title is required.')]
    #[Assert\Length(min: 2, max: 255, minMessage: 'Activity title must be at least {{ limit }} characters.', maxMessage: 'Activity title cannot exceed {{ limit }} characters.')]
    #[Assert\Regex(pattern: '/^[^<>]+$/u', message: 'Activity title contains invalid characters.')]
    private ?string $title = null;

    #[ORM\Column(length: 100, nullable: true)]
    #[Assert\Length(max: 100, maxMessage: 'Type cannot exceed {{ limit }} characters.')]
    #[Assert\Regex(pattern: '/^[^<>]*$/u', message: 'Type contains invalid characters.')]
    private ?string $type = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Assert\Length(max: 4000, maxMessage: 'Description cannot exceed {{ limit }} characters.')]
    #[Assert\Regex(pattern: '/^[^<>]*$/u', message: 'Description contains invalid characters.')]
    private ?string $description = null;

    #[ORM\Column(name: 'activity_date', type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $activityDate = null;

    #[ORM\Column(name: 'start_time', type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $startTime = null;

    #[ORM\Column(name: 'end_time', type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $endTime = null;

    #[ORM\Column(name: 'location_name', length: 255, nullable: true)]
    #[Assert\Length(max: 255, maxMessage: 'Location name cannot exceed {{ limit }} characters.')]
    #[Assert\Regex(pattern: '/^[^<>]*$/u', message: 'Location name contains invalid characters.')]
    private ?string $locationName = null;

    #[ORM\Column(name: 'transport_type', length: 100, nullable: true)]
    #[Assert\Length(max: 100, maxMessage: 'Transport type cannot exceed {{ limit }} characters.')]
    #[Assert\Regex(pattern: '/^[^<>]*$/u', message: 'Transport type contains invalid characters.')]
    private ?string $transportType = null;

    #[ORM\Column(name: 'cost_amount', type: Types::FLOAT, nullable: true)]
    #[Assert\PositiveOrZero(message: 'Cost amount cannot be negative.')]
    #[Assert\LessThanOrEqual(value: 1000000000, message: 'Cost amount is too large.')]
    private ?float $costAmount = 0.0;

    #[ORM\Column(length: 10, nullable: true)]
    #[Assert\NotBlank(message: 'Currency is required.')]
    #[Assert\Regex(pattern: '/^[A-Z]{3,10}$/', message: 'Currency must be uppercase letters (e.g. USD).')]
    private ?string $currency = 'USD';

    #[ORM\Column(name: 'xp_earned', type: Types::INTEGER, nullable: true)]
    #[Assert\PositiveOrZero(message: 'XP earned cannot be negative.')]
    #[Assert\LessThanOrEqual(value: 1000000000, message: 'XP earned value is too large.')]
    private ?int $xpEarned = 0;

    #[ORM\Column(length: 50, nullable: true)]
    #[Assert\NotBlank(message: 'Status is required.')]
    #[Assert\Choice(choices: self::ALLOWED_STATUSES, message: 'Select a valid activity status.')]
    private ?string $status = 'PLANNED';

    #[ORM\Column(name: 'created_at', type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(name: 'updated_at', type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $updatedAt = null;

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
            $tripStartDate = $this->trip->getStartDate();
            $tripEndDate = $this->trip->getEndDate();
            if (
                $tripStartDate !== null
                && $tripEndDate !== null
                && ($this->activityDate < $tripStartDate || $this->activityDate > $tripEndDate)
            ) {
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

        if ($this->createdAt === null) {
            $this->createdAt = \DateTime::createFromImmutable($now);
        }

        $this->updatedAt = \DateTime::createFromImmutable($now);
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->updatedAt = new \DateTime();
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

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): static
    {
        $this->type = $type;

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

    public function getActivityDate(): ?\DateTimeInterface
    {
        return $this->activityDate;
    }

    public function setActivityDate(?\DateTimeInterface $activityDate): static
    {
        $this->activityDate = $activityDate;

        return $this;
    }

    public function getStartTime(): ?\DateTimeInterface
    {
        return $this->startTime;
    }

    public function setStartTime(?\DateTimeInterface $startTime): static
    {
        $this->startTime = $startTime;

        return $this;
    }

    public function getEndTime(): ?\DateTimeInterface
    {
        return $this->endTime;
    }

    public function setEndTime(?\DateTimeInterface $endTime): static
    {
        $this->endTime = $endTime;

        return $this;
    }

    public function getLocationName(): ?string
    {
        return $this->locationName;
    }

    public function setLocationName(?string $locationName): static
    {
        $this->locationName = $locationName;

        return $this;
    }

    public function getTransportType(): ?string
    {
        return $this->transportType;
    }

    public function setTransportType(?string $transportType): static
    {
        $this->transportType = $transportType;

        return $this;
    }

    public function getCostAmount(): ?float
    {
        return $this->costAmount;
    }

    public function setCostAmount(?float $costAmount): static
    {
        $this->costAmount = $costAmount;

        return $this;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(?string $currency): static
    {
        $this->currency = $currency;

        return $this;
    }

    public function getXpEarned(): ?int
    {
        return $this->xpEarned;
    }

    public function setXpEarned(?int $xpEarned): static
    {
        $this->xpEarned = $xpEarned;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
