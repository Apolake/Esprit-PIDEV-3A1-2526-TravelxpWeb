<?php

namespace App\Repository;

use App\Entity\Activity;
use App\Entity\Trip;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Activity>
 */
class ActivityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Activity::class);
    }

    /**
     * @param array<string, string> $filters
     */
    public function createFilteredQueryBuilder(array $filters, bool $adminView = false, ?User $viewer = null): QueryBuilder
    {
        $qb = $this->createQueryBuilder('a')
            ->leftJoin('a.trip', 't')
            ->addSelect('t');

        $q = trim((string) ($filters['q'] ?? ''));
        if ('' !== $q) {
            $search = '%'.mb_strtolower($q).'%';
            $qb
                ->andWhere('LOWER(a.title) LIKE :search OR LOWER(COALESCE(a.type, \'\')) LIKE :search OR LOWER(COALESCE(a.locationName, \'\')) LIKE :search OR LOWER(COALESCE(t.tripName, \'\')) LIKE :search')
                ->setParameter('search', $search);
        }

        $status = strtoupper(trim((string) ($filters['status'] ?? '')));
        if ('' !== $status && in_array($status, Activity::ALLOWED_STATUSES, true)) {
            $qb->andWhere('a.status = :status')->setParameter('status', $status);
        }

        $type = trim((string) ($filters['type'] ?? ''));
        if ('' !== $type) {
            $qb->andWhere('a.type = :type')->setParameter('type', $type);
        }

        $tripId = trim((string) ($filters['tripId'] ?? ''));
        if (ctype_digit($tripId)) {
            $qb->andWhere('t.id = :tripId')->setParameter('tripId', (int) $tripId);
        }

        if (!$adminView && '1' === (string) ($filters['myActivities'] ?? '') && $viewer !== null) {
            $qb
                ->innerJoin('a.participants', 'ap_my')
                ->andWhere('ap_my = :viewer')
                ->setParameter('viewer', $viewer);
        }

        $sort = (string) ($filters['sort'] ?? 'newest');
        $sortable = [
            'newest' => ['a.createdAt', 'DESC'],
            'oldest' => ['a.createdAt', 'ASC'],
            'title_asc' => ['a.title', 'ASC'],
            'title_desc' => ['a.title', 'DESC'],
            'date_asc' => ['a.activityDate', 'ASC'],
            'date_desc' => ['a.activityDate', 'DESC'],
            'cost_asc' => ['a.costAmount', 'ASC'],
            'cost_desc' => ['a.costAmount', 'DESC'],
        ];
        [$sortField, $sortDirection] = $sortable[$sort] ?? $sortable['newest'];

        return $qb
            ->orderBy($sortField, $sortDirection)
            ->addOrderBy('a.id', 'DESC');
    }

    /**
     * @return list<string>
     */
    public function getDistinctTypes(): array
    {
        $rows = $this->createQueryBuilder('a')
            ->select('DISTINCT a.type AS value')
            ->andWhere('a.type IS NOT NULL')
            ->andWhere("a.type <> ''")
            ->orderBy('value', 'ASC')
            ->getQuery()
            ->getScalarResult();

        return array_map(static fn (array $row): string => (string) $row['value'], $rows);
    }

    /**
     * @return Trip[]
     */
    public function getTripsForFilter(): array
    {
        return $this->getEntityManager()
            ->getRepository(Trip::class)
            ->createQueryBuilder('t')
            ->orderBy('t.tripName', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return list<int>
     */
    public function findJoinedActivityIdsForUser(User $user): array
    {
        $rows = $this->createQueryBuilder('a')
            ->select('a.id AS id')
            ->innerJoin('a.participants', 'ap')
            ->andWhere('ap = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getScalarResult();

        return array_map(static fn (array $row): int => (int) $row['id'], $rows);
    }
}
