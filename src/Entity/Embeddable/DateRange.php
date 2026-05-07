<?php

namespace App\Entity\Embeddable;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Embeddable]
class DateRange
{
    #[ORM\Column(type: 'date_immutable')]
    #[Assert\NotNull(message: 'Start date is required.')]
    private \DateTimeImmutable $startDate;

    #[ORM\Column(type: 'date_immutable')]
    #[Assert\NotNull(message: 'End date is required.')]
    private \DateTimeImmutable $endDate;

    public function __construct(
        ?\DateTimeImmutable $startDate = null,
        ?\DateTimeImmutable $endDate = null,
    ) {
        $this->startDate = $startDate ?? new \DateTimeImmutable();
        $this->endDate = $endDate ?? new \DateTimeImmutable();
    }

    public function getStartDate(): \DateTimeImmutable
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): void
    {
        $this->startDate = \DateTimeImmutable::createFromInterface($startDate);
    }

    public function getEndDate(): \DateTimeImmutable
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): void
    {
        $this->endDate = \DateTimeImmutable::createFromInterface($endDate);
    }

    public function getDurationInDays(): int
    {
        return max(0, (int) $this->startDate->diff($this->endDate)->days);
    }

    public function contains(\DateTimeImmutable $date): bool
    {
        return $date >= $this->startDate && $date <= $this->endDate;
    }

    public function isValid(): bool
    {
        return $this->endDate >= $this->startDate;
    }
}
