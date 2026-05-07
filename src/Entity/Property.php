<?php

namespace App\Entity;

use App\Entity\Trait\BlameableTrait;
use App\Repository\PropertyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PropertyRepository::class)]
#[ORM\Table(name: 'property')]
#[ORM\HasLifecycleCallbacks]
class Property
{
    use BlameableTrait;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Assert\NotBlank(normalizer: 'trim', message: 'Property title is required.')]
    #[Assert\Length(min: 3, max: 180)]
    private string $title = '';

    #[ORM\Column(type: 'text', nullable: true)]
    #[Assert\Length(max: 3000)]
    private ?string $description = null;

    #[ORM\Column(length: 80)]
    #[Assert\NotBlank(normalizer: 'trim', message: 'Property type is required.')]
    #[Assert\Length(min: 2, max: 80)]
    private string $propertyType = '';

    #[ORM\Column(length: 120)]
    #[Assert\NotBlank(normalizer: 'trim', message: 'City is required.')]
    #[Assert\Length(min: 2, max: 120)]
    private string $city = '';

    #[ORM\Column(length: 120)]
    #[Assert\NotBlank(normalizer: 'trim', message: 'Country is required.')]
    #[Assert\Length(min: 2, max: 120)]
    private string $country = '';

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(max: 255)]
    private ?string $address = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $latitude = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $longitude = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    #[Assert\PositiveOrZero(message: 'Price per night must be positive or zero.')]
    private string $pricePerNight = '0.00';

    #[ORM\Column(options: ['default' => 0])]
    #[Assert\PositiveOrZero(message: 'Bedrooms must be positive or zero.')]
    private int $bedrooms = 0;

    #[ORM\Column(options: ['default' => 1])]
    #[Assert\Positive(message: 'Max guests must be greater than 0.')]
    private int $maxGuests = 1;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(max: 255)]
    private ?string $images = null;

    #[ORM\Column(options: ['default' => true])]
    private bool $isActive = true;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * @var Collection<int, Offer>
     */
    #[ORM\OneToMany(mappedBy: 'property', targetEntity: Offer::class, orphanRemoval: true, cascade: ['persist'])]
    private Collection $offers;

    /**
     * @var Collection<int, Booking>
     */
    #[ORM\OneToMany(mappedBy: 'property', targetEntity: Booking::class, orphanRemoval: true, cascade: ['persist'])]
    private Collection $bookings;

    public function __construct()
    {
        $this->offers = new ArrayCollection();
        $this->bookings = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
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

    public function getPropertyType(): string
    {
        return $this->propertyType;
    }

    public function setPropertyType(?string $propertyType): static
    {
        $this->propertyType = null === $propertyType ? null : trim($propertyType);

        return $this;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function setCity(?string $city): static
    {
        $this->city = null === $city ? null : trim($city);

        return $this;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function setCountry(?string $country): static
    {
        $this->country = null === $country ? null : trim($country);

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): static
    {
        $this->address = null === $address ? null : trim($address);

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

    public function getPricePerNight(): string
    {
        return $this->pricePerNight;
    }

    public function setPricePerNight(string|float|int $pricePerNight): static
    {
        $value = is_string($pricePerNight) ? (float) $pricePerNight : (float) $pricePerNight;
        $this->pricePerNight = number_format(max(0, $value), 2, '.', '');

        return $this;
    }

    public function getBedrooms(): int
    {
        return $this->bedrooms;
    }

    public function setBedrooms(int $bedrooms): static
    {
        $this->bedrooms = max(0, $bedrooms);

        return $this;
    }

    public function getMaxGuests(): int
    {
        return $this->maxGuests;
    }

    public function setMaxGuests(int $maxGuests): static
    {
        $this->maxGuests = max(1, $maxGuests);

        return $this;
    }

    public function getImages(): ?string
    {
        return $this->images;
    }

    public function setImages(?string $images): static
    {
        $this->images = null === $images ? null : trim($images);

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

    /**
     * @internal Managed by lifecycle callbacks.
     */
    public function setCreatedAt(?\DateTimeInterface $createdAt): static
    {
        $this->createdAt = null === $createdAt ? null : \DateTimeImmutable::createFromInterface($createdAt);

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt ?? $this->createdAt;
    }

    /**
     * @internal Managed by lifecycle callbacks.
     */
    public function setUpdatedAt(?\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = null === $updatedAt ? null : \DateTimeImmutable::createFromInterface($updatedAt);

        return $this;
    }

    /**
     * @return Collection<int, Offer>
     */
    public function getOffers(): Collection
    {
        return $this->offers;
    }

    public function addOffer(Offer $offer): static
    {
        if (!$this->offers->contains($offer)) {
            $this->offers->add($offer);
            $offer->setProperty($this);
        }

        return $this;
    }

    public function removeOffer(Offer $offer): static
    {
        if ($this->offers->removeElement($offer)) {
            if ($offer->getProperty() === $this) {
                $offer->setProperty(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Booking>
     */
    public function getBookings(): Collection
    {
        return $this->bookings;
    }

    public function addBooking(Booking $booking): static
    {
        if (!$this->bookings->contains($booking)) {
            $this->bookings->add($booking);
            $booking->setProperty($this);
        }

        return $this;
    }

    public function removeBooking(Booking $booking): static
    {
        if ($this->bookings->removeElement($booking)) {
            if ($booking->getProperty() === $this) {
                $booking->setProperty(null);
            }
        }

        return $this;
    }

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $this->createdAt ??= new \DateTimeImmutable();
    }
}
