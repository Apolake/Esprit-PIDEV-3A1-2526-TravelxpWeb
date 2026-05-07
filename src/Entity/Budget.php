<?php

namespace App\Entity;

use App\Entity\Trait\BlameableTrait;
use App\Repository\BudgetRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: BudgetRepository::class)]
#[ORM\Table(name: 'budgets')]
#[ORM\HasLifecycleCallbacks]
class Budget
{
    use BlameableTrait;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'budgets')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $user = null;

    #[ORM\Column(length: 180)]
    #[Assert\NotBlank(normalizer: 'trim', message: 'Title is required.')]
    #[Assert\Length(min: 2, max: 180)]
    private string $title = '';

    #[ORM\Column(length: 180)]
    #[Assert\NotBlank(normalizer: 'trim', message: 'Destination is required.')]
    #[Assert\Length(min: 2, max: 180)]
    private string $destination = '';

    #[ORM\Column(type: 'date_immutable')]
    #[Assert\NotNull(message: 'Start date is required.')]
    private \DateTimeImmutable $startDate;

    #[ORM\Column(type: 'date_immutable')]
    #[Assert\NotNull(message: 'End date is required.')]
    private \DateTimeImmutable $endDate;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    #[Assert\Positive(message: 'Total planned budget must be greater than 0.')]
    private string $plannedAmount = '0.00';

    #[ORM\Column(length: 10, options: ['default' => 'USD'])]
    #[Assert\NotBlank(message: 'Currency is required.')]
    #[Assert\Regex(pattern: '/^[A-Z]{3,10}$/', message: 'Currency must be uppercase letters.')]
    private string $currency = 'USD';

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    /**
     * @var Collection<int, ExpenseEntry>
     */
    #[ORM\OneToMany(targetEntity: ExpenseEntry::class, mappedBy: 'budget', orphanRemoval: true, cascade: ['persist'])]
    #[ORM\OrderBy(['expenseDate' => 'DESC', 'id' => 'DESC'])]
    private Collection $expenses;

    /**
     * @var Collection<int, Payment>
     */
    #[ORM\OneToMany(targetEntity: Payment::class, mappedBy: 'budget')]
    private Collection $payments;

    public function __construct()
    {
        $this->expenses = new ArrayCollection();
        $this->payments = new ArrayCollection();
        $this->startDate = new \DateTimeImmutable();
        $this->endDate = new \DateTimeImmutable();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    #[Assert\Callback]
    public function validateDates(ExecutionContextInterface $context): void
    {
        if ($this->startDate !== null && $this->endDate !== null && $this->endDate < $this->startDate) {
            $context->buildViolation('End date must be on or after start date.')
                ->atPath('endDate')
                ->addViolation();
        }
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

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(?string $title): static
    {
        $this->title = null === $title ? null : trim($title);

        return $this;
    }

    public function getDestination(): string
    {
        return $this->destination;
    }

    public function setDestination(?string $destination): static
    {
        $this->destination = null === $destination ? null : trim($destination);

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

    public function getPlannedAmount(): string
    {
        return $this->plannedAmount;
    }

    public function setPlannedAmount(string|float|int $plannedAmount): static
    {
        $value = is_string($plannedAmount) ? (float) $plannedAmount : (float) $plannedAmount;
        $this->plannedAmount = number_format(max(0, $value), 2, '.', '');

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

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * @return Collection<int, ExpenseEntry>
     */
    public function getExpenses(): Collection
    {
        return $this->expenses;
    }

    public function addExpense(ExpenseEntry $expense): static
    {
        if (!$this->expenses->contains($expense)) {
            $this->expenses->add($expense);
            $expense->setBudget($this);
        }

        return $this;
    }

    public function removeExpense(ExpenseEntry $expense): static
    {
        if ($this->expenses->removeElement($expense) && $expense->getBudget() === $this) {
            $expense->setBudget(null);
        }

        return $this;
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
            $payment->setBudget($this);
        }

        return $this;
    }

    public function removePayment(Payment $payment): static
    {
        if ($this->payments->removeElement($payment) && $payment->getBudget() === $this) {
            $payment->setBudget(null);
        }

        return $this;
    }

    public function getSpentAmount(): string
    {
        $spent = 0.0;
        foreach ($this->expenses as $expense) {
            $spent += (float) $expense->getAmount();
        }

        return number_format(max(0, $spent), 2, '.', '');
    }

    public function getRemainingAmount(): string
    {
        $remaining = (float) $this->plannedAmount - (float) $this->getSpentAmount();

        return number_format($remaining, 2, '.', '');
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
