<?php

namespace App\Service;

use App\Entity\User;

class GamificationProgressService
{
    /**
     * @return array{
     *   xp:int,
     *   level:int,
     *   nextLevelXp:int,
     *   progressPercent:int,
     *   rank:string,
     *   streak:int,
     *   quests:array<int, array{title:string, progress:int, goal:int, reward:string, status:string}>
     * }
     */
    public function buildForUser(?User $user): array
    {
        $seed = $user?->getId() ?? 4;
        $xp = 300 + (($seed * 173) % 1600);
        $levelSize = 250;
        $level = intdiv($xp, $levelSize) + 1;
        $xpIntoLevel = $xp % $levelSize;
        $progressPercent = (int) round(($xpIntoLevel / $levelSize) * 100);

        $quests = [
            [
                'title' => 'Complete profile setup',
                'progress' => $user ? 1 : 0,
                'goal' => 1,
                'reward' => '+100 XP',
                'status' => $user ? 'completed' : 'active',
            ],
            [
                'title' => 'Update profile details 3 times',
                'progress' => min(3, ($seed % 4)),
                'goal' => 3,
                'reward' => '+180 XP',
                'status' => ($seed % 4) >= 3 ? 'completed' : 'active',
            ],
            [
                'title' => 'Log in 5 days in a row',
                'progress' => min(5, 2 + ($seed % 5)),
                'goal' => 5,
                'reward' => 'Explorer Badge',
                'status' => (2 + ($seed % 5)) >= 5 ? 'completed' : 'active',
            ],
            [
                'title' => 'Reach level 10',
                'progress' => min(10, $level),
                'goal' => 10,
                'reward' => 'Pro Traveler Frame',
                'status' => $level >= 10 ? 'completed' : 'active',
            ],
        ];

        return [
            'xp' => $xp,
            'level' => $level,
            'nextLevelXp' => ($level * $levelSize),
            'progressPercent' => $progressPercent,
            'rank' => $level >= 8 ? 'Gold Traveler' : ($level >= 4 ? 'Silver Traveler' : 'Bronze Traveler'),
            'streak' => 2 + ($seed % 8),
            'quests' => $quests,
        ];
    }
}
