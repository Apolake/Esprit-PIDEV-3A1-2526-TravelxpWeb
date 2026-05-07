<?php

namespace App\Repository;

use App\Entity\Booking;
use App\Entity\Payment;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Payment>
 */
class PaymentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Payment::class);
    }

    /**
     * @return Payment[]
     */
    public function findByUser(User $user): array
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.booking', 'b')
            ->addSelect('b')
            ->leftJoin('p.budget', 'budget')
            ->addSelect('budget')
            ->andWhere('p.user = :user')
            ->setParameter('user', $user)
            ->orderBy('p.createdAt', 'DESC')
            ->addOrderBy('p.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findLatestByBooking(Booking $booking): ?Payment
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.booking = :booking')
            ->setParameter('booking', $booking)
            ->orderBy('p.createdAt', 'DESC')
            ->addOrderBy('p.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findLatestActiveByBooking(Booking $booking): ?Payment
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.booking = :booking')
            ->andWhere('p.status IN (:activeStatuses)')
            ->setParameter('booking', $booking)
            ->setParameter('activeStatuses', [
                Payment::STATUS_REQUIRES_PAYMENT_METHOD,
                Payment::STATUS_REQUIRES_ACTION,
                Payment::STATUS_PROCESSING,
            ])
            ->orderBy('p.createdAt', 'DESC')
            ->addOrderBy('p.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findOneByPaymentIntentId(string $paymentIntentId): ?Payment
    {
        return $this->findOneBy(['stripePaymentIntentId' => $paymentIntentId]);
    }

    public function findLatestWalletTopUpForUser(User $user): ?Payment
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.user = :user')
            ->andWhere('p.booking IS NULL')
            ->andWhere('p.stripePaymentIntentId LIKE :checkoutPrefix')
            ->setParameter('user', $user)
            ->setParameter('checkoutPrefix', 'cs_%')
            ->orderBy('p.createdAt', 'DESC')
            ->addOrderBy('p.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
