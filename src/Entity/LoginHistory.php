<?php

namespace App\Entity;

use App\Repository\LoginHistoryRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LoginHistoryRepository::class)]
#[ORM\Table(name: 'login_history')]
#[ORM\HasLifecycleCallbacks]
class LoginHistory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $user = null;

    #[ORM\Column(length: 45)]
    private string $ipAddress = 'unknown';

    #[ORM\Column(length: 120, nullable: true)]
    private ?string $country = null;

    #[ORM\Column(length: 120, nullable: true)]
    private ?string $region = null;

    #[ORM\Column(length: 120, nullable: true)]
    private ?string $city = null;

    #[ORM\Column(length: 191, nullable: true)]
    private ?string $isp = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $latitude = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $longitude = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $userAgent = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $loginAt;

    public function __construct()
    {
        $this->loginAt = new \DateTimeImmutable();
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

    public function getIpAddress(): string
    {
        return $this->ipAddress;
    }

    public function setIpAddress(?string $ipAddress): static
    {
        $normalized = trim((string) $ipAddress);
        $this->ipAddress = '' === $normalized ? 'unknown' : mb_substr($normalized, 0, 45);

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): static
    {
        $this->country = $this->normalizeNullableString($country, 120);

        return $this;
    }

    public function getRegion(): ?string
    {
        return $this->region;
    }

    public function setRegion(?string $region): static
    {
        $this->region = $this->normalizeNullableString($region, 120);

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): static
    {
        $this->city = $this->normalizeNullableString($city, 120);

        return $this;
    }

    public function getIsp(): ?string
    {
        return $this->isp;
    }

    public function setIsp(?string $isp): static
    {
        $this->isp = $this->normalizeNullableString($isp, 191);

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(?float $latitude): static
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(?float $longitude): static
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getUserAgent(): ?string
    {
        return $this->userAgent;
    }

    public function setUserAgent(?string $userAgent): static
    {
        $this->userAgent = $this->normalizeNullableString($userAgent, 255);

        return $this;
    }

    public function getLoginAt(): \DateTimeImmutable
    {
        return $this->loginAt;
    }

    /**
     * @internal Managed by lifecycle callbacks.
     */
    public function setLoginAt(?\DateTimeImmutable $loginAt): static
    {
        $this->loginAt = $loginAt;

        return $this;
    }

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $this->loginAt ??= new \DateTimeImmutable();
    }

    private function normalizeNullableString(?string $value, int $maxLength): ?string
    {
        $normalized = trim((string) $value);
        if ('' === $normalized) {
            return null;
        }

        return mb_substr($normalized, 0, $maxLength);
    }
}
