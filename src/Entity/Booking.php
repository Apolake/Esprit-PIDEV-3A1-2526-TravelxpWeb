<?php

namespace App\Entity;

use App\Repository\BookingRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BookingRepository::class)]
#[ORM\Table(name: 'bookings')]
#[ORM\HasLifecycleCallbacks]
class Booking
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_CANCELLED = 'cancelled';
    public const PAYMENT_STATUS_UNPAID = 'unpaid';
    public const PAYMENT_STATUS_PAID = 'paid';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'bookings')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[Assert\NotNull(message: 'Property is required.')]
    private ?Property $property = null;

    #[ORM\Column]
    #[Assert\Positive(message: 'User ID must be greater than 0.')]
    private ?int $userId = null;

    #[ORM\Column(type: 'date_immutable')]
    #[Assert\NotNull(message: 'Booking date is required.')]
    #[Assert\GreaterThanOrEqual('today', message: 'Booking date cannot be before today.')]
    private ?\DateTimeImmutable $bookingDate = null;

    #[ORM\Column]
    #[Assert\Positive(message: 'Duration must be greater than 0.')]
    private ?int $duration = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    #[Assert\PositiveOrZero(message: 'Total price must be greater than or equal to 0.')]
    private string $totalPrice = '0.00';

    #[ORM\Column(length: 10, options: ['default' => 'USD'])]
    #[Assert\NotBlank(message: 'Currency is required.')]
    #[Assert\Regex(pattern: '/^[A-Z]{3,10}$/', message: 'Currency must be uppercase letters (e.g. USD).')]
    private string $currency = 'USD';

    #[ORM\Column(length: 20, options: ['default' => self::PAYMENT_STATUS_UNPAID])]
    #[Assert\NotBlank]
    #[Assert\Choice(choices: [self::PAYMENT_STATUS_UNPAID, self::PAYMENT_STATUS_PAID])]
    private string $paymentStatus = self::PAYMENT_STATUS_UNPAID;

    #[ORM\Column(length: 80, nullable: true)]
    #[Assert\Length(max: 80)]
    private ?string $paymentReference = null;

    /**
     * @var array<string, mixed>|null
     */
    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $pricingSnapshot = null;

    #[ORM\Column(length: 20, options: ['default' => self::STATUS_PENDING])]
    #[Assert\NotBlank]
    #[Assert\Choice(choices: [self::STATUS_PENDING, self::STATUS_CONFIRMED, self::STATUS_CANCELLED])]
    private string $status = self::STATUS_PENDING;

    /**
     * @var Collection<int, Service>
     */
    #[ORM\ManyToMany(targetEntity: Service::class, inversedBy: 'bookings')]
    #[ORM\JoinTable(name: 'booking_services')]
    private Collection $services;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->services = new ArrayCollection();
    }

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

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(?int $userId): static
    {
        $this->userId = null === $userId ? null : max(1, $userId);

        return $this;
    }

    public function getBookingDate(): ?\DateTimeImmutable
    {
        return $this->bookingDate;
    }

    public function setBookingDate(?\DateTimeInterface $bookingDate): static
    {
        $this->bookingDate = null === $bookingDate ? null : \DateTimeImmutable::createFromInterface($bookingDate);

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(?int $duration): static
    {
        $this->duration = null === $duration ? null : max(1, $duration);

        return $this;
    }

    public function getTotalPrice(): string
    {
        return $this->totalPrice;
    }

    public function setTotalPrice(string|float|int $totalPrice): static
    {
        $value = is_string($totalPrice) ? (float) $totalPrice : (float) $totalPrice;
        $this->totalPrice = number_format(max(0, $value), 2, '.', '');

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

    public function getPaymentStatus(): string
    {
        return $this->paymentStatus;
    }

    public function setPaymentStatus(string $paymentStatus): static
    {
        $normalized = strtolower(trim($paymentStatus));
        if (!in_array($normalized, [self::PAYMENT_STATUS_UNPAID, self::PAYMENT_STATUS_PAID], true)) {
            $normalized = self::PAYMENT_STATUS_UNPAID;
        }

        $this->paymentStatus = $normalized;

        return $this;
    }

    public function getPaymentReference(): ?string
    {
        return $this->paymentReference;
    }

    public function setPaymentReference(?string $paymentReference): static
    {
        $this->paymentReference = null === $paymentReference ? null : trim($paymentReference);

        return $this;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getPricingSnapshot(): ?array
    {
        return $this->pricingSnapshot;
    }

    /**
     * @param array<string, mixed>|null $pricingSnapshot
     */
    public function setPricingSnapshot(?array $pricingSnapshot): static
    {
        $this->pricingSnapshot = $pricingSnapshot;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $normalized = strtolower(trim($status));
        if (!in_array($normalized, [self::STATUS_PENDING, self::STATUS_CONFIRMED, self::STATUS_CANCELLED], true)) {
            $normalized = self::STATUS_PENDING;
        }

        $this->status = $normalized;

        return $this;
    }

    /**
     * @return Collection<int, Service>
     */
    public function getServices(): Collection
    {
        return $this->services;
    }

    public function addService(Service $service): static
    {
        if (!$this->services->contains($service)) {
            $this->services->add($service);
            $service->addBooking($this);
        }

        return $this;
    }

    public function removeService(Service $service): static
    {
        if ($this->services->removeElement($service)) {
            $service->removeBooking($this);
        }

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): static
    {
        $this->createdAt = null === $createdAt ? null : \DateTimeImmutable::createFromInterface($createdAt);

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = null === $updatedAt ? null : \DateTimeImmutable::createFromInterface($updatedAt);

        return $this;
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    public function isPaid(): bool
    {
        return $this->paymentStatus === self::PAYMENT_STATUS_PAID;
    }

    public function isInPast(): bool
    {
        if ($this->bookingDate === null) {
            return false;
        }

        return $this->bookingDate < new \DateTimeImmutable('today');
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
}
