<?php

namespace App\Entity;

use App\Entity\Trait\BlameableTrait;
use App\Repository\QuestRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: QuestRepository::class)]
#[ORM\Table(name: 'quests')]
#[ORM\HasLifecycleCallbacks]
class Quest
{
    use BlameableTrait;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 140)]
    #[Assert\NotBlank(normalizer: 'trim', message: 'Quest title is required.')]
    #[Assert\Length(min: 4, max: 140)]
    private string $title = '';

    #[ORM\Column(type: 'text', nullable: true)]
    #[Assert\Length(max: 1000)]
    private ?string $description = null;

    #[ORM\Column]
    #[Assert\Positive(message: 'Goal must be greater than 0.')]
    private int $goal = 1;

    #[ORM\Column(options: ['default' => 100])]
    #[Assert\PositiveOrZero(message: 'Reward XP must be positive or zero.')]
    private int $rewardXp = 100;

    #[ORM\Column(options: ['default' => true])]
    private bool $isActive = true;

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

    public function getGoal(): int
    {
        return $this->goal;
    }

    public function setGoal(int $goal): static
    {
        $this->goal = max(1, $goal);

        return $this;
    }

    public function getRewardXp(): int
    {
        return $this->rewardXp;
    }

    public function setRewardXp(int $rewardXp): static
    {
        $this->rewardXp = max(0, $rewardXp);

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

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $now = new \DateTimeImmutable();
        $this->createdAt = $now;
        $this->updatedAt = $now;
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }
}
