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
            ->setMaxResults(100)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Quest[]
     */
    public function findByAdminFilters(?string $query, ?string $status, string $sortBy = 'updatedAt', string $direction = 'DESC'): array
    {
        $qb = $this->createQueryBuilder('q');

        $trimmedQuery = trim((string) $query);
        if ('' !== $trimmedQuery) {
            $search = '%'.mb_strtolower($trimmedQuery).'%';
            $orX = $qb->expr()->orX(
                'LOWER(q.title) LIKE :search',
                'LOWER(COALESCE(q.description, \'\')) LIKE :search'
            );

            if (ctype_digit($trimmedQuery)) {
                $orX->add('q.id = :idQuery');
                $qb->setParameter('idQuery', (int) $trimmedQuery);
            }

            $qb->andWhere($orX)->setParameter('search', $search);
        }

        if ('active' === $status) {
            $qb->andWhere('q.isActive = :isActive')->setParameter('isActive', true);
        } elseif ('inactive' === $status) {
            $qb->andWhere('q.isActive = :isActive')->setParameter('isActive', false);
        }

        $sortable = [
            'title' => 'q.title',
            'goal' => 'q.goal',
            'rewardXp' => 'q.rewardXp',
            'createdAt' => 'q.createdAt',
            'updatedAt' => 'q.updatedAt',
        ];

        $orderBy = $sortable[$sortBy] ?? 'q.updatedAt';
        $order = 'ASC' === strtoupper($direction) ? 'ASC' : 'DESC';

        return $qb
            ->orderBy($orderBy, $order)
            ->addOrderBy('q.id', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
