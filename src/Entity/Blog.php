<?php

namespace App\Entity;

use App\Entity\Trait\BlameableTrait;
use App\Repository\BlogRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BlogRepository::class)]
#[ORM\Table(name: 'blogs')]
#[ORM\HasLifecycleCallbacks]
class Blog
{
    use BlameableTrait;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Assert\NotBlank(normalizer: 'trim', message: 'Title is required.')]
    #[Assert\Length(min: 3, max: 180)]
    private string $title = '';

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank(normalizer: 'trim', message: 'Content is required.')]
    #[Assert\Length(min: 10, max: 10000)]
    private string $content = '';

    #[ORM\Column(length: 500, nullable: true)]
    #[Assert\Length(max: 500)]
    #[Assert\Url(message: 'Image must be a valid URL.')]
    private ?string $imageUrl = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $author = null;

    /**
     * @var Collection<int, Comment>
     */
    #[ORM\OneToMany(mappedBy: 'blog', targetEntity: Comment::class, orphanRemoval: true, cascade: ['persist', 'remove'])]
    #[ORM\OrderBy(['createdAt' => 'DESC'])]
    private Collection $comments;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class)]
    #[ORM\JoinTable(name: 'blog_likes')]
    private Collection $likedByUsers;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class)]
    #[ORM\JoinTable(name: 'blog_dislikes')]
    private Collection $dislikedByUsers;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $publishedAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->likedByUsers = new ArrayCollection();
        $this->dislikedByUsers = new ArrayCollection();
        $this->publishedAt = new \DateTimeImmutable();
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

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(?string $content): static
    {
        $this->content = null === $content ? null : trim($content);

        return $this;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(?string $imageUrl): static
    {
        $trimmed = null === $imageUrl ? null : trim($imageUrl);
        $this->imageUrl = '' === $trimmed ? null : $trimmed;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    /**
     * @internal Set by event subscriber or controller action, not by form handling.
     */
    public function setAuthor(?User $author): static
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setBlog($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        $this->comments->removeElement($comment);

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getLikedByUsers(): Collection
    {
        return $this->likedByUsers;
    }

    /**
     * @return Collection<int, User>
     */
    public function getDislikedByUsers(): Collection
    {
        return $this->dislikedByUsers;
    }

    public function hasLikedBy(User $user): bool
    {
        return $this->likedByUsers->contains($user);
    }

    public function hasDislikedBy(User $user): bool
    {
        return $this->dislikedByUsers->contains($user);
    }

    public function addLikeBy(User $user): static
    {
        if (!$this->likedByUsers->contains($user)) {
            $this->likedByUsers->add($user);
        }

        if ($this->dislikedByUsers->contains($user)) {
            $this->dislikedByUsers->removeElement($user);
        }

        return $this;
    }

    public function addDislikeBy(User $user): static
    {
        if (!$this->dislikedByUsers->contains($user)) {
            $this->dislikedByUsers->add($user);
        }

        if ($this->likedByUsers->contains($user)) {
            $this->likedByUsers->removeElement($user);
        }

        return $this;
    }

    public function getLikesCount(): int
    {
        return $this->likedByUsers->count();
    }

    public function getDislikesCount(): int
    {
        return $this->dislikedByUsers->count();
    }

    public function getPublishedAt(): \DateTimeImmutable
    {
        return $this->publishedAt;
    }

    /**
     * @internal Managed by lifecycle callbacks.
     */
    public function setPublishedAt(?\DateTimeInterface $publishedAt): static
    {
        $this->publishedAt = null === $publishedAt ? null : \DateTimeImmutable::createFromInterface($publishedAt);

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

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * @internal Managed by lifecycle callbacks.
     */
    public function setUpdatedAt(?\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = null === $updatedAt ? null : \DateTimeImmutable::createFromInterface($updatedAt);

        return $this;
    }

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $now = new \DateTimeImmutable();
        $this->createdAt ??= $now;
        $this->updatedAt = $now;
        $this->publishedAt ??= $now;
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }
}
