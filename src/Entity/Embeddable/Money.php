<?php

namespace App\Entity\Embeddable;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Embeddable]
class Money
{
    #[ORM\Column(type: 'decimal', precision: 12, scale: 2)]
    #[Assert\PositiveOrZero(message: 'Amount must be positive or zero.')]
    private string $amount = '0.00';

    #[ORM\Column(length: 10)]
    #[Assert\NotBlank(message: 'Currency is required.')]
    #[Assert\Currency(message: 'Invalid currency code.')]
    private string $currency = 'USD';

    public function __construct(string $amount = '0.00', string $currency = 'USD')
    {
        $this->amount = $amount;
        $this->currency = strtoupper($currency);
    }

    public function getAmount(): string
    {
        return $this->amount;
    }

    public function setAmount(string|float|int $amount): void
    {
        $value = is_string($amount) ? (float) $amount : (float) $amount;
        $this->amount = number_format(max(0, $value), 2, '.', '');
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): void
    {
        $this->currency = strtoupper(trim($currency));
    }

    public function isZero(): bool
    {
        return bccomp($this->amount, '0.00', 2) === 0;
    }

    public function isGreaterThan(self $other): bool
    {
        return bccomp($this->amount, $other->amount, 2) > 0;
    }

    public function add(self $other): self
    {
        if ($this->currency !== $other->currency) {
            throw new \InvalidArgumentException('Cannot add amounts in different currencies.');
        }

        return new self(bcadd($this->amount, $other->amount, 2), $this->currency);
    }
}
