<?php

namespace App\Repository;

use App\DTO\RelationCountRow;
use App\Entity\Blog;
use App\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Comment>
 */
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    /**
     * @param array<string, string> $filters
     * @return list<Comment>
     */
    public function findForBlog(Blog $blog, array $filters = []): array
    {
        $qb = $this->createQueryBuilder('c')
            ->leftJoin('c.author', 'a')
            ->leftJoin('c.likedByUsers', 'cl')
            ->addSelect('a')
            ->addSelect('COUNT(DISTINCT cl.id) AS HIDDEN likesCount')
            ->andWhere('c.blog = :blog')
            ->setParameter('blog', $blog)
            ->groupBy('c.id, a.id');

        $authorId = trim((string) ($filters['authorId'] ?? ''));
        if (ctype_digit($authorId)) {
            $qb
                ->andWhere('a.id = :authorId')
                ->setParameter('authorId', (int) $authorId);
        }

        $fromDate = trim((string) ($filters['fromDate'] ?? ''));
        if ($fromDate !== '') {
            $from = \DateTimeImmutable::createFromFormat('Y-m-d', $fromDate);
            if ($from instanceof \DateTimeImmutable) {
                $qb
                    ->andWhere('c.createdAt >= :fromDate')
                    ->setParameter('fromDate', $from->setTime(0, 0, 0));
            }
        }

        $toDate = trim((string) ($filters['toDate'] ?? ''));
        if ($toDate !== '') {
            $to = \DateTimeImmutable::createFromFormat('Y-m-d', $toDate);
            if ($to instanceof \DateTimeImmutable) {
                $qb
                    ->andWhere('c.createdAt <= :toDate')
                    ->setParameter('toDate', $to->setTime(23, 59, 59));
            }
        }

        $sort = (string) ($filters['sort'] ?? 'latest');
        $sortable = [
            'latest' => ['c.createdAt', 'DESC'],
            'oldest' => ['c.createdAt', 'ASC'],
            'most_liked' => ['likesCount', 'DESC'],
            'least_liked' => ['likesCount', 'ASC'],
        ];
        [$sortField, $sortDirection] = $sortable[$sort] ?? $sortable['latest'];

        return $qb
            ->orderBy($sortField, $sortDirection)
            ->addOrderBy('c.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return list<array{id:int,username:string}>
     */
    public function getAuthorsForBlog(Blog $blog): array
    {
        $rows = $this->createQueryBuilder('c')
            ->select('DISTINCT a.id AS id, a.username AS username')
            ->innerJoin('c.author', 'a')
            ->andWhere('c.blog = :blog')
            ->setParameter('blog', $blog)
            ->orderBy('username', 'ASC')
            ->getQuery()
            ->getArrayResult();

        return array_map(static fn (array $row): array => [
            'id' => (int) ($row['id'] ?? 0),
            'username' => (string) ($row['username'] ?? ''),
        ], $rows);
    }

    /**
     * @param list<int> $ids
     *
     * @return array<int, array{likes:int, dislikes:int}>
     */
    public function getReactionCountsForCommentIds(array $ids): array
    {
        if ($ids === []) {
            return [];
        }

        $likesMap = $this->countRelationForCommentIds($ids, 'likedByUsers', 'likes');
        $dislikesMap = $this->countRelationForCommentIds($ids, 'dislikedByUsers', 'dislikes');

        $result = [];
        foreach ($ids as $id) {
            $id = (int) $id;
            $result[$id] = [
                'likes' => $likesMap[$id] ?? 0,
                'dislikes' => $dislikesMap[$id] ?? 0,
            ];
        }

        return $result;
    }

    /**
     * Pre-built DQL fragments for each relation to avoid any string concatenation in queries.
     */
    private const RELATION_MAP = [
        'likes' => ['relation' => 'likedByUsers', 'select' => 'c.id AS id, COUNT(DISTINCT r.id) AS likes', 'join' => 'c.likedByUsers'],
        'dislikes' => ['relation' => 'dislikedByUsers', 'select' => 'c.id AS id, COUNT(DISTINCT r.id) AS dislikes', 'join' => 'c.dislikedByUsers'],
    ];

    /**
     * @param list<int> $ids
     *
     * @return array<int, int>
     */
    private function countRelationForCommentIds(array $ids, string $relation, string $alias): array
    {
        if (!isset(self::RELATION_MAP[$alias]) || self::RELATION_MAP[$alias]['relation'] !== $relation) {
            throw new \InvalidArgumentException(sprintf('Invalid relation "%s" or alias "%s".', $relation, $alias));
        }

        $map = self::RELATION_MAP[$alias];

        /** @var RelationCountRow[] $rows */
        $rows = $this->getEntityManager()
            ->createQuery(sprintf(
                'SELECT NEW App\\DTO\\RelationCountRow(c.id, COUNT(DISTINCT r.id)) FROM App\\Entity\\Comment c LEFT JOIN %s r WHERE c.id IN (:ids) GROUP BY c.id',
                $map['join']
            ))
            ->setParameter('ids', $ids)
            ->getResult();

        $counts = [];
        foreach ($rows as $row) {
            $counts[$row->id] = $row->count;
        }

        return $counts;
    }
}
