<?php

namespace App\Repository;

use App\Entity\Trip;
use App\Entity\TripWaitingListEntry;
use App\Entity\User;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TripWaitingListEntry>
 */
class TripWaitingListEntryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TripWaitingListEntry::class);
    }

    public function findActiveWaitingEntryForUser(Trip $trip, User $user): ?TripWaitingListEntry
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.trip = :trip')
            ->andWhere('w.user = :user')
            ->andWhere('w.status = :status')
            ->setParameter('trip', $trip)
            ->setParameter('user', $user)
            ->setParameter('status', TripWaitingListEntry::STATUS_WAITING)
            ->orderBy('w.queuedAt', 'ASC')
            ->addOrderBy('w.id', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findNextWaitingEntry(Trip $trip, \DateTimeImmutable $now): ?TripWaitingListEntry
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.trip = :trip')
            ->andWhere('w.status = :status')
            ->andWhere('(w.expiresAt IS NULL OR w.expiresAt > :now)')
            ->setParameter('trip', $trip)
            ->setParameter('status', TripWaitingListEntry::STATUS_WAITING)
            ->setParameter('now', $now)
            ->orderBy('w.queuedAt', 'ASC')
            ->addOrderBy('w.id', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return TripWaitingListEntry[]
     */
    public function findExpiredWaitingEntries(Trip $trip, \DateTimeImmutable $now): array
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.trip = :trip')
            ->andWhere('w.status = :status')
            ->andWhere('w.expiresAt IS NOT NULL')
            ->andWhere('w.expiresAt <= :now')
            ->setParameter('trip', $trip)
            ->setParameter('status', TripWaitingListEntry::STATUS_WAITING)
            ->setParameter('now', $now)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return TripWaitingListEntry[]
     */
    public function findActiveWaitingEntriesForUser(User $user): array
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.user = :user')
            ->andWhere('w.status = :status')
            ->setParameter('user', $user)
            ->setParameter('status', TripWaitingListEntry::STATUS_WAITING)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return list<int>
     */
    public function findActiveWaitingTripIdsForUser(User $user): array
    {
        $rows = $this->createQueryBuilder('w')
            ->select('IDENTITY(w.trip) AS tripId')
            ->andWhere('w.user = :user')
            ->andWhere('w.status = :status')
            ->setParameter('user', $user)
            ->setParameter('status', TripWaitingListEntry::STATUS_WAITING)
            ->getQuery()
            ->getScalarResult();

        return array_map(static fn (array $row): int => (int) $row['tripId'], $rows);
    }

    public function createAdminQueryBuilder(string $status = ''): QueryBuilder
    {
        $qb = $this->createQueryBuilder('w')
            ->innerJoin('w.user', 'u')->addSelect('u')
            ->innerJoin('w.trip', 't')->addSelect('t');

        $normalizedStatus = strtoupper(trim($status));
        if ($normalizedStatus !== '') {
            $qb->andWhere('w.status = :status')
                ->setParameter('status', $normalizedStatus);
        }

        return $qb
            ->orderBy("CASE WHEN w.status = 'WAITING' THEN 0 ELSE 1 END", 'ASC')
            ->addOrderBy('w.queuedAt', 'ASC')
            ->addOrderBy('w.id', 'ASC');
    }

    /**
     * @return TripWaitingListEntry[]
     */
    public function findExpiredWaitingEntriesBatch(\DateTimeImmutable $now, int $limit = 200): array
    {
        return $this->createQueryBuilder('w')
            ->innerJoin('w.user', 'u')->addSelect('u')
            ->innerJoin('w.trip', 't')->addSelect('t')
            ->andWhere('w.status = :status')
            ->andWhere('w.expiresAt IS NOT NULL')
            ->andWhere('w.expiresAt <= :now')
            ->setParameter('status', TripWaitingListEntry::STATUS_WAITING)
            ->setParameter('now', $now)
            ->orderBy('w.expiresAt', 'ASC')
            ->addOrderBy('w.id', 'ASC')
            ->setMaxResults(max(1, $limit))
            ->getQuery()
            ->getResult();
    }
}
