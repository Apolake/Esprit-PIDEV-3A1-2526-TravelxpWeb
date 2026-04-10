<?php

namespace App\Repository;

use App\Entity\Service;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Service>
 */
class ServiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Service::class);
    }

    /**
     * @param array<string, string> $filters
     */
    public function createFilteredQueryBuilder(array $filters): QueryBuilder
    {
        $qb = $this->createQueryBuilder('s');

        $q = trim((string) ($filters['q'] ?? ''));
        if ('' !== $q) {
            $search = '%'.mb_strtolower($q).'%';
            $qb
                ->andWhere('LOWER(s.providerName) LIKE :search OR LOWER(s.serviceType) LIKE :search OR LOWER(COALESCE(s.description, \'\')) LIKE :search')
                ->setParameter('search', $search);
        }

        $serviceType = trim((string) ($filters['serviceType'] ?? ''));
        if ('' !== $serviceType) {
            $qb->andWhere('s.serviceType = :serviceType')->setParameter('serviceType', $serviceType);
        }

        if ('1' === (string) ($filters['availableOnly'] ?? '')) {
            $qb->andWhere('s.isAvailable = :available')->setParameter('available', true);
        }

        if ('1' === (string) ($filters['ecoOnly'] ?? '')) {
            $qb->andWhere('s.ecoFriendly = :eco')->setParameter('eco', true);
        }

        $minPrice = trim((string) ($filters['minPrice'] ?? ''));
        if (is_numeric($minPrice)) {
            $qb->andWhere('s.price >= :minPrice')->setParameter('minPrice', number_format((float) $minPrice, 2, '.', ''));
        }

        $maxPrice = trim((string) ($filters['maxPrice'] ?? ''));
        if (is_numeric($maxPrice)) {
            $qb->andWhere('s.price <= :maxPrice')->setParameter('maxPrice', number_format((float) $maxPrice, 2, '.', ''));
        }

        $sort = (string) ($filters['sort'] ?? 'newest');
        $sortable = [
            'newest' => ['s.createdAt', 'DESC'],
            'oldest' => ['s.createdAt', 'ASC'],
            'price_asc' => ['s.price', 'ASC'],
            'price_desc' => ['s.price', 'DESC'],
            'provider_asc' => ['s.providerName', 'ASC'],
            'provider_desc' => ['s.providerName', 'DESC'],
        ];
        [$sortField, $sortDirection] = $sortable[$sort] ?? $sortable['newest'];

        return $qb
            ->orderBy($sortField, $sortDirection)
            ->addOrderBy('s.id', 'DESC');
    }

    /**
     * @return list<string>
     */
    public function getDistinctServiceTypes(): array
    {
        $rows = $this->createQueryBuilder('s')
            ->select('DISTINCT s.serviceType AS value')
            ->andWhere('s.serviceType IS NOT NULL')
            ->orderBy('value', 'ASC')
            ->getQuery()
            ->getScalarResult();

        return array_map(static fn (array $row): string => (string) $row['value'], $rows);
    }
}
