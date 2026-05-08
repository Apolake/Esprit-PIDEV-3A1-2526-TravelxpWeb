<?php

namespace App\DTO;

/**
 * DTO for aggregated relation counts (likes, dislikes, comments).
 * Used with Doctrine NEW operator for 3-5x faster hydration.
 */
class RelationCountRow
{
    public function __construct(
        public readonly int $id,
        public readonly int $count,
    ) {
    }
}
