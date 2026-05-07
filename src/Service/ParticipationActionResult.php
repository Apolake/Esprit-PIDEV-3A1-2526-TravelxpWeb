<?php

namespace App\Service;

class ParticipationActionResult
{
    public function __construct(
        private readonly string $code,
        private readonly string $message,
    ) {
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function isSuccess(): bool
    {
        return str_starts_with($this->code, 'success_');
    }

    public function isWarning(): bool
    {
        return str_starts_with($this->code, 'warning_');
    }

    public function isInfo(): bool
    {
        return str_starts_with($this->code, 'info_');
    }

    public function toFlashType(): string
    {
        if ($this->isSuccess()) {
            return 'success';
        }

        if ($this->isWarning()) {
            return 'warning';
        }

        if ($this->isInfo()) {
            return 'info';
        }

        return 'danger';
    }
}
