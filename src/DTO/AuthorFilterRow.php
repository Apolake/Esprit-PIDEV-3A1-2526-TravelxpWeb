<?php

namespace App\DTO;

/**
 * DTO for author filter dropdown items.
 * Used with Doctrine NEW operator for faster hydration.
 */
class AuthorFilterRow
{
    public function __construct(
        public readonly int $id,
        public readonly string $username,
    ) {
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
        ];
    }
}
