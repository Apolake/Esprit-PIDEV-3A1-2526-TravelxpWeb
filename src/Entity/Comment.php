<?php

namespace App\Entity;

use App\Entity\Trait\BlameableTrait;
use App\Repository\CommentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
#[ORM\Table(name: 'blog_comments')]
#[ORM\HasLifecycleCallbacks]
class Comment
{
    use BlameableTrait;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Blog $blog = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $author = null;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank(normalizer: 'trim', message: 'Comment content is required.')]
    #[Assert\Length(min: 2, max: 3000)]
    private string $content = '';

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class)]
    #[ORM\JoinTable(name: 'blog_comment_likes')]
    private Collection $likedByUsers;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class)]
    #[ORM\JoinTable(name: 'blog_comment_dislikes')]
    private Collection $dislikedByUsers;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $this->likedByUsers = new ArrayCollection();
        $this->dislikedByUsers = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBlog(): ?Blog
    {
        return $this->blog;
    }

    public function setBlog(?Blog $blog): static
    {
        $this->blog = $blog;

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

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(?string $content): static
    {
        $this->content = null === $content ? null : trim($content);

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
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }
}
