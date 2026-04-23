<?php

namespace App\Repository;

use App\Entity\Budget;
use App\Entity\ExpenseEntry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ExpenseEntry>
 */
class ExpenseEntryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExpenseEntry::class);
    }

    /**
     * @return ExpenseEntry[]
     */
    public function findByBudget(Budget $budget): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.budget = :budget')
            ->setParameter('budget', $budget)
            ->orderBy('e.expenseDate', 'DESC')
            ->addOrderBy('e.id', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
