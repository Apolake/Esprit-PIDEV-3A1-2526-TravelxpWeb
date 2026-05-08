<?php

namespace App\DTO;

/**
 * DTO for blog/comment search suggestions.
 * Used with Doctrine NEW operator for faster hydration.
 */
class SearchSuggestionRow
{
    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly string $content,
    ) {
    }

    public function toArray(): array
    {
        $excerpt = mb_substr(trim($this->content), 0, 90);

        return [
            'id' => $this->id,
            'title' => $this->title,
            'excerpt' => $excerpt . (mb_strlen(trim($this->content)) > 90 ? '...' : ''),
        ];
    }
}
