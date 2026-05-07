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

    public function hasDuplicateTrip(Trip $trip): bool
    {
        $qb = $this->createQueryBuilder('t')
            ->select('COUNT(t.id)')
            ->andWhere('LOWER(t.tripName) = :tripName')
            ->andWhere('LOWER(COALESCE(t.origin, \'\')) = :origin')
            ->andWhere('LOWER(COALESCE(t.destination, \'\')) = :destination')
            ->andWhere('t.startDate = :startDate')
            ->andWhere('t.endDate = :endDate')
            ->setParameter('tripName', mb_strtolower(trim((string) $trip->getTripName())))
            ->setParameter('origin', mb_strtolower(trim((string) $trip->getOrigin())))
            ->setParameter('destination', mb_strtolower(trim((string) $trip->getDestination())))
            ->setParameter('startDate', $trip->getStartDate())
            ->setParameter('endDate', $trip->getEndDate());

        if (null !== $trip->getId()) {
            $qb->andWhere('t.id != :currentId')->setParameter('currentId', $trip->getId());
        }

        return (int) $qb->getQuery()->getSingleScalarResult() > 0;
    }

    public function hasSameDestinationTimeConflictForUser(Trip $trip): bool
    {
        if (
            null === $trip->getUserId()
            || null === $trip->getStartDate()
            || null === $trip->getEndDate()
            || '' === trim((string) $trip->getDestination())
        ) {
            return false;
        }

        $qb = $this->createQueryBuilder('t')
            ->select('COUNT(t.id)')
            ->andWhere('t.userId = :userId')
            ->andWhere('LOWER(COALESCE(t.destination, \'\')) = :destination')
            ->andWhere('t.startDate <= :endDate')
            ->andWhere('t.endDate >= :startDate')
            ->setParameter('userId', $trip->getUserId())
            ->setParameter('destination', mb_strtolower(trim((string) $trip->getDestination())))
            ->setParameter('startDate', $trip->getStartDate())
            ->setParameter('endDate', $trip->getEndDate());

        if (null !== $trip->getId()) {
            $qb->andWhere('t.id != :currentId')->setParameter('currentId', $trip->getId());
        }

        return (int) $qb->getQuery()->getSingleScalarResult() > 0;
    }

    /**
     * @return Trip[]
     */
    public function findUpcomingTripsForWeatherMonitoring(\DateTimeImmutable $fromDate, \DateTimeImmutable $toDate, int $limit = 50): array
    {
        return $this->createQueryBuilder('t')
            ->leftJoin('t.participants', 'p')->addSelect('p')
            ->andWhere('t.startDate >= :fromDate')
            ->andWhere('t.startDate <= :toDate')
            ->andWhere('t.destinationLatitude IS NOT NULL')
            ->andWhere('t.destinationLongitude IS NOT NULL')
            ->andWhere('t.status NOT IN (:closedStatuses)')
            ->setParameter('fromDate', $fromDate)
            ->setParameter('toDate', $toDate)
            ->setParameter('closedStatuses', ['CANCELLED', 'COMPLETED', 'DONE'])
            ->orderBy('t.startDate', 'ASC')
            ->addOrderBy('t.id', 'ASC')
            ->setMaxResults(max(1, $limit))
            ->getQuery()
            ->getResult();
    }
}
