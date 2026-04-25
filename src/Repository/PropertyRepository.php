<?php

namespace App\Repository;

use App\Entity\Property;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Property>
 */
class PropertyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Property::class);
    }

    /**
     * @param array<string, string> $filters
     */
    public function createFilteredQueryBuilder(array $filters): QueryBuilder
    {
        $qb = $this->createQueryBuilder('p');

        $q = trim((string) ($filters['q'] ?? ''));
        if ('' !== $q) {
            $search = '%'.mb_strtolower($q).'%';
            $qb
                ->andWhere('LOWER(p.title) LIKE :search OR LOWER(COALESCE(p.description, \'\')) LIKE :search OR LOWER(p.city) LIKE :search OR LOWER(p.country) LIKE :search')
                ->setParameter('search', $search);
        }

        $propertyType = trim((string) ($filters['propertyType'] ?? ''));
        if ('' !== $propertyType) {
            $qb->andWhere('p.propertyType = :propertyType')->setParameter('propertyType', $propertyType);
        }

        $city = trim((string) ($filters['city'] ?? ''));
        if ('' !== $city) {
            $qb->andWhere('p.city = :city')->setParameter('city', $city);
        }

        $country = trim((string) ($filters['country'] ?? ''));
        if ('' !== $country) {
            $qb->andWhere('p.country = :country')->setParameter('country', $country);
        }

        $active = (string) ($filters['active'] ?? '');
        if (in_array($active, ['0', '1'], true)) {
            $qb->andWhere('p.isActive = :active')->setParameter('active', $active === '1');
        }

        $minPrice = trim((string) ($filters['minPrice'] ?? ''));
        if (is_numeric($minPrice)) {
            $qb->andWhere('p.pricePerNight >= :minPrice')->setParameter('minPrice', number_format((float) $minPrice, 2, '.', ''));
        }

        $maxPrice = trim((string) ($filters['maxPrice'] ?? ''));
        if (is_numeric($maxPrice)) {
            $qb->andWhere('p.pricePerNight <= :maxPrice')->setParameter('maxPrice', number_format((float) $maxPrice, 2, '.', ''));
        }

        $bedrooms = trim((string) ($filters['bedrooms'] ?? ''));
        if (ctype_digit($bedrooms)) {
            $qb->andWhere('p.bedrooms >= :bedrooms')->setParameter('bedrooms', (int) $bedrooms);
        }

        $maxGuests = trim((string) ($filters['maxGuests'] ?? ''));
        if (ctype_digit($maxGuests)) {
            $qb->andWhere('p.maxGuests >= :maxGuests')->setParameter('maxGuests', (int) $maxGuests);
        }

        $sort = (string) ($filters['sort'] ?? 'newest');
        $sortable = [
            'newest' => ['p.createdAt', 'DESC'],
            'oldest' => ['p.createdAt', 'ASC'],
            'price_asc' => ['p.pricePerNight', 'ASC'],
            'price_desc' => ['p.pricePerNight', 'DESC'],
            'title_asc' => ['p.title', 'ASC'],
            'title_desc' => ['p.title', 'DESC'],
        ];
        [$sortField, $sortDirection] = $sortable[$sort] ?? $sortable['newest'];

        return $qb
            ->orderBy($sortField, $sortDirection)
            ->addOrderBy('p.id', 'DESC');
    }

    /**
     * @return list<string>
     */
    public function getDistinctPropertyTypes(): array
    {
        $rows = $this->createQueryBuilder('p')
            ->select('DISTINCT p.propertyType AS value')
            ->andWhere('p.propertyType IS NOT NULL')
            ->orderBy('value', 'ASC')
            ->getQuery()
            ->getScalarResult();

        return array_map(static fn (array $row): string => (string) $row['value'], $rows);
    }

    /**
     * @return list<string>
     */
    public function getDistinctCities(): array
    {
        $rows = $this->createQueryBuilder('p')
            ->select('DISTINCT p.city AS value')
            ->andWhere('p.city IS NOT NULL')
            ->orderBy('value', 'ASC')
            ->getQuery()
            ->getScalarResult();

        return array_map(static fn (array $row): string => (string) $row['value'], $rows);
    }

    /**
     * @return list<string>
     */
    public function getDistinctCountries(): array
    {
        $rows = $this->createQueryBuilder('p')
            ->select('DISTINCT p.country AS value')
            ->andWhere('p.country IS NOT NULL')
            ->orderBy('value', 'ASC')
            ->getQuery()
            ->getScalarResult();

        return array_map(static fn (array $row): string => (string) $row['value'], $rows);
    }
}
