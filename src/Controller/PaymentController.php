<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Entity\Payment;
use App\Entity\User;
use App\Repository\BudgetRepository;
use App\Repository\PaymentRepository;
use App\Service\StripePaymentService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class PaymentController extends AbstractController
{
    #[Route('/bookings/{id}/payment', name: 'booking_payment', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function checkout(
        Booking $booking,
        PaymentRepository $paymentRepository,
        BudgetRepository $budgetRepository,
    ): Response {
        $user = $this->getCurrentUser();
        $this->assertBookingOwnership($booking, $user);

        if ($booking->isCancelled()) {
            $this->addFlash('error', 'Cancelled bookings cannot be paid.');

            return $this->redirectToRoute('booking_show', ['id' => $booking->getId()]);
        }

        $payment = $paymentRepository->findLatestByBooking($booking);

        return $this->render('payment/checkout.html.twig', [
            'booking' => $booking,
            'payment' => $payment,
            'budgets' => $budgetRepository->findByUser($user),
            'walletBalance' => (float) $user->getBalance(),
            'bookingTotal' => (float) $booking->getTotalPrice(),
        ]);
    }

    #[Route('/bookings/{id}/payment/pay', name: 'booking_pay_wallet', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function payBookingFromWallet(
        Booking $booking,
        Request $request,
        BudgetRepository $budgetRepository,
        EntityManagerInterface $entityManager,
    ): Response {
        $user = $this->getCurrentUser();
        $this->assertBookingOwnership($booking, $user);

        if (!$this->isCsrfTokenValid('pay_booking_wallet_'.$booking->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('error', 'Invalid CSRF token.');

            return $this->redirectToRoute('booking_payment', ['id' => $booking->getId()]);
        }

        if ($booking->isCancelled()) {
            $this->addFlash('error', 'Cancelled bookings cannot be paid.');

            return $this->redirectToRoute('booking_show', ['id' => $booking->getId()]);
        }

        if ($booking->hasSuccessfulPayment()) {
            $this->addFlash('info', 'This booking is already paid.');

            return $this->redirectToRoute('booking_payment', ['id' => $booking->getId()]);
        }

        $balance = (float) $user->getBalance();
        $amount = (float) $booking->getTotalPrice();
        if ($balance < $amount) {
            $this->addFlash('error', 'Insufficient wallet balance. Recharge your wallet first.');

            return $this->redirectToRoute('booking_payment', ['id' => $booking->getId()]);
        }

        $budgetId = $this->parseOptionalPositiveInt($request, 'budget_id');
        $budget = $budgetId > 0 ? $budgetRepository->find($budgetId) : null;
        if ($budget !== null && $budget->getUser()?->getId() !== $user->getId()) {
            throw $this->createAccessDeniedException('You can only link your own budgets.');
        }

        $user->setBalance($this->formatMoney($balance - $amount));

        $payment = new Payment();
        $payment
            ->setUser($user)
            ->setBooking($booking)
            ->setBudget($budget)
            ->setStripePaymentIntentId($this->generateWalletReference('BOOKING'))
            ->setAmount($amount)
            ->setCurrency('USD')
            ->setStatus(Payment::STATUS_SUCCEEDED)
            ->setFailureMessage(null);

        $booking->setStatus(Booking::STATUS_CONFIRMED);

        $entityManager->persist($payment);
        $entityManager->flush();

        $this->addFlash('success', 'Booking paid successfully from your wallet.');

        return $this->redirectToRoute('payment_history');
    }

    #[Route('/payments/{id}/budget', name: 'app_payment_link_budget', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function linkBudget(
        Payment $payment,
        Request $request,
        BudgetRepository $budgetRepository,
        EntityManagerInterface $entityManager,
    ): Response {
        $user = $this->getCurrentUser();
        if ($payment->getUser()?->getId() !== $user->getId()) {
            throw $this->createNotFoundException('Payment not found.');
        }

        if (!$this->isCsrfTokenValid('link_payment_budget_'.$payment->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('error', 'Invalid CSRF token.');

            return $this->redirectToRoute('payment_history');
        }

        $budgetId = $this->parseOptionalPositiveInt($request, 'budget_id');
        $budget = $budgetId > 0 ? $budgetRepository->find($budgetId) : null;
        if ($budget !== null && $budget->getUser()?->getId() !== $user->getId()) {
            throw $this->createAccessDeniedException('You can only link your own budgets.');
        }

        $payment->setBudget($budget);
        $entityManager->flush();
        $this->addFlash('success', 'Payment budget link updated.');

        $booking = $payment->getBooking();
        if ($booking instanceof Booking) {
            return $this->redirectToRoute('booking_payment', ['id' => $booking->getId()]);
        }

        return $this->redirectToRoute('payment_history');
    }

    #[Route('/payments/wallet/top-up', name: 'app_wallet_top_up', methods: ['POST'])]
    public function topUpWallet(
        Request $request,
        StripePaymentService $stripePaymentService,
        EntityManagerInterface $entityManager,
        LoggerInterface $logger,
    ): Response
    {
        $user = $this->getCurrentUser();

        if (!$this->isCsrfTokenValid('wallet_top_up', (string) $request->request->get('_token'))) {
            $this->addFlash('error', 'Invalid CSRF token.');

            return $this->redirectToRoute('payment_history');
        }

        $amount = (float) $request->request->get('amount', 0);
        if ($amount <= 0) {
            $this->addFlash('error', 'Top-up amount must be greater than zero.');

            return $this->redirectToRoute('payment_history');
        }

        try {
            $payment = new Payment();
            $payment
                ->setUser($user)
                ->setStripePaymentIntentId($this->generateWalletReference('TOPUP'))
                ->setAmount($amount)
                ->setCurrency('USD')
                ->setStatus(Payment::STATUS_REQUIRES_PAYMENT_METHOD)
                ->setFailureMessage(null);

            $entityManager->persist($payment);
            $entityManager->flush();

            $checkoutSession = $stripePaymentService->createWalletTopUpCheckoutSession(
                $amount,
                $user,
                (int) $payment->getId(),
                $this->buildWalletTopUpSuccessUrl((int) $payment->getId()),
                $this->generateUrl('app_wallet_top_up_return', [
                    'result' => 'cancel',
                    'payment_id' => (int) $payment->getId(),
                ], UrlGeneratorInterface::ABSOLUTE_URL),
            );

            $payment->setStripePaymentIntentId((string) $checkoutSession->id);
            $entityManager->flush();

            $checkoutUrl = (string) ($checkoutSession->url ?? '');
            if ($checkoutUrl === '') {
                throw new \RuntimeException('Stripe checkout URL was not returned.');
            }

            $logger->info('Wallet checkout session created.', [
                'user_id' => $user->getId(),
                'payment_id' => $payment->getId(),
                'session_id' => (string) $checkoutSession->id,
                'checkout_url' => $checkoutUrl,
            ]);

            return $this->redirect($checkoutUrl, Response::HTTP_SEE_OTHER);
        } catch (\Throwable $exception) {
            $logger->error('Wallet top-up checkout creation failed.', [
                'user_id' => $user->getId(),
                'message' => $exception->getMessage(),
                'exception_class' => $exception::class,
            ]);
            $this->addFlash('error', $exception->getMessage());

            return $this->redirectToRoute('payment_history');
        }
    }

    #[Route('/payments/history', name: 'payment_history', methods: ['GET'])]
    public function history(PaymentRepository $paymentRepository): Response
    {
        return $this->render('payment/history.html.twig', [
            'payments' => $paymentRepository->findByUser($this->getCurrentUser()),
            'walletBalance' => (float) $this->getCurrentUser()->getBalance(),
        ]);
    }

    #[Route('/payments/wallet/top-up/return', name: 'app_wallet_top_up_return', methods: ['GET'])]
    public function completeWalletTopUp(
        Request $request,
        PaymentRepository $paymentRepository,
        StripePaymentService $stripePaymentService,
        EntityManagerInterface $entityManager,
        LoggerInterface $logger,
    ): Response {
        $user = $this->getCurrentUser();

        $result = (string) $request->query->get('result', '');
        $sessionId = trim((string) $request->query->get('session_id', ''));
        $paymentId = $request->query->getInt('payment_id');

        if ($result === 'cancel') {
            $this->addFlash('warning', 'Wallet recharge was cancelled.');

            return $this->redirectToRoute('payment_history');
        }

        try {
            if ($sessionId === '' || str_contains($sessionId, '{CHECKOUT_SESSION_ID}')) {
                $fallbackPayment = $paymentId > 0 ? $paymentRepository->find($paymentId) : null;
                if (
                    !$fallbackPayment instanceof Payment
                    || $fallbackPayment->getUser()?->getId() !== $user->getId()
                    || $fallbackPayment->getBooking() !== null
                ) {
                    $fallbackPayment = $paymentRepository->findLatestWalletTopUpForUser($user);
                }

                if (
                    $fallbackPayment instanceof Payment
                    && $fallbackPayment->getUser()?->getId() === $user->getId()
                    && $fallbackPayment->getBooking() === null
                    && str_starts_with((string) $fallbackPayment->getStripePaymentIntentId(), 'cs_')
                ) {
                    $sessionId = (string) $fallbackPayment->getStripePaymentIntentId();
                    $logger->warning('Wallet return received placeholder session id; using stored session id from payment.', [
                        'user_id' => $user->getId(),
                        'payment_id' => $fallbackPayment->getId(),
                        'resolved_session_id' => $sessionId,
                    ]);
                }
            }

            if ($sessionId === '' || str_contains($sessionId, '{CHECKOUT_SESSION_ID}')) {
                $this->addFlash('error', 'Missing Stripe checkout session.');

                return $this->redirectToRoute('payment_history');
            }

            $payment = $paymentRepository->findOneByPaymentIntentId($sessionId);
            if (!$payment instanceof Payment && $paymentId > 0) {
                $payment = $paymentRepository->find($paymentId);
            }

            if (!$payment instanceof Payment || $payment->getUser()?->getId() !== $user->getId() || $payment->getBooking() !== null) {
                $this->addFlash('error', 'Top-up not found.');

                return $this->redirectToRoute('payment_history');
            }

            $checkoutSession = $stripePaymentService->retrieveCheckoutSession($sessionId);
            $paymentIntent = $checkoutSession->payment_intent ?? null;
            $previousStatus = $payment->getStatus();

            if (($checkoutSession->payment_status ?? '') === 'paid' && $paymentIntent !== null) {
                $payment
                    ->setStatus(Payment::STATUS_SUCCEEDED)
                    ->setCurrency(strtoupper((string) ($paymentIntent->currency ?? 'USD')))
                    ->setAmount($stripePaymentService->amountFromCents((int) ($paymentIntent->amount ?? 0)))
                    ->setFailureMessage(null);
            } else {
                $payment
                    ->setStatus((string) ($paymentIntent->status ?? Payment::STATUS_FAILED))
                    ->setFailureMessage((string) ($paymentIntent->last_payment_error->message ?? ''));
            }

            if ($payment->isSuccessful() && $previousStatus !== Payment::STATUS_SUCCEEDED) {
                $user->setBalance($this->formatMoney((float) $user->getBalance() + (float) $payment->getAmount()));
                $this->addFlash('success', 'Wallet recharge successful.');
            } elseif (!$payment->isSuccessful()) {
                $this->addFlash('error', $payment->getFailureMessage() ?? 'Wallet recharge is not completed yet.');
            }

            $logger->info('Wallet top-up return handled.', [
                'user_id' => $user->getId(),
                'payment_id' => $payment->getId(),
                'session_id' => $sessionId,
                'status' => $payment->getStatus(),
            ]);

            $entityManager->flush();

            return $this->redirectToRoute('payment_history');
        } catch (\Throwable $exception) {
            $logger->error('Wallet top-up return verification failed.', [
                'user_id' => $user->getId(),
                'session_id' => $sessionId,
                'message' => $exception->getMessage(),
                'exception_class' => $exception::class,
            ]);
            $this->addFlash('error', $exception->getMessage() !== '' ? $exception->getMessage() : 'Unable to verify wallet recharge status.');

            return $this->redirectToRoute('payment_history');
        }
    }

    private function assertBookingOwnership(Booking $booking, User $user): void
    {
        if ($booking->getUser()?->getId() !== $user->getId()) {
            throw $this->createAccessDeniedException('You can only pay your own bookings.');
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

    private function generateWalletReference(string $type): string
    {
        return sprintf('WALLET-%s-%s', strtoupper($type), strtoupper(bin2hex(random_bytes(6))));
    }

    private function formatMoney(float $amount): string
    {
        return number_format(max(0, $amount), 2, '.', '');
    }

    private function parseOptionalPositiveInt(Request $request, string $key): int
    {
        $raw = trim((string) $request->request->get($key, ''));

        return preg_match('/^\d+$/', $raw) === 1 ? (int) $raw : 0;
    }

    private function buildWalletTopUpSuccessUrl(int $paymentId): string
    {
        $placeholder = 'CHECKOUT_SESSION_ID_TOKEN';
        $successUrl = $this->generateUrl('app_wallet_top_up_return', [
            'result' => 'success',
            'session_id' => $placeholder,
            'payment_id' => $paymentId,
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        return str_replace($placeholder, '{CHECKOUT_SESSION_ID}', $successUrl);
    }
}
