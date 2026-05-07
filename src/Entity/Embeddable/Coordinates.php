<?php

namespace App\Entity\Embeddable;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Embeddable]
class Coordinates
{
    #[ORM\Column(type: 'decimal', precision: 10, scale: 7, nullable: true)]
    #[Assert\Range(min: -90, max: 90, notInRangeMessage: 'Latitude must be between -90 and 90.')]
    private ?string $latitude = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 7, nullable: true)]
    #[Assert\Range(min: -180, max: 180, notInRangeMessage: 'Longitude must be between -180 and 180.')]
    private ?string $longitude = null;

    public function __construct(?string $latitude = null, ?string $longitude = null)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(null|string|float $latitude): void
    {
        $this->latitude = null === $latitude ? null : (string) $latitude;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(null|string|float $longitude): void
    {
        $this->longitude = null === $longitude ? null : (string) $longitude;
    }

    public function isSet(): bool
    {
        return $this->latitude !== null && $this->longitude !== null;
    }

    /**
     * Calculate approximate distance in kilometers using the Haversine formula.
     */
    public function distanceTo(self $other): ?float
    {
        if (!$this->isSet() || !$other->isSet()) {
            return null;
        }

        $earthRadiusKm = 6371;
        $latFrom = deg2rad((float) $this->latitude);
        $lonFrom = deg2rad((float) $this->longitude);
        $latTo = deg2rad((float) $other->latitude);
        $lonTo = deg2rad((float) $other->longitude);

        $latDiff = $latTo - $latFrom;
        $lonDiff = $lonTo - $lonFrom;

        $a = sin($latDiff / 2) ** 2 + cos($latFrom) * cos($latTo) * sin($lonDiff / 2) ** 2;

        return 2 * $earthRadiusKm * asin(sqrt($a));
    }
}
