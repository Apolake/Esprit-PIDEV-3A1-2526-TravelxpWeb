<?php

namespace App\Repository;

use App\Entity\Activity;
use App\Entity\ActivityWaitingListEntry;
use App\Entity\Trip;
use App\Entity\User;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ActivityWaitingListEntry>
 */
class ActivityWaitingListEntryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ActivityWaitingListEntry::class);
    }

    public function findActiveWaitingEntryForUser(Activity $activity, User $user): ?ActivityWaitingListEntry
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.activity = :activity')
            ->andWhere('w.user = :user')
            ->andWhere('w.status = :status')
            ->setParameter('activity', $activity)
            ->setParameter('user', $user)
            ->setParameter('status', ActivityWaitingListEntry::STATUS_WAITING)
            ->orderBy('w.queuedAt', 'ASC')
            ->addOrderBy('w.id', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findNextWaitingEntry(Activity $activity, \DateTimeImmutable $now): ?ActivityWaitingListEntry
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.activity = :activity')
            ->andWhere('w.status = :status')
            ->andWhere('(w.expiresAt IS NULL OR w.expiresAt > :now)')
            ->setParameter('activity', $activity)
            ->setParameter('status', ActivityWaitingListEntry::STATUS_WAITING)
            ->setParameter('now', $now)
            ->orderBy('w.queuedAt', 'ASC')
            ->addOrderBy('w.id', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return ActivityWaitingListEntry[]
     */
    public function findExpiredWaitingEntries(Activity $activity, \DateTimeImmutable $now): array
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.activity = :activity')
            ->andWhere('w.status = :status')
            ->andWhere('w.expiresAt IS NOT NULL')
            ->andWhere('w.expiresAt <= :now')
            ->setParameter('activity', $activity)
            ->setParameter('status', ActivityWaitingListEntry::STATUS_WAITING)
            ->setParameter('now', $now)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return ActivityWaitingListEntry[]
     */
    public function findActiveWaitingEntriesForUser(User $user): array
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.user = :user')
            ->andWhere('w.status = :status')
            ->setParameter('user', $user)
            ->setParameter('status', ActivityWaitingListEntry::STATUS_WAITING)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return ActivityWaitingListEntry[]
     */
    public function findActiveWaitingEntriesForTripAndUser(Trip $trip, User $user): array
    {
        return $this->createQueryBuilder('w')
            ->innerJoin('w.activity', 'a')
            ->andWhere('a.trip = :trip')
            ->andWhere('w.user = :user')
            ->andWhere('w.status = :status')
            ->setParameter('trip', $trip)
            ->setParameter('user', $user)
            ->setParameter('status', ActivityWaitingListEntry::STATUS_WAITING)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return list<int>
     */
    public function findActiveWaitingActivityIdsForUser(User $user): array
    {
        $rows = $this->createQueryBuilder('w')
            ->select('IDENTITY(w.activity) AS activityId')
            ->andWhere('w.user = :user')
            ->andWhere('w.status = :status')
            ->setParameter('user', $user)
            ->setParameter('status', ActivityWaitingListEntry::STATUS_WAITING)
            ->getQuery()
            ->getScalarResult();

        return array_map(static fn (array $row): int => (int) $row['activityId'], $rows);
    }

    public function createAdminQueryBuilder(string $status = ''): QueryBuilder
    {
        $qb = $this->createQueryBuilder('w')
            ->innerJoin('w.user', 'u')->addSelect('u')
            ->innerJoin('w.activity', 'a')->addSelect('a')
            ->leftJoin('a.trip', 't')->addSelect('t');

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
     * @return ActivityWaitingListEntry[]
     */
    public function findExpiredWaitingEntriesBatch(\DateTimeImmutable $now, int $limit = 200): array
    {
        return $this->createQueryBuilder('w')
            ->innerJoin('w.user', 'u')->addSelect('u')
            ->innerJoin('w.activity', 'a')->addSelect('a')
            ->andWhere('w.status = :status')
            ->andWhere('w.expiresAt IS NOT NULL')
            ->andWhere('w.expiresAt <= :now')
            ->setParameter('status', ActivityWaitingListEntry::STATUS_WAITING)
            ->setParameter('now', $now)
            ->orderBy('w.expiresAt', 'ASC')
            ->addOrderBy('w.id', 'ASC')
            ->setMaxResults(max(1, $limit))
            ->getQuery()
            ->getResult();
    }
}
