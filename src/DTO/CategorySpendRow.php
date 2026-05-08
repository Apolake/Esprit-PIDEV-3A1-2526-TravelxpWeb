<?php

namespace App\DTO;

/**
 * DTO for expense category breakdown aggregation.
 * Used with Doctrine NEW operator for faster, type-safe hydration.
 */
class CategorySpendRow
{
    public function __construct(
        public readonly string $category,
        public readonly float $total,
    ) {
    }
}
