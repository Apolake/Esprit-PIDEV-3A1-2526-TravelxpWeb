<?php

namespace App\Entity;

use App\Entity\Trait\BlameableTrait;
use App\Repository\OfferRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: OfferRepository::class)]
#[ORM\Table(name: 'offer')]
#[ORM\HasLifecycleCallbacks]
class Offer
{
    use BlameableTrait;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'offers')]
    #[ORM\JoinColumn(name: 'property_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    #[Assert\NotNull(message: 'Property is required.')]
    private ?Property $property = null;

    #[ORM\Column(length: 180)]
    #[Assert\NotBlank(normalizer: 'trim', message: 'Offer title is required.')]
    #[Assert\Length(min: 3, max: 180)]
    private string $title = '';

    #[ORM\Column(type: 'text', nullable: true)]
    #[Assert\Length(max: 2000)]
    private ?string $description = null;

    #[ORM\Column(type: 'decimal', precision: 5, scale: 2)]
    #[Assert\Range(min: 1, max: 100, notInRangeMessage: 'Discount percentage must be between {{ min }} and {{ max }}.')]
    private string $discountPercentage = '1.00';

    #[ORM\Column(type: 'date_immutable')]
    #[Assert\NotNull(message: 'Start date is required.')]
    private \DateTimeImmutable $startDate;

    #[ORM\Column(type: 'date_immutable')]
    #[Assert\NotNull(message: 'End date is required.')]
    #[Assert\GreaterThanOrEqual(propertyPath: 'startDate', message: 'End date must be after or equal to start date.')]
    private \DateTimeImmutable $endDate;

    #[ORM\Column(options: ['default' => true])]
    private bool $isActive = true;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->startDate = new \DateTimeImmutable();
        $this->endDate = new \DateTimeImmutable();
        $this->createdAt = new \DateTimeImmutable();
    }

    private ?\DateTimeImmutable $updatedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProperty(): ?Property
    {
        return $this->property;
    }

    public function setProperty(?Property $property): static
    {
        $this->property = $property;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getDiscountPercentage(): string
    {
        return $this->discountPercentage;
    }

    public function setDiscountPercentage(string|float|int $discountPercentage): static
    {
        $value = is_string($discountPercentage) ? (float) $discountPercentage : (float) $discountPercentage;
        $this->discountPercentage = number_format(min(100, max(1, $value)), 2, '.', '');

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

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @internal Managed by lifecycle callbacks.
     */
    public function setCreatedAt(?\DateTimeInterface $createdAt): static
    {
        $this->createdAt = null === $createdAt ? null : \DateTimeImmutable::createFromInterface($createdAt);

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt ?? $this->createdAt;
    }

    /**
     * @internal Managed by lifecycle callbacks.
     */
    public function setUpdatedAt(?\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = null === $updatedAt ? null : \DateTimeImmutable::createFromInterface($updatedAt);

        return $this;
    }

    public function isValidToday(): bool
    {
        if ($this->startDate === null || $this->endDate === null) {
            return false;
        }

        $today = new \DateTimeImmutable('today');

        return $today >= $this->startDate && $today <= $this->endDate;
    }

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $this->createdAt ??= new \DateTimeImmutable();
    }
}
