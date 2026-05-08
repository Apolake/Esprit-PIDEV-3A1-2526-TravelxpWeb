<?php

namespace App\DTO;

/**
 * DTO for scalar COUNT aggregation queries.
 * Used with Doctrine NEW operator for type-safe hydration.
 */
class ScalarCountRow
{
    public function __construct(
        public readonly int $count,
    ) {
    }
}
