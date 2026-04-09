<?php

namespace App\Repository;

use App\Entity\Quest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Quest>
 */
class QuestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Quest::class);
    }

    /**
     * @return Quest[]
     */
    public function findActiveOrdered(): array
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.isActive = :active')
            ->setParameter('active', true)
            ->orderBy('q.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
