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
}
