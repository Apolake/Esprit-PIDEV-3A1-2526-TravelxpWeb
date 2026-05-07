<?php

namespace App\Repository;

use App\Entity\Blog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Blog>
 */
class BlogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Blog::class);
    }

    /**
     * @param array<string, string> $filters
     */
    public function createFilteredQueryBuilder(array $filters): QueryBuilder
    {
        $qb = $this->createQueryBuilder('b')
            ->leftJoin('b.author', 'a')
            ->leftJoin('b.likedByUsers', 'lb')
            ->addSelect('a')
            ->addSelect('COUNT(DISTINCT lb.id) AS HIDDEN likesCount')
            ->groupBy('b.id, a.id');

        $q = trim((string) ($filters['q'] ?? ''));
        if ('' !== $q) {
            $search = '%'.mb_strtolower($q).'%';
            $qb
                ->andWhere("LOWER(b.title) LIKE :search OR LOWER(b.content) LIKE :search OR LOWER(COALESCE(a.username, '')) LIKE :search")
                ->setParameter('search', $search);
        }

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
                    ->andWhere('b.publishedAt >= :fromDate')
                    ->setParameter('fromDate', $from->setTime(0, 0, 0));
            }
        }

        $toDate = trim((string) ($filters['toDate'] ?? ''));
        if ($toDate !== '') {
            $to = \DateTimeImmutable::createFromFormat('Y-m-d', $toDate);
            if ($to instanceof \DateTimeImmutable) {
                $qb
                    ->andWhere('b.publishedAt <= :toDate')
                    ->setParameter('toDate', $to->setTime(23, 59, 59));
            }
        }

        $sort = (string) ($filters['sort'] ?? 'latest');
        $sortable = [
            'latest' => ['b.publishedAt', 'DESC'],
            'oldest' => ['b.publishedAt', 'ASC'],
            'most_liked' => ['likesCount', 'DESC'],
            'least_liked' => ['likesCount', 'ASC'],
        ];
        [$sortField, $sortDirection] = $sortable[$sort] ?? $sortable['latest'];

        return $qb
            ->orderBy($sortField, $sortDirection)
            ->addOrderBy('b.id', 'DESC');
    }

    /**
     * @return list<array{id:int,title:string,excerpt:string}>
     */
    public function findLiveSuggestions(string $query, int $limit = 8): array
    {
        $q = trim($query);
        if ($q === '') {
            return [];
        }

        $rows = $this->createQueryBuilder('b')
            ->select('b.id AS id, b.title AS title, b.content AS content')
            ->andWhere('LOWER(b.title) LIKE :search OR LOWER(b.content) LIKE :search')
            ->setParameter('search', '%'.mb_strtolower($q).'%')
            ->orderBy('b.publishedAt', 'DESC')
            ->setMaxResults(max(1, min(20, $limit)))
            ->getQuery()
            ->getArrayResult();

        return array_map(static function (array $row): array {
            $content = trim((string) ($row['content'] ?? ''));
            $excerpt = mb_substr($content, 0, 90);

            return [
                'id' => (int) ($row['id'] ?? 0),
                'title' => (string) ($row['title'] ?? ''),
                'excerpt' => $excerpt . (mb_strlen($content) > 90 ? '...' : ''),
            ];
        }, $rows);
    }

    /**
     * @return list<array{id:int,username:string}>
     */
    public function getAuthorsForFilter(): array
    {
        $rows = $this->createQueryBuilder('b')
            ->select('DISTINCT a.id AS id, a.username AS username')
            ->innerJoin('b.author', 'a')
            ->orderBy('username', 'ASC')
            ->getQuery()
            ->getArrayResult();

        return array_map(static fn (array $row): array => [
            'id' => (int) ($row['id'] ?? 0),
            'username' => (string) ($row['username'] ?? ''),
        ], $rows);
    }

    /**
     * Return reaction and comment counts for a list of blog ids.
     *
     * @param list<int> $ids
     * @return array<int, array{likes:int,dislikes:int,comments:int}>
     */
    public function getCountsForBlogIds(array $ids): array
    {
        if ($ids === []) {
            return [];
        }

        // likes
        $likesRows = $this->createQueryBuilder('b')
            ->select('b.id AS id, COUNT(DISTINCT lb.id) AS likes')
            ->leftJoin('b.likedByUsers', 'lb')
            ->andWhere('b.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->groupBy('b.id')
            ->getQuery()
            ->getArrayResult();

        $likesMap = [];
        foreach ($likesRows as $row) {
            $likesMap[(int) $row['id']] = (int) $row['likes'];
        }

        // dislikes
        $dislikeRows = $this->createQueryBuilder('b')
            ->select('b.id AS id, COUNT(DISTINCT db.id) AS dislikes')
            ->leftJoin('b.dislikedByUsers', 'db')
            ->andWhere('b.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->groupBy('b.id')
            ->getQuery()
            ->getArrayResult();

        $dislikesMap = [];
        foreach ($dislikeRows as $row) {
            $dislikesMap[(int) $row['id']] = (int) $row['dislikes'];
        }

        // comments count
        $commentRows = $this->createQueryBuilder('b')
            ->select('b.id AS id, COUNT(DISTINCT c.id) AS comments')
            ->leftJoin('b.comments', 'c')
            ->andWhere('b.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->groupBy('b.id')
            ->getQuery()
            ->getArrayResult();

        $commentsMap = [];
        foreach ($commentRows as $row) {
            $commentsMap[(int) $row['id']] = (int) $row['comments'];
        }

        $result = [];
        foreach ($ids as $id) {
            $id = (int) $id;
            $result[$id] = [
                'likes' => $likesMap[$id] ?? 0,
                'dislikes' => $dislikesMap[$id] ?? 0,
                'comments' => $commentsMap[$id] ?? 0,
            ];
        }

        return $result;
    }
}
