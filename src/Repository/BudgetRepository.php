<?php

namespace App\Repository;

use App\Entity\Budget;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Budget>
 */
class BudgetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Budget::class);
    }

    /**
     * @return Budget[]
     */
    public function findByUser(User $user): array
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.user = :user')
            ->setParameter('user', $user)
            ->orderBy('b.startDate', 'DESC')
            ->addOrderBy('b.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getSpentAmountForBudget(Budget $budget): string
    {
        $sum = $this->getEntityManager()
            ->createQuery(
                'SELECT COALESCE(SUM(e.amount), 0) FROM App\Entity\ExpenseEntry e WHERE e.budget = :budget'
            )
            ->setParameter('budget', $budget)
            ->getSingleScalarResult();

        return number_format((float) $sum, 2, '.', '');
    }

    /**
     * @return array<string, float>
     */
    public function getCategoryBreakdownForBudget(Budget $budget): array
    {
        $rows = $this->getEntityManager()
            ->createQuery(
                'SELECT e.category AS category, COALESCE(SUM(e.amount), 0) AS total
                 FROM App\Entity\ExpenseEntry e
                 WHERE e.budget = :budget
                 GROUP BY e.category'
            )
            ->setParameter('budget', $budget)
            ->getArrayResult();

        $breakdown = [];
        foreach ($rows as $row) {
            $breakdown[(string) $row['category']] = (float) $row['total'];
        }

        return $breakdown;
    }

    /**
     * @return array{totalBudget: float, totalSpent: float, remaining: float}
     */
    public function getDashboardTotals(User $user): array
    {
        $budgetSum = $this->createQueryBuilder('b')
            ->select('COALESCE(SUM(b.plannedAmount), 0) AS totalBudget')
            ->andWhere('b.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();

        $spentSum = $this->getEntityManager()
            ->createQuery(
                'SELECT COALESCE(SUM(e.amount), 0) AS totalSpent
                 FROM App\Entity\ExpenseEntry e
                 JOIN e.budget b
                 WHERE b.user = :user'
            )
            ->setParameter('user', $user)
            ->getSingleScalarResult();

        $totalBudget = (float) $budgetSum;
        $totalSpent = (float) $spentSum;

        return [
            'totalBudget' => $totalBudget,
            'totalSpent' => $totalSpent,
            'remaining' => $totalBudget - $totalSpent,
        ];
    }
}
