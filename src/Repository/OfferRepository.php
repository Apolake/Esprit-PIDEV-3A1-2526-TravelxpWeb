<?php

namespace App\Repository;

use App\Entity\Offer;
use App\Entity\Property;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Offer>
 */
class OfferRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Offer::class);
    }

    /**
     * @param array<string, string> $filters
     */
    public function createFilteredQueryBuilder(array $filters): QueryBuilder
    {
        $qb = $this->createQueryBuilder('o')
            ->leftJoin('o.property', 'p')
            ->addSelect('p');

        $q = trim((string) ($filters['q'] ?? ''));
        if ('' !== $q) {
            $search = '%'.mb_strtolower($q).'%';
            $qb
                ->andWhere('LOWER(o.title) LIKE :search OR LOWER(COALESCE(o.description, \'\')) LIKE :search OR LOWER(COALESCE(p.title, \'\')) LIKE :search')
                ->setParameter('search', $search);
        }

        $active = (string) ($filters['active'] ?? '');
        if (in_array($active, ['0', '1'], true)) {
            $qb->andWhere('o.isActive = :active')->setParameter('active', $active === '1');
        }

        $propertyId = trim((string) ($filters['propertyId'] ?? ''));
        if (ctype_digit($propertyId)) {
            $qb->andWhere('p.id = :propertyId')->setParameter('propertyId', (int) $propertyId);
        }

        $minDiscount = trim((string) ($filters['minDiscount'] ?? ''));
        if (is_numeric($minDiscount)) {
            $qb->andWhere('o.discountPercentage >= :minDiscount')->setParameter('minDiscount', number_format((float) $minDiscount, 2, '.', ''));
        }

        $maxDiscount = trim((string) ($filters['maxDiscount'] ?? ''));
        if (is_numeric($maxDiscount)) {
            $qb->andWhere('o.discountPercentage <= :maxDiscount')->setParameter('maxDiscount', number_format((float) $maxDiscount, 2, '.', ''));
        }

        if ('1' === (string) ($filters['validNow'] ?? '')) {
            $today = new \DateTimeImmutable('today');
            $qb
                ->andWhere('o.startDate <= :today')
                ->andWhere('o.endDate >= :today')
                ->setParameter('today', $today);
        }

        $sort = (string) ($filters['sort'] ?? 'highest_discount');
        $sortable = [
            'highest_discount' => ['o.discountPercentage', 'DESC'],
            'lowest_discount' => ['o.discountPercentage', 'ASC'],
            'newest' => ['o.createdAt', 'DESC'],
            'oldest' => ['o.createdAt', 'ASC'],
            'ending_soon' => ['o.endDate', 'ASC'],
        ];
        [$sortField, $sortDirection] = $sortable[$sort] ?? $sortable['highest_discount'];

        return $qb
            ->orderBy($sortField, $sortDirection)
            ->addOrderBy('o.id', 'DESC');
    }

    /**
     * @return Property[]
     */
    public function getPropertiesForFilter(): array
    {
        return $this->getEntityManager()
            ->getRepository(Property::class)
            ->createQueryBuilder('p')
            ->orderBy('p.title', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
