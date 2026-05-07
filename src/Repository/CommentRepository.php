<?php

namespace App\Repository;

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
     * @return array<int, array{likes:int,dislikes:int}>
     */
    public function getReactionCountsForCommentIds(array $ids): array
    {
        if ($ids === []) {
            return [];
        }

        $likesRows = $this->createQueryBuilder('c')
            ->select('c.id AS id, COUNT(DISTINCT cl.id) AS likes')
            ->leftJoin('c.likedByUsers', 'cl')
            ->andWhere('c.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->groupBy('c.id')
            ->getQuery()
            ->getArrayResult();

        $likesMap = [];
        foreach ($likesRows as $r) {
            $likesMap[(int) $r['id']] = (int) $r['likes'];
        }

        $dislikeRows = $this->createQueryBuilder('c')
            ->select('c.id AS id, COUNT(DISTINCT cd.id) AS dislikes')
            ->leftJoin('c.dislikedByUsers', 'cd')
            ->andWhere('c.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->groupBy('c.id')
            ->getQuery()
            ->getArrayResult();

        $dislikesMap = [];
        foreach ($dislikeRows as $r) {
            $dislikesMap[(int) $r['id']] = (int) $r['dislikes'];
        }

        $result = [];
        foreach ($ids as $id) {
            $i = (int) $id;
            $result[$i] = [
                'likes' => $likesMap[$i] ?? 0,
                'dislikes' => $dislikesMap[$i] ?? 0,
            ];
        }

        return $result;
    }
}
