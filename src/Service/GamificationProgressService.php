<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\QuestRepository;
use App\Repository\UserQuestProgressRepository;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;

class GamificationProgressService
{
    public function __construct(
        private readonly QuestRepository $questRepository,
        private readonly UserQuestProgressRepository $userQuestProgressRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

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
        $levelSize = 250;
        $xp = $user?->getXp() ?? 0;
        $level = max(1, $user?->getLevel() ?? 1);
        $streak = max(0, $user?->getStreak() ?? 0);
        $xpIntoLevel = $xp % $levelSize;
        $progressPercent = (int) round(($xpIntoLevel / $levelSize) * 100);

        if (!$this->hasGamificationTables()) {
            return [
                'xp' => $xp,
                'level' => $level,
                'nextLevelXp' => $level * $levelSize,
                'progressPercent' => $progressPercent,
                'rank' => $level >= 12 ? 'Legend Traveler' : ($level >= 8 ? 'Gold Traveler' : ($level >= 4 ? 'Silver Traveler' : 'Bronze Traveler')),
                'streak' => $streak,
                'quests' => [],
            ];
        }

        try {
            $quests = $this->questRepository->findActiveOrdered();
            $questProgressMap = [];

            if ($user) {
                foreach ($this->userQuestProgressRepository->findByUserWithQuest($user) as $progress) {
                    if (null !== $progress->getQuest()) {
                        $questProgressMap[$progress->getQuest()->getId()] = $progress;
                    }
                }
            }

            $questRows = [];
            foreach ($quests as $quest) {
                $goal = max(1, $quest->getGoal());
                $progressEntity = $questProgressMap[$quest->getId()] ?? null;
                $progress = $progressEntity?->getProgress() ?? 0;
                $clampedProgress = min($goal, $progress);
                $status = $clampedProgress >= $goal || 'completed' === ($progressEntity?->getStatus() ?? '')
                    ? 'completed'
                    : 'active';

                $questRows[] = [
                    'title' => $quest->getTitle() ?? 'Quest',
                    'progress' => $clampedProgress,
                    'goal' => $goal,
                    'reward' => sprintf('+%d XP', $quest->getRewardXp()),
                    'status' => $status,
                ];
            }
        } catch (\Throwable) {
            $questRows = [];
        }

        return [
            'xp' => $xp,
            'level' => $level,
            'nextLevelXp' => $level * $levelSize,
            'progressPercent' => $progressPercent,
            'rank' => $level >= 12 ? 'Legend Traveler' : ($level >= 8 ? 'Gold Traveler' : ($level >= 4 ? 'Silver Traveler' : 'Bronze Traveler')),
            'streak' => $streak,
            'quests' => $questRows,
        ];
    }

    private function hasGamificationTables(): bool
    {
        try {
            $schemaManager = $this->entityManager->getConnection()->createSchemaManager();

            return $schemaManager->tablesExist(['quests', 'user_quest_progress']);
        } catch (Exception|\Throwable) {
            return false;
        }
    }
}
