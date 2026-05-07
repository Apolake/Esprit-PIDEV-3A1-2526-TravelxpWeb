<?php

namespace App\Controller;

use App\Entity\Budget;
use App\Entity\ExpenseEntry;
use App\Entity\User;
use App\Form\BudgetType;
use App\Form\ExpenseEntryType;
use App\Repository\BudgetRepository;
use App\Repository\ExpenseEntryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/budgets')]
#[IsGranted('ROLE_USER')]
class BudgetController extends AbstractController
{
    public function __construct(
        #[Autowire('%env(string:DEFAULT_BUDGET_CURRENCY)%')]
        private readonly string $defaultCurrency,
        #[Autowire('%env(float:BUDGET_WARNING_THRESHOLD)%')]
        private readonly float $warningThreshold,
    ) {
    }

    #[Route('', name: 'budget_index', methods: ['GET'])]
    public function index(BudgetRepository $budgetRepository): Response
    {
        $user = $this->getCurrentUser();
        $budgets = $budgetRepository->findByUser($user);
        $totals = $budgetRepository->getDashboardTotals($user);

        return $this->render('budget/index.html.twig', [
            'budgets' => $budgets,
            'totals' => $totals,
            'warningThreshold' => $this->warningThreshold,
        ]);
    }

    #[Route('/new', name: 'budget_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $budget = new Budget();
        $budget->setCurrency(strtoupper($this->defaultCurrency));
        $budget->setUser($this->getCurrentUser());

        $form = $this->createForm(BudgetType::class, $budget);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($budget);
            $entityManager->flush();
            $this->addFlash('success', 'Budget created successfully.');

            return $this->redirectToRoute('budget_index');
        }

        return $this->render('budget/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'budget_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(Budget $budget, BudgetRepository $budgetRepository, ExpenseEntryRepository $expenseEntryRepository): Response
    {
        $this->assertBudgetOwnership($budget);

        $spentAmount = (float) $budgetRepository->getSpentAmountForBudget($budget);
        $plannedAmount = (float) $budget->getPlannedAmount();
        $remainingAmount = $plannedAmount - $spentAmount;
        $spentRatio = $plannedAmount > 0 ? $spentAmount / $plannedAmount : 0.0;

        return $this->render('budget/show.html.twig', [
            'budget' => $budget,
            'expenses' => $expenseEntryRepository->findByBudget($budget),
            'spentAmount' => $spentAmount,
            'remainingAmount' => $remainingAmount,
            'spentRatio' => $spentRatio,
            'categoryBreakdown' => $budgetRepository->getCategoryBreakdownForBudget($budget),
            'warningThreshold' => $this->warningThreshold,
        ]);
    }

    #[Route('/{id}/edit', name: 'budget_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(Budget $budget, Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->assertBudgetOwnership($budget);

        $form = $this->createForm(BudgetType::class, $budget);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Budget updated successfully.');

            return $this->redirectToRoute('budget_show', ['id' => $budget->getId()]);
        }

        return $this->render('budget/edit.html.twig', [
            'budget' => $budget,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'budget_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function delete(Budget $budget, Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->assertBudgetOwnership($budget);

        if (!$this->isCsrfTokenValid('delete_budget_'.$budget->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('error', 'Invalid CSRF token.');

            return $this->redirectToRoute('budget_index');
        }

        $entityManager->remove($budget);
        $entityManager->flush();
        $this->addFlash('success', 'Budget deleted.');

        return $this->redirectToRoute('budget_index');
    }

    #[Route('/{id}/expenses/new', name: 'budget_expense_new', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function newExpense(Budget $budget, Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->assertBudgetOwnership($budget);

        $expense = new ExpenseEntry();
        $expense->setBudget($budget);
        $expense->setExpenseDate(new \DateTimeImmutable('today'));

        $form = $this->createForm(ExpenseEntryType::class, $expense);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($expense);
            $entityManager->flush();
            $this->addFlash('success', 'Expense added.');

            return $this->redirectToRoute('budget_show', ['id' => $budget->getId()]);
        }

        return $this->render('budget/expense_new.html.twig', [
            'budget' => $budget,
            'form' => $form,
        ]);
    }

    #[Route('/{budgetId}/expenses/{id}/edit', name: 'budget_expense_edit', requirements: ['budgetId' => '\d+', 'id' => '\d+'], methods: ['GET', 'POST'])]
    public function editExpense(
        int $budgetId,
        ExpenseEntry $expense,
        Request $request,
        EntityManagerInterface $entityManager,
    ): Response {
        $budget = $expense->getBudget();
        if (!$budget instanceof Budget || $budget->getId() !== $budgetId) {
            throw $this->createNotFoundException('Expense not found in this budget.');
        }

        $this->assertBudgetOwnership($budget);

        $form = $this->createForm(ExpenseEntryType::class, $expense);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Expense updated.');

            return $this->redirectToRoute('budget_show', ['id' => $budget->getId()]);
        }

        return $this->render('budget/expense_edit.html.twig', [
            'budget' => $budget,
            'expense' => $expense,
            'form' => $form,
        ]);
    }

    #[Route('/{budgetId}/expenses/{id}/delete', name: 'budget_expense_delete', requirements: ['budgetId' => '\d+', 'id' => '\d+'], methods: ['POST'])]
    public function deleteExpense(
        int $budgetId,
        ExpenseEntry $expense,
        Request $request,
        EntityManagerInterface $entityManager,
    ): Response {
        $budget = $expense->getBudget();
        if (!$budget instanceof Budget || $budget->getId() !== $budgetId) {
            throw $this->createNotFoundException('Expense not found in this budget.');
        }

        $this->assertBudgetOwnership($budget);

        if (!$this->isCsrfTokenValid('delete_expense_'.$expense->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('error', 'Invalid CSRF token.');

            return $this->redirectToRoute('budget_show', ['id' => $budget->getId()]);
        }

        $entityManager->remove($expense);
        $entityManager->flush();
        $this->addFlash('success', 'Expense removed.');

        return $this->redirectToRoute('budget_show', ['id' => $budget->getId()]);
    }

    private function assertBudgetOwnership(Budget $budget): void
    {
        $user = $this->getCurrentUser();
        if ($budget->getUser()?->getId() !== $user->getId()) {
            throw $this->createAccessDeniedException('You can only access your own budgets.');
        }
    }

    private function getCurrentUser(): User
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw $this->createAccessDeniedException('User not authenticated.');
        }

        return $user;
    }
}
