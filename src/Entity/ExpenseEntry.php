<?php

namespace App\Entity;

use App\Entity\Trait\BlameableTrait;
use App\Repository\ExpenseEntryRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ExpenseEntryRepository::class)]
#[ORM\Table(name: 'expense_entries')]
#[ORM\HasLifecycleCallbacks]
class ExpenseEntry
{
    use BlameableTrait;
    public const CATEGORY_TRANSPORT = 'transport';
    public const CATEGORY_HOTEL = 'hotel';
    public const CATEGORY_FOOD = 'food';
    public const CATEGORY_ACTIVITIES = 'activities';
    public const CATEGORY_SHOPPING = 'shopping';
    public const CATEGORY_MISC = 'misc';

    /**
     * @var list<string>
     */
    public const ALLOWED_CATEGORIES = [
        self::CATEGORY_TRANSPORT,
        self::CATEGORY_HOTEL,
        self::CATEGORY_FOOD,
        self::CATEGORY_ACTIVITIES,
        self::CATEGORY_SHOPPING,
        self::CATEGORY_MISC,
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'expenses')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Budget $budget = null;

    #[ORM\Column(length: 180)]
    #[Assert\NotBlank(normalizer: 'trim', message: 'Expense title is required.')]
    #[Assert\Length(min: 2, max: 180)]
    private string $title = '';

    #[ORM\Column(length: 30)]
    #[Assert\NotBlank(message: 'Category is required.')]
    #[Assert\Choice(choices: self::ALLOWED_CATEGORIES, message: 'Select a valid category.')]
    private string $category = self::CATEGORY_MISC;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    #[Assert\Positive(message: 'Amount must be greater than 0.')]
    private string $amount = '0.00';

    #[ORM\Column(type: 'date_immutable')]
    #[Assert\NotNull(message: 'Expense date is required.')]
    private \DateTimeImmutable $expenseDate;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Assert\Length(max: 2000)]
    private ?string $note = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $this->expenseDate = new \DateTimeImmutable();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(?string $title): static
    {
        $this->title = null === $title ? null : trim($title);

        return $this;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function setCategory(?string $category): static
    {
        $normalized = strtolower(trim((string) $category));
        if (!in_array($normalized, self::ALLOWED_CATEGORIES, true)) {
            $normalized = self::CATEGORY_MISC;
        }

        $this->category = $normalized;

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

    public function getExpenseDate(): \DateTimeImmutable
    {
        return $this->expenseDate;
    }

    public function setExpenseDate(?\DateTimeInterface $expenseDate): static
    {
        $this->expenseDate = null === $expenseDate ? null : \DateTimeImmutable::createFromInterface($expenseDate);

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): static
    {
        $this->note = $note;

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
