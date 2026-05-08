<?php

namespace App\Repository;

use App\DTO\AuthorFilterRow;
use App\DTO\RelationCountRow;
use App\DTO\SearchSuggestionRow;
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

        /** @var SearchSuggestionRow[] $rows */
        $rows = $this->getEntityManager()
            ->createQuery(
                'SELECT NEW App\\DTO\\SearchSuggestionRow(b.id, b.title, b.content)
                 FROM App\\Entity\\Blog b
                 WHERE LOWER(b.title) LIKE :search OR LOWER(b.content) LIKE :search
                 ORDER BY b.publishedAt DESC'
            )
            ->setParameter('search', '%'.mb_strtolower($q).'%')
            ->setMaxResults(max(1, min(20, $limit)))
            ->getResult();

        return array_map(static fn (SearchSuggestionRow $row): array => $row->toArray(), $rows);
    }

    /**
     * @return list<array{id:int,username:string}>
     */
    public function getAuthorsForFilter(): array
    {
        /** @var AuthorFilterRow[] $rows */
        $rows = $this->getEntityManager()
            ->createQuery(
                'SELECT DISTINCT NEW App\\DTO\\AuthorFilterRow(a.id, a.username)
                 FROM App\\Entity\\Blog b
                 INNER JOIN b.author a
                 ORDER BY a.username ASC'
            )
            ->getResult();

        return array_map(static fn (AuthorFilterRow $row): array => $row->toArray(), $rows);
    }

    /**
     * @param list<int> $ids
     *
     * @return array<int, array{likes:int, dislikes:int, comments:int}>
     */
    public function getCountsForBlogIds(array $ids): array
    {
        if ($ids === []) {
            return [];
        }

        $likesMap = $this->countRelationForBlogIds($ids, 'likedByUsers', 'likes');
        $dislikesMap = $this->countRelationForBlogIds($ids, 'dislikedByUsers', 'dislikes');
        $commentsMap = $this->countRelationForBlogIds($ids, 'comments', 'comments');

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

    /**
     * Pre-built DQL fragments for each relation to avoid any string concatenation in queries.
     */
    private const RELATION_MAP = [
        'likes' => ['relation' => 'likedByUsers', 'select' => 'b.id AS id, COUNT(DISTINCT r.id) AS likes', 'join' => 'b.likedByUsers'],
        'dislikes' => ['relation' => 'dislikedByUsers', 'select' => 'b.id AS id, COUNT(DISTINCT r.id) AS dislikes', 'join' => 'b.dislikedByUsers'],
        'comments' => ['relation' => 'comments', 'select' => 'b.id AS id, COUNT(DISTINCT r.id) AS comments', 'join' => 'b.comments'],
    ];

    /**
     * @param list<int> $ids
     *
     * @return array<int, int>
     */
    private function countRelationForBlogIds(array $ids, string $relation, string $alias): array
    {
        if (!isset(self::RELATION_MAP[$alias]) || self::RELATION_MAP[$alias]['relation'] !== $relation) {
            throw new \InvalidArgumentException(sprintf('Invalid relation "%s" or alias "%s".', $relation, $alias));
        }

        $map = self::RELATION_MAP[$alias];

        /** @var RelationCountRow[] $rows */
        $rows = $this->getEntityManager()
            ->createQuery(sprintf(
                'SELECT NEW App\\DTO\\RelationCountRow(b.id, COUNT(DISTINCT r.id)) FROM App\\Entity\\Blog b LEFT JOIN %s r WHERE b.id IN (:ids) GROUP BY b.id',
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
