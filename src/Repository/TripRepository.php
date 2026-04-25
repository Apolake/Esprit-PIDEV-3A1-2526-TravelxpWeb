<?php

namespace App\Repository;

use App\Entity\Trip;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Trip>
 */
class TripRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Trip::class);
    }

    /**
     * @param array<string, string> $filters
     */
    public function createFilteredQueryBuilder(array $filters, bool $adminView = false, ?User $viewer = null): QueryBuilder
    {
        $qb = $this->createQueryBuilder('t');

        $q = trim((string) ($filters['q'] ?? ''));
        if ('' !== $q) {
            $search = '%'.mb_strtolower($q).'%';
            $qb
                ->andWhere('LOWER(t.tripName) LIKE :search OR LOWER(COALESCE(t.origin, \'\')) LIKE :search OR LOWER(COALESCE(t.destination, \'\')) LIKE :search')
                ->setParameter('search', $search);
        }

        $status = strtoupper(trim((string) ($filters['status'] ?? '')));
        if ('' !== $status && in_array($status, Trip::ALLOWED_STATUSES, true)) {
            $qb->andWhere('t.status = :status')->setParameter('status', $status);
        }

        $destination = trim((string) ($filters['destination'] ?? ''));
        if ('' !== $destination) {
            $qb->andWhere('t.destination = :destination')->setParameter('destination', $destination);
        }

        if (!$adminView && '1' === (string) ($filters['myTrips'] ?? '') && $viewer !== null) {
            $qb
                ->innerJoin('t.participants', 'tp_my')
                ->andWhere('tp_my = :viewer')
                ->setParameter('viewer', $viewer);
        }

        $sort = (string) ($filters['sort'] ?? 'newest');
        $sortable = [
            'newest' => ['t.createdAt', 'DESC'],
            'oldest' => ['t.createdAt', 'ASC'],
            'name_asc' => ['t.tripName', 'ASC'],
            'name_desc' => ['t.tripName', 'DESC'],
            'date_asc' => ['t.startDate', 'ASC'],
            'date_desc' => ['t.startDate', 'DESC'],
            'budget_asc' => ['t.budgetAmount', 'ASC'],
            'budget_desc' => ['t.budgetAmount', 'DESC'],
        ];
        [$sortField, $sortDirection] = $sortable[$sort] ?? $sortable['newest'];

        return $qb
            ->orderBy($sortField, $sortDirection)
            ->addOrderBy('t.id', 'DESC');
    }

    /**
     * @return list<string>
     */
    public function getDistinctDestinations(): array
    {
        $rows = $this->createQueryBuilder('t')
            ->select('DISTINCT t.destination AS value')
            ->andWhere('t.destination IS NOT NULL')
            ->andWhere("t.destination <> ''")
            ->orderBy('value', 'ASC')
            ->getQuery()
            ->getScalarResult();

        return array_map(static fn (array $row): string => (string) $row['value'], $rows);
    }

    /**
     * @return list<int>
     */
    public function findJoinedTripIdsForUser(User $user): array
    {
        $rows = $this->createQueryBuilder('t')
            ->select('t.id AS id')
            ->innerJoin('t.participants', 'tp')
            ->andWhere('tp = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getScalarResult();

        return array_map(static fn (array $row): int => (int) $row['id'], $rows);
    }
}
