<?php

namespace App\Repository;

use App\Entity\LoginHistory;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LoginHistory>
 */
class LoginHistoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LoginHistory::class);
    }

    /**
     * @return LoginHistory[]
     */
    public function findRecentForUser(User $user, int $limit = 15): array
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.user = :user')
            ->setParameter('user', $user)
            ->orderBy('h.loginAt', 'DESC')
            ->addOrderBy('h.id', 'DESC')
            ->setMaxResults(max(1, $limit))
            ->getQuery()
            ->getResult();
    }
}
