<?php

namespace App\Service;

class ProfanityFilterService
{
    /**
     * @var list<string>
     */
    private array $blockedWords = [
        'fuck',
        'shit',
        'bitch',
        'asshole',
        'bastard',
        'dumbass',
        'crap',
        'puta',
        'merde',
    ];

    public function sanitize(?string $text): string
    {
        $value = (string) $text;
        if (trim($value) === '') {
            return $value;
        }

        $pattern = '/\\b(' . implode('|', array_map(static fn (string $word): string => preg_quote($word, '/'), $this->blockedWords)) . ')\\b/i';

        return (string) preg_replace_callback($pattern, static function (array $matches): string {
            return str_repeat('*', max(4, mb_strlen((string) ($matches[0] ?? ''))));
        }, $value);
    }
}
