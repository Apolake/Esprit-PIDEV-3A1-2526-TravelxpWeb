<?php

namespace App\Entity;

use App\Entity\Trait\BlameableTrait;
use App\Repository\PaymentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PaymentRepository::class)]
#[ORM\Table(name: 'payments')]
#[ORM\HasLifecycleCallbacks]
#[ORM\UniqueConstraint(name: 'uniq_payment_intent', fields: ['stripePaymentIntentId'])]
class Payment
{
    use BlameableTrait;
    public const STATUS_REQUIRES_PAYMENT_METHOD = 'requires_payment_method';
    public const STATUS_REQUIRES_ACTION = 'requires_action';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_SUCCEEDED = 'succeeded';
    public const STATUS_CANCELED = 'canceled';
    public const STATUS_FAILED = 'failed';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'payments')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'payments')]
    #[ORM\JoinColumn(name: 'booking_id', referencedColumnName: 'booking_id', nullable: true, onDelete: 'SET NULL')]
    private ?Booking $booking = null;

    #[ORM\ManyToOne(inversedBy: 'payments')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Budget $budget = null;

    #[ORM\Column(length: 191)]
    #[Assert\NotBlank]
    private string $stripePaymentIntentId = '';

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    #[Assert\PositiveOrZero]
    private string $amount = '0.00';

    #[ORM\Column(length: 10)]
    #[Assert\NotBlank]
    private string $currency = 'USD';

    #[ORM\Column(length: 40)]
    #[Assert\NotBlank]
    private string $status = self::STATUS_REQUIRES_PAYMENT_METHOD;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $failureMessage = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getBooking(): ?Booking
    {
        return $this->booking;
    }

    public function setBooking(?Booking $booking): static
    {
        $this->booking = $booking;

        return $this;
    }

    public function getBudget(): ?Budget
    {
        return $this->budget;
    }

    public function setBudget(?Budget $budget): static
    {
        $this->budget = $budget;

        return $this;
    }

    public function getStripePaymentIntentId(): string
    {
        return $this->stripePaymentIntentId;
    }

    public function setStripePaymentIntentId(?string $stripePaymentIntentId): static
    {
        $this->stripePaymentIntentId = null === $stripePaymentIntentId ? null : trim($stripePaymentIntentId);

        return $this;
    }

    public function getAmount(): string
    {
        return $this->amount;
    }

    public function setAmount(string|float|int $amount): static
    {
        $value = is_string($amount) ? (float) $amount : (float) $amount;
        $this->amount = number_format(max(0, $value), 2, '.', '');

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

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(?string $status): static
    {
        $normalized = strtolower(trim((string) $status));
        $this->status = '' === $normalized ? self::STATUS_FAILED : $normalized;

        return $this;
    }

    public function getFailureMessage(): ?string
    {
        return $this->failureMessage;
    }

    public function setFailureMessage(?string $failureMessage): static
    {
        $this->failureMessage = $failureMessage;

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

    public function isSuccessful(): bool
    {
        return $this->status === self::STATUS_SUCCEEDED;
    }

    public function isTerminal(): bool
    {
        return in_array($this->status, [self::STATUS_SUCCEEDED, self::STATUS_CANCELED, self::STATUS_FAILED], true);
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
