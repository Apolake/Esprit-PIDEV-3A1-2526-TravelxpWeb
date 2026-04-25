<?php

namespace App\Repository;

use App\Entity\Booking;
use App\Entity\Property;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Booking>
 */
class BookingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Booking::class);
    }

    /**
     * @param array<string, string> $filters
     */
    public function createFilteredQueryBuilder(array $filters): QueryBuilder
    {
        $qb = $this->createQueryBuilder('b')
            ->leftJoin('b.property', 'p')
            ->addSelect('p');

        $q = trim((string) ($filters['q'] ?? ''));
        if ('' !== $q) {
            $search = '%'.mb_strtolower($q).'%';
            $orX = $qb->expr()->orX(
                'LOWER(COALESCE(p.title, \'\')) LIKE :search',
                'LOWER(b.status) LIKE :search'
            );

            if (ctype_digit($q)) {
                $orX->add('b.userId = :userIdSearch');
                $qb->setParameter('userIdSearch', (int) $q);
            }

            $qb
                ->andWhere($orX)
                ->setParameter('search', $search);
        }

        $status = trim((string) ($filters['status'] ?? ''));
        if (in_array($status, [Booking::STATUS_PENDING, Booking::STATUS_CONFIRMED, Booking::STATUS_CANCELLED], true)) {
            $qb->andWhere('b.status = :status')->setParameter('status', $status);
        }

        $propertyId = trim((string) ($filters['propertyId'] ?? ''));
        if (ctype_digit($propertyId)) {
            $qb->andWhere('p.id = :propertyId')->setParameter('propertyId', (int) $propertyId);
        }

        $userId = trim((string) ($filters['userId'] ?? ''));
        if (ctype_digit($userId)) {
            $qb->andWhere('b.userId = :userId')->setParameter('userId', (int) $userId);
        }

        $fromDate = trim((string) ($filters['fromDate'] ?? ''));
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $fromDate) === 1) {
            $parsedFromDate = \DateTimeImmutable::createFromFormat('Y-m-d', $fromDate);
            if ($parsedFromDate instanceof \DateTimeImmutable) {
                $qb->andWhere('b.bookingDate >= :fromDate')->setParameter('fromDate', $parsedFromDate);
            }
        }

        $toDate = trim((string) ($filters['toDate'] ?? ''));
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $toDate) === 1) {
            $parsedToDate = \DateTimeImmutable::createFromFormat('Y-m-d', $toDate);
            if ($parsedToDate instanceof \DateTimeImmutable) {
                $qb->andWhere('b.bookingDate <= :toDate')->setParameter('toDate', $parsedToDate);
            }
        }

        $minTotal = trim((string) ($filters['minTotal'] ?? ''));
        if (is_numeric($minTotal)) {
            $qb->andWhere('b.totalPrice >= :minTotal')->setParameter('minTotal', number_format((float) $minTotal, 2, '.', ''));
        }

        $maxTotal = trim((string) ($filters['maxTotal'] ?? ''));
        if (is_numeric($maxTotal)) {
            $qb->andWhere('b.totalPrice <= :maxTotal')->setParameter('maxTotal', number_format((float) $maxTotal, 2, '.', ''));
        }

        if ('1' === (string) ($filters['futureOnly'] ?? '')) {
            $qb->andWhere('b.bookingDate >= :today')->setParameter('today', new \DateTimeImmutable('today'));
        }

        if ('1' === (string) ($filters['cancelledOnly'] ?? '')) {
            $qb->andWhere('b.status = :cancelledStatus')->setParameter('cancelledStatus', Booking::STATUS_CANCELLED);
        }

        $sort = (string) ($filters['sort'] ?? 'newest');
        $sortable = [
            'newest' => ['b.createdAt', 'DESC'],
            'oldest' => ['b.createdAt', 'ASC'],
            'date_asc' => ['b.bookingDate', 'ASC'],
            'date_desc' => ['b.bookingDate', 'DESC'],
            'total_asc' => ['b.totalPrice', 'ASC'],
            'total_desc' => ['b.totalPrice', 'DESC'],
        ];
        [$sortField, $sortDirection] = $sortable[$sort] ?? $sortable['newest'];

        return $qb
            ->orderBy($sortField, $sortDirection)
            ->addOrderBy('b.id', 'DESC');
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
