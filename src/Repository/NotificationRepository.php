<?php

namespace App\Repository;

use App\DTO\ScalarCountRow;
use App\Entity\Notification;
use App\Entity\User;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Notification>
 */
class NotificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notification::class);
    }

    /**
     * @return Notification[]
     */
    public function findUnreadByUser(User $user, int $limit = 50): array
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.user = :user')
            ->andWhere('n.isRead = false')
            ->setParameter('user', $user)
            ->orderBy('n.createdAt', 'DESC')
            ->setMaxResults(max(1, $limit))
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Notification[]
     */
    public function findLatestByUser(User $user, int $limit = 10): array
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.user = :user')
            ->setParameter('user', $user)
            ->orderBy('n.createdAt', 'DESC')
            ->setMaxResults(max(1, $limit))
            ->getQuery()
            ->getResult();
    }

    public function countUnreadByUser(User $user): int
    {
        /** @var ScalarCountRow[] $result */
        $result = $this->getEntityManager()
            ->createQuery(
                'SELECT NEW App\\DTO\\ScalarCountRow(COUNT(n.id))
                 FROM App\\Entity\\Notification n
                 WHERE n.user = :user AND n.isRead = false'
            )
            ->setParameter('user', $user)
            ->getResult();

        return $result[0]->count ?? 0;
    }

    /**
     * @return Notification[]
     */
    public function findPagedByUser(User $user, int $page = 1, int $perPage = 20): array
    {
        $page = max(1, $page);
        $perPage = max(1, $perPage);

        return $this->createQueryBuilder('n')
            ->andWhere('n.user = :user')
            ->setParameter('user', $user)
            ->orderBy('n.createdAt', 'DESC')
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage)
            ->getQuery()
            ->getResult();
    }

    public function createPagedByUserQueryBuilder(User $user): QueryBuilder
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.user = :user')
            ->setParameter('user', $user)
            ->orderBy('n.createdAt', 'DESC');
    }

    public function countByUser(User $user): int
    {
        /** @var ScalarCountRow[] $result */
        $result = $this->getEntityManager()
            ->createQuery(
                'SELECT NEW App\\DTO\\ScalarCountRow(COUNT(n.id))
                 FROM App\\Entity\\Notification n
                 WHERE n.user = :user'
            )
            ->setParameter('user', $user)
            ->getResult();

        return $result[0]->count ?? 0;
    }

    public function hasRecentByTypeAndTitle(User $user, string $type, string $title, \DateTimeImmutable $since): bool
    {
        return (int) $this->createQueryBuilder('n')
            ->select('COUNT(n.id)')
            ->andWhere('n.user = :user')
            ->andWhere('n.type = :type')
            ->andWhere('n.title = :title')
            ->andWhere('n.createdAt >= :since')
            ->setParameter('user', $user)
            ->setParameter('type', strtoupper(trim($type)))
            ->setParameter('title', trim($title))
            ->setParameter('since', $since)
            ->getQuery()
            ->getSingleScalarResult() > 0;
    }

    public function deleteReadOlderThan(\DateTimeImmutable $before): int
    {
        return $this->createQueryBuilder('n')
            ->delete()
            ->andWhere('n.isRead = true')
            ->andWhere('n.createdAt < :before')
            ->setParameter('before', $before)
            ->getQuery()
            ->execute();
    }
}
