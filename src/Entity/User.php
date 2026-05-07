<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'users')]
#[ORM\UniqueConstraint(name: 'uniq_users_email', fields: ['email'])]
#[ORM\UniqueConstraint(name: 'uniq_users_username', fields: ['username'])]
#[ORM\UniqueConstraint(name: 'uniq_users_firebase_uid', fields: ['firebaseUid'])]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email.')]
#[UniqueEntity(fields: ['username'], message: 'This username is already taken.')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(normalizer: 'trim', message: 'Username is required.')]
    #[Assert\Length(min: 3, max: 50, minMessage: 'Username must be at least {{ limit }} characters.')]
    #[Assert\Regex(pattern: '/^[a-zA-Z0-9_.-]+$/', message: 'Username can contain only letters, numbers, dots, underscores and dashes.')]
    private ?string $username = null;

    #[ORM\Column(length: 180)]
    #[Assert\NotBlank(normalizer: 'trim', message: 'Email is required.')]
    #[Assert\Email(message: 'Please enter a valid email address.')]
    private ?string $email = null;
    

    /**
     * @var list<string>
     */
    #[ORM\Column]
    private array $roles = ['ROLE_USER'];

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(type: 'date_immutable')]
    #[Assert\NotNull(message: 'Birthday is required.')]
    #[Assert\LessThanOrEqual('today', message: 'Birthday cannot be in the future.')]
    #[Assert\LessThanOrEqual('-12 years', message: 'You must be at least 12 years old.')]
    private ?\DateTimeImmutable $birthday = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Assert\Length(max: 500, maxMessage: 'Bio cannot exceed {{ limit }} characters.')]
    private ?string $bio = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(max: 255)]
    private ?string $profileImage = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, options: ['default' => 0])]
    private string $balance = '0.00';

    #[ORM\Column(options: ['default' => 0])]
    private int $xp = 0;

    #[ORM\Column(options: ['default' => 1])]
    private int $level = 1;

    #[ORM\Column(options: ['default' => 0])]
    private int $streak = 0;

    #[ORM\Column(options: ['default' => false])]
    private bool $faceRegistered = false;

    #[ORM\Column(options: ['default' => false])]
    private bool $totpEnabled = false;

    #[ORM\Column(length: 64, nullable: true)]
    private ?string $totpSecret = null;

    /**
     * @var list<string>
     */
    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $totpRecoveryCodes = [];

    #[ORM\Column(length: 128, nullable: true)]
    private ?string $firebaseUid = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * @var Collection<int, Budget>
     */
    #[ORM\OneToMany(targetEntity: Budget::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $budgets;

    /**
     * @var Collection<int, Payment>
     */
    #[ORM\OneToMany(targetEntity: Payment::class, mappedBy: 'user')]
    private Collection $payments;

    public function __construct()
    {
        $this->budgets = new ArrayCollection();
        $this->payments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): static
    {
        $this->username = null === $username ? null : trim($username);

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = null === $email ? null : mb_strtolower(trim($email));

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_values(array_unique($roles));
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $normalized = array_values(array_unique(array_filter($roles)));
        if (!in_array('ROLE_USER', $normalized, true)) {
            $normalized[] = 'ROLE_USER';
        }

        $this->roles = $normalized;

        return $this;
    }

    public function getPrimaryRole(): string
    {
        if (in_array('ROLE_ADMIN', $this->getRoles(), true)) {
            return 'ROLE_ADMIN';
        }

        return 'ROLE_USER';
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function eraseCredentials(): void
    {
    }

    public function getBirthday(): ?\DateTimeImmutable
    {
        return $this->birthday;
    }

    public function setBirthday(?\DateTimeInterface $birthday): static
    {
        $this->birthday = null === $birthday ? null : \DateTimeImmutable::createFromInterface($birthday);

        return $this;
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function setBio(?string $bio): static
    {
        $this->bio = $bio;

        return $this;
    }

    public function getProfileImage(): ?string
    {
        return $this->profileImage;
    }

    public function setProfileImage(?string $profileImage): static
    {
        $this->profileImage = $profileImage;

        return $this;
    }

    public function getBalance(): string
    {
        return $this->balance;
    }

    public function setBalance(string $balance): static
    {
        $this->balance = $balance;

        return $this;
    }

    public function getXp(): int
    {
        return $this->xp;
    }

    public function setXp(int $xp): static
    {
        $this->xp = max(0, $xp);

        return $this;
    }

    public function getLevel(): int
    {
        return $this->level;
    }

    public function setLevel(int $level): static
    {
        $this->level = max(1, $level);

        return $this;
    }

    public function getStreak(): int
    {
        return $this->streak;
    }

    public function setStreak(int $streak): static
    {
        $this->streak = max(0, $streak);

        return $this;
    }

    public function isFaceRegistered(): bool
    {
        return $this->faceRegistered;
    }

    public function setFaceRegistered(bool $faceRegistered): static
    {
        $this->faceRegistered = $faceRegistered;

        return $this;
    }

    public function isTotpEnabled(): bool
    {
        return $this->totpEnabled;
    }

    public function setTotpEnabled(bool $totpEnabled): static
    {
        $this->totpEnabled = $totpEnabled;

        return $this;
    }

    public function getTotpSecret(): ?string
    {
        return $this->totpSecret;
    }

    public function setTotpSecret(?string $totpSecret): static
    {
        $this->totpSecret = $totpSecret;

        return $this;
    }

    /**
     * @return list<string>
     */
    public function getTotpRecoveryCodes(): array
    {
        $codes = $this->totpRecoveryCodes ?? [];

        return array_values(array_filter($codes, static fn (mixed $code): bool => is_string($code) && '' !== trim($code)));
    }

    /**
     * @param list<string> $totpRecoveryCodes
     */
    public function setTotpRecoveryCodes(array $totpRecoveryCodes): static
    {
        $this->totpRecoveryCodes = array_values(array_filter($totpRecoveryCodes, static fn (mixed $code): bool => is_string($code) && '' !== trim($code)));

        return $this;
    }

    public function getFirebaseUid(): ?string
    {
        return $this->firebaseUid;
    }

    public function setFirebaseUid(?string $firebaseUid): static
    {
        $normalized = null === $firebaseUid ? null : trim($firebaseUid);
        $this->firebaseUid = '' === (string) $normalized ? null : $normalized;

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
     * @return Collection<int, Budget>
     */
    public function getBudgets(): Collection
    {
        return $this->budgets;
    }

    public function addBudget(Budget $budget): static
    {
        if (!$this->budgets->contains($budget)) {
            $this->budgets->add($budget);
            $budget->setUser($this);
        }

        return $this;
    }

    public function removeBudget(Budget $budget): static
    {
        if ($this->budgets->removeElement($budget) && $budget->getUser() === $this) {
            $budget->setUser(null);
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
            $payment->setUser($this);
        }

        return $this;
    }

    public function removePayment(Payment $payment): static
    {
        if ($this->payments->removeElement($payment) && $payment->getUser() === $this) {
            $payment->setUser(null);
        }

        return $this;
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

    public function __serialize(): array
    {
        $data = (array) $this;
        $data["\0".self::class."\0password"] = hash('crc32c', $this->password ?? '');

        return $data;
    }
}
