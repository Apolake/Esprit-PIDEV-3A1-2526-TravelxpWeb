<?php

namespace App\Service;

class ReadTimeEstimatorService
{
    public function estimateMinutes(?string $content): int
    {
        if ($content === null || trim($content) === '') {
            return 1;
        }

        $plain = trim(strip_tags($content));
        if ($plain === '') {
            return 1;
        }

        $wordCount = str_word_count($plain);
        $minutes = (int) ceil($wordCount / 200);

        return max(1, $minutes);
    }
}
