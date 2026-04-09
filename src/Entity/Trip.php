<?php

namespace App\Entity;

use App\Repository\TripRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
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
    #[ORM\Column(type: Types::BIGINT)]
    private ?int $id = null;

    #[ORM\Column(name: 'user_id', type: Types::INTEGER, nullable: true)]
    #[Assert\Positive(message: 'User ID must be a positive number.')]
    private ?int $userId = null;

    #[ORM\Column(name: 'trip_name', length: 255)]
    #[Assert\NotBlank(message: 'Trip name is required.')]
    #[Assert\Length(min: 2, max: 255, minMessage: 'Trip name must be at least {{ limit }} characters.', maxMessage: 'Trip name cannot exceed {{ limit }} characters.')]
    #[Assert\Regex(pattern: '/^[^<>]+$/u', message: 'Trip name contains invalid characters.')]
    private ?string $tripName = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(max: 255, maxMessage: 'Origin cannot exceed {{ limit }} characters.')]
    #[Assert\Regex(pattern: '/^[^<>]*$/u', message: 'Origin contains invalid characters.')]
    private ?string $origin = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(max: 255, maxMessage: 'Destination cannot exceed {{ limit }} characters.')]
    #[Assert\Regex(pattern: '/^[^<>]*$/u', message: 'Destination contains invalid characters.')]
    private ?string $destination = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Assert\Length(max: 4000, maxMessage: 'Description cannot exceed {{ limit }} characters.')]
    #[Assert\Regex(pattern: '/^[^<>]*$/u', message: 'Description contains invalid characters.')]
    private ?string $description = null;

    #[ORM\Column(name: 'start_date', type: Types::DATE_MUTABLE)]
    #[Assert\NotNull(message: 'Start date is required.')]
    private ?\DateTimeInterface $startDate = null;

    #[ORM\Column(name: 'end_date', type: Types::DATE_MUTABLE)]
    #[Assert\NotNull(message: 'End date is required.')]
    private ?\DateTimeInterface $endDate = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Assert\NotBlank(message: 'Status is required.')]
    #[Assert\Choice(choices: self::ALLOWED_STATUSES, message: 'Select a valid trip status.')]
    private ?string $status = 'PLANNED';

    #[ORM\Column(name: 'budget_amount', type: Types::FLOAT, nullable: true)]
    #[Assert\PositiveOrZero(message: 'Budget amount cannot be negative.')]
    #[Assert\LessThanOrEqual(value: 1000000000, message: 'Budget amount is too large.')]
    private ?float $budgetAmount = 0.0;

    #[ORM\Column(length: 10, nullable: true)]
    #[Assert\NotBlank(message: 'Currency is required.')]
    #[Assert\Regex(pattern: '/^[A-Z]{3,10}$/', message: 'Currency must be uppercase letters (e.g. USD).')]
    private ?string $currency = 'USD';

    #[ORM\Column(name: 'total_expenses', type: Types::FLOAT, nullable: true)]
    #[Assert\PositiveOrZero(message: 'Total expenses cannot be negative.')]
    #[Assert\LessThanOrEqual(value: 1000000000, message: 'Total expenses value is too large.')]
    private ?float $totalExpenses = 0.0;

    #[ORM\Column(name: 'total_xp_earned', type: Types::INTEGER, nullable: true)]
    #[Assert\PositiveOrZero(message: 'Total XP earned cannot be negative.')]
    #[Assert\LessThanOrEqual(value: 1000000000, message: 'Total XP earned value is too large.')]
    private ?int $totalXpEarned = 0;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Assert\Length(max: 4000, maxMessage: 'Notes cannot exceed {{ limit }} characters.')]
    #[Assert\Regex(pattern: '/^[^<>]*$/u', message: 'Notes contain invalid characters.')]
    private ?string $notes = null;

    #[ORM\Column(name: 'cover_image_url', length: 255, nullable: true)]
    #[Assert\Length(max: 255, maxMessage: 'Cover image URL cannot exceed {{ limit }} characters.')]
    #[Assert\Url(message: 'Cover image URL is not valid.')]
    private ?string $coverImageUrl = null;

    #[ORM\Column(name: 'created_at', type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(name: 'updated_at', type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\Column(name: 'parent_id', type: Types::BIGINT, nullable: true)]
    #[Assert\Positive(message: 'Parent trip ID must be a positive number.')]
    private ?int $parentId = null;

    /**
     * @var Collection<int, Activity>
     */
    #[ORM\OneToMany(targetEntity: Activity::class, mappedBy: 'trip')]
    private Collection $activities;

    public function __construct()
    {
        $this->activities = new ArrayCollection();
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

    public function __toString(): string
    {
        return sprintf('%s (#%d)', $this->tripName ?? 'Trip', $this->id ?? 0);
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

    public function getTripName(): ?string
    {
        return $this->tripName;
    }

    public function setTripName(string $tripName): static
    {
        $this->tripName = $tripName;

        return $this;
    }

    public function getOrigin(): ?string
    {
        return $this->origin;
    }

    public function setOrigin(?string $origin): static
    {
        $this->origin = $origin;

        return $this;
    }

    public function getDestination(): ?string
    {
        return $this->destination;
    }

    public function setDestination(?string $destination): static
    {
        $this->destination = $destination;

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

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): static
    {
        $this->endDate = $endDate;

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

    public function getBudgetAmount(): ?float
    {
        return $this->budgetAmount;
    }

    public function setBudgetAmount(?float $budgetAmount): static
    {
        $this->budgetAmount = $budgetAmount;

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

    public function getTotalExpenses(): ?float
    {
        return $this->totalExpenses;
    }

    public function setTotalExpenses(?float $totalExpenses): static
    {
        $this->totalExpenses = $totalExpenses;

        return $this;
    }

    public function getTotalXpEarned(): ?int
    {
        return $this->totalXpEarned;
    }

    public function setTotalXpEarned(?int $totalXpEarned): static
    {
        $this->totalXpEarned = $totalXpEarned;

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
        $this->coverImageUrl = $coverImageUrl;

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

    public function getParentId(): ?int
    {
        return $this->parentId;
    }

    public function setParentId(?int $parentId): static
    {
        $this->parentId = $parentId;

        return $this;
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
        if ($this->activities->removeElement($activity)) {
            if ($activity->getTrip() === $this) {
                $activity->setTrip(null);
            }
        }

        return $this;
    }
}
