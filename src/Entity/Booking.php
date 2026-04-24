<?php

namespace App\Entity;

use App\Repository\BookingRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BookingRepository::class)]
#[ORM\Table(name: 'booking')]
#[ORM\HasLifecycleCallbacks]
class Booking
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_CANCELLED = 'cancelled';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'booking_id')]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'bookings')]
    #[ORM\JoinColumn(name: 'property_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    #[Assert\NotNull(message: 'Property is required.')]
    private ?Property $property = null;

    #[ORM\Column(name: 'user_id')]
    #[Assert\Positive(message: 'User ID must be greater than 0.')]
    private ?int $userId = null;

    #[ORM\Column(name: 'booking_date', type: 'date_immutable')]
    #[Assert\NotNull(message: 'Booking date is required.')]
    #[Assert\GreaterThanOrEqual('today', message: 'Booking date cannot be before today.')]
    private ?\DateTimeImmutable $bookingDate = null;

    #[ORM\Column(name: 'duration')]
    #[Assert\Positive(message: 'Duration must be greater than 0.')]
    private ?int $duration = null;

    #[ORM\Column(name: 'total_price', type: 'decimal', precision: 10, scale: 2)]
    #[Assert\PositiveOrZero(message: 'Total price must be greater than or equal to 0.')]
    private string $totalPrice = '0.00';

    #[ORM\Column(name: 'booking_status', length: 20, options: ['default' => self::STATUS_PENDING])]
    #[Assert\NotBlank]
    #[Assert\Choice(choices: [self::STATUS_PENDING, self::STATUS_CONFIRMED, self::STATUS_CANCELLED])]
    private string $status = self::STATUS_PENDING;

    /**
     * @var Collection<int, Service>
     */
    #[ORM\ManyToMany(targetEntity: Service::class, inversedBy: 'bookings')]
    #[ORM\JoinTable(name: 'booking_services')]
    #[ORM\JoinColumn(name: 'booking_id', referencedColumnName: 'booking_id')]
    #[ORM\InverseJoinColumn(name: 'service_id', referencedColumnName: 'service_id')]
    private Collection $services;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private ?\DateTimeImmutable $createdAt = null;

    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * @var Collection<int, Payment>
     */
    #[ORM\OneToMany(targetEntity: Payment::class, mappedBy: 'booking')]
    private Collection $payments;

    public function __construct()
    {
        $this->services = new ArrayCollection();
        $this->payments = new ArrayCollection();
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

    /**
     * @return Collection<int, Payment>
     */
    public function getPayments(): Collection
    {
        return $this->payments;
    }

    public function addPayment(Payment $payment): static
    {
        if (!$this->payments->contains($payment)) {
            $this->payments->add($payment);
            $payment->setBooking($this);
        }

        return $this;
    }

    public function removePayment(Payment $payment): static
    {
        if ($this->payments->removeElement($payment) && $payment->getBooking() === $this) {
            $payment->setBooking(null);
        }

        return $this;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): static
    {
        $this->createdAt = null === $createdAt ? null : \DateTimeImmutable::createFromInterface($createdAt);

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt ?? $this->createdAt;
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

    public function isInPast(): bool
    {
        if ($this->bookingDate === null) {
            return false;
        }

        return $this->bookingDate < new \DateTimeImmutable('today');
    }

    public function hasSuccessfulPayment(): bool
    {
        foreach ($this->payments as $payment) {
            if ($payment->isSuccessful()) {
                return true;
            }
        }

        return false;
    }

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $this->createdAt ??= new \DateTimeImmutable();
    }
}
