<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\UserQuestProgress;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserQuestProgress>
 */
class UserQuestProgressRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserQuestProgress::class);
    }

    /**
     * @return UserQuestProgress[]
     */
    public function findByUserWithQuest(User $user): array
    {
        return $this->createQueryBuilder('uq')
            ->innerJoin('uq.quest', 'q')
            ->addSelect('q')
            ->andWhere('uq.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }
}
