<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Entity\User;
use App\Form\BookingType;
use App\Repository\BookingRepository;
use App\Repository\PropertyRepository;
<<<<<<< Updated upstream
use App\Service\BookingPricingService;
use App\Service\CurrencyConverterService;
=======
use App\Repository\ServiceRepository;
use App\Service\BookingPricingService;
use App\Service\CurrencyConverterService;
use App\Service\GeminiAssistantService;
>>>>>>> Stashed changes
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class BookingController extends AbstractController
{
    #[Route('/admin/bookings', name: 'admin_booking_index', methods: ['GET'])]
    #[Route('/bookings', name: 'booking_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(
        Request $request,
        BookingRepository $bookingRepository,
        CurrencyConverterService $currencyConverter
    ): Response {
        $isAdmin = str_starts_with((string) $request->attributes->get('_route'), 'admin_');
        $filters = $this->extractFilters($request);
        $selectedCurrency = $currencyConverter->normalizeCurrency($request->query->get('currency', 'USD'));
        $authenticated = $this->getUser();
        $currentUser = $authenticated instanceof User ? $authenticated : null;
        if (!$isAdmin && $currentUser?->getId() !== null) {
            $filters['userId'] = (string) $currentUser->getId();
        }

        $page = max(1, $request->query->getInt('page', 1));
        $perPage = $isAdmin ? 10 : 9;

        $qb = $bookingRepository->createFilteredQueryBuilder($filters);
        $qb
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage);

        $paginator = new Paginator($qb, true);
        $totalItems = count($paginator);
        $totalPages = max(1, (int) ceil($totalItems / $perPage));
        $bookings = iterator_to_array($paginator);

        $formattedTotalsByBookingId = [];
        foreach ($bookings as $booking) {
            if (!$booking instanceof Booking || $booking->getId() === null) {
                continue;
            }

            $convertedTotal = $currencyConverter->convert((float) $booking->getTotalPrice(), 'USD', $selectedCurrency);
            $formattedTotalsByBookingId[$booking->getId()] = $currencyConverter->formatAmount($convertedTotal, $selectedCurrency);
        }

        return $this->render('booking/index.html.twig', [
            'isAdmin' => $isAdmin,
            'bookings' => $bookings,
            'filters' => $filters,
            'properties' => $bookingRepository->getPropertiesForFilter(),
            'selectedCurrency' => $selectedCurrency,
            'supportedCurrencies' => $currencyConverter->getSupportedCurrenciesWithLabels(),
            'formattedTotalsByBookingId' => $formattedTotalsByBookingId,
            'pagination' => [
                'page' => $page,
                'perPage' => $perPage,
                'totalItems' => $totalItems,
                'totalPages' => $totalPages,
            ],
        ]);
    }

    #[Route('/admin/bookings/new', name: 'admin_booking_new', methods: ['GET', 'POST'])]
    #[Route('/bookings/new', name: 'booking_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        PropertyRepository $propertyRepository,
        CurrencyConverterService $currencyConverter,
        BookingPricingService $bookingPricingService
<<<<<<< Updated upstream
    ): Response
    {
=======
    ): Response {
>>>>>>> Stashed changes
        $isAdmin = str_starts_with((string) $request->attributes->get('_route'), 'admin_');
        $authenticated = $this->getUser();
        $currentUser = $authenticated instanceof User ? $authenticated : null;

        $booking = new Booking();
        $booking->setCreatedAt(new \DateTimeImmutable());
        $booking->setStatus(Booking::STATUS_PENDING);
        $booking->setPaymentStatus(Booking::PAYMENT_STATUS_UNPAID);
<<<<<<< Updated upstream
        $booking->setCurrency($request->query->get('currency', 'USD'));
=======
        $booking->setCurrency('USD');
>>>>>>> Stashed changes
        if (!$isAdmin && $currentUser?->getId() !== null) {
            $booking->setUserId($currentUser->getId());
        }

        $propertyId = $request->query->getInt('propertyId');
        if ($propertyId > 0) {
            $property = $propertyRepository->find($propertyId);
            if ($property !== null) {
                $booking->setProperty($property);
            }
        }

        $form = $this->createForm(BookingType::class, $booking, [
            'allow_status_change' => $isAdmin,
            'show_user_field' => $isAdmin,
            'supported_currencies' => $currencyConverter->getSupportedCurrenciesForFormChoices(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$isAdmin && $currentUser?->getId() !== null) {
                $booking->setUserId($currentUser->getId());
            }
<<<<<<< Updated upstream
=======

>>>>>>> Stashed changes
            $bookingPricingService->applyPricing($booking);
            $entityManager->persist($booking);
            $entityManager->flush();
            $this->addFlash('success', 'Booking created successfully.');

            return $this->redirectToRoute($isAdmin ? 'admin_booking_show' : 'booking_show', ['id' => $booking->getId()]);
        }

        return $this->render('booking/new.html.twig', [
            'isAdmin' => $isAdmin,
            'form' => $form,
            'booking' => $booking,
            'supportedCurrencies' => $currencyConverter->getSupportedCurrenciesWithLabels(),
        ]);
    }

    #[Route('/admin/bookings/{id}', name: 'admin_booking_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    #[Route('/bookings/{id}', name: 'booking_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(
        Request $request,
        Booking $booking,
        CurrencyConverterService $currencyConverter,
        BookingPricingService $bookingPricingService
<<<<<<< Updated upstream
    ): Response
    {
=======
    ): Response {
>>>>>>> Stashed changes
        $isAdmin = str_starts_with((string) $request->attributes->get('_route'), 'admin_');
        if (!$isAdmin) {
            $this->assertBookingOwnership($booking);
        }

        $selectedCurrency = $currencyConverter->normalizeCurrency($request->query->get('currency', $booking->getCurrency()));
        $pricingSnapshot = $booking->getPricingSnapshot() ?? $bookingPricingService->buildPricingSnapshot($booking);
        $convertedTotal = $currencyConverter->convert((float) $booking->getTotalPrice(), 'USD', $selectedCurrency);

        return $this->render('booking/show.html.twig', [
            'isAdmin' => $isAdmin,
            'booking' => $booking,
            'pricingSnapshot' => $pricingSnapshot,
            'selectedCurrency' => $selectedCurrency,
            'supportedCurrencies' => $currencyConverter->getSupportedCurrenciesWithLabels(),
            'formattedConvertedTotal' => $currencyConverter->formatAmount($convertedTotal, $selectedCurrency),
<<<<<<< Updated upstream
=======
            'qrCodeUrl' => $this->buildQrCodeUrl($booking, $selectedCurrency, $currencyConverter),
>>>>>>> Stashed changes
        ]);
    }

    #[Route('/admin/bookings/{id}/edit', name: 'admin_booking_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(
        Request $request,
        Booking $booking,
        EntityManagerInterface $entityManager,
        CurrencyConverterService $currencyConverter,
        BookingPricingService $bookingPricingService
<<<<<<< Updated upstream
    ): Response
    {
        $isAdmin = str_starts_with((string) $request->attributes->get('_route'), 'admin_');
=======
    ): Response {
>>>>>>> Stashed changes
        $form = $this->createForm(BookingType::class, $booking, [
            'allow_status_change' => true,
            'supported_currencies' => $currencyConverter->getSupportedCurrenciesForFormChoices(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $bookingPricingService->applyPricing($booking);
            $entityManager->flush();
            $this->addFlash('success', 'Booking updated successfully.');

            return $this->redirectToRoute('admin_booking_show', ['id' => $booking->getId()]);
        }

        return $this->render('booking/edit.html.twig', [
            'isAdmin' => true,
            'form' => $form,
            'booking' => $booking,
            'supportedCurrencies' => $currencyConverter->getSupportedCurrenciesWithLabels(),
        ]);
    }

<<<<<<< Updated upstream
=======
    #[Route('/bookings/pricing-preview', name: 'booking_pricing_preview', methods: ['GET'])]
    #[Route('/admin/bookings/pricing-preview', name: 'admin_booking_pricing_preview', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function pricingPreview(
        Request $request,
        PropertyRepository $propertyRepository,
        ServiceRepository $serviceRepository,
        BookingPricingService $bookingPricingService,
        CurrencyConverterService $currencyConverter
    ): JsonResponse {
        $booking = new Booking();
        $booking->setCurrency($request->query->get('currency', 'USD'));
        $booking->setDuration(max(1, $request->query->getInt('duration', 1)));

        $property = $propertyRepository->find($request->query->getInt('propertyId'));
        if ($property === null) {
            return $this->json(['error' => 'Select a property to preview pricing.'], Response::HTTP_BAD_REQUEST);
        }
        $booking->setProperty($property);

        $dateValue = trim((string) $request->query->get('bookingDate', ''));
        if ($dateValue !== '') {
            $bookingDate = \DateTimeImmutable::createFromFormat('Y-m-d', $dateValue);
            if ($bookingDate !== false) {
                $booking->setBookingDate($bookingDate);
            }
        }

        $serviceIds = array_merge($request->query->all('services'), $request->query->all('services[]'));
        foreach ($serviceIds as $serviceId) {
            $service = $serviceRepository->find((int) $serviceId);
            if ($service !== null && $service->isAvailable()) {
                $booking->addService($service);
            }
        }

        $snapshot = $bookingPricingService->buildPricingSnapshot($booking);
        $selectedCurrency = $currencyConverter->normalizeCurrency($request->query->get('currency', 'USD'));
        $conversion = $currencyConverter->getConversionData((float) $snapshot['total'], 'USD', $selectedCurrency);

        return $this->json([
            'snapshot' => $snapshot,
            'displayCurrency' => $selectedCurrency,
            'convertedTotal' => round($conversion['convertedAmount'], 2),
            'formattedConvertedTotal' => $currencyConverter->formatAmount($conversion['convertedAmount'], $selectedCurrency),
            'conversionProvider' => $conversion['provider'],
            'conversionFallback' => $conversion['fallback'],
        ]);
    }

    #[Route('/bookings/currency-convert', name: 'booking_currency_convert', methods: ['GET'])]
    #[Route('/admin/bookings/currency-convert', name: 'admin_booking_currency_convert', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function currencyConvert(Request $request, CurrencyConverterService $currencyConverter): JsonResponse
    {
        $amount = (float) $request->query->get('amount', 0);
        $from = (string) $request->query->get('from', 'USD');
        $to = (string) $request->query->get('to', 'USD');

        return $this->json($currencyConverter->getConversionData($amount, $from, $to));
    }

    #[Route('/bookings/ai-assistant', name: 'booking_ai_assistant', methods: ['POST'])]
    #[Route('/admin/bookings/ai-assistant', name: 'admin_booking_ai_assistant', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function aiAssistant(
        Request $request,
        BookingRepository $bookingRepository,
        PropertyRepository $propertyRepository,
        ServiceRepository $serviceRepository,
        BookingPricingService $bookingPricingService,
        GeminiAssistantService $geminiAssistant
    ): JsonResponse {
        if (!$this->isCsrfTokenValid('booking_ai', (string) $request->request->get('_token'))) {
            return $this->json(['error' => 'Invalid AI request token.'], Response::HTTP_FORBIDDEN);
        }

        $booking = null;
        $bookingId = $request->request->getInt('bookingId');
        if ($bookingId > 0) {
            $booking = $bookingRepository->find($bookingId);
            if (!$booking instanceof Booking) {
                return $this->json(['error' => 'Booking not found.'], Response::HTTP_NOT_FOUND);
            }

            if (!str_starts_with((string) $request->attributes->get('_route'), 'admin_')) {
                $this->assertBookingOwnership($booking);
            }
        } else {
            $booking = new Booking();
            $booking->setDuration(max(1, $request->request->getInt('duration', 1)));
            $booking->setCurrency($request->request->get('currency', 'USD'));

            $property = $propertyRepository->find($request->request->getInt('propertyId'));
            if ($property !== null) {
                $booking->setProperty($property);
            }

            $dateValue = trim((string) $request->request->get('bookingDate', ''));
            if ($dateValue !== '') {
                $bookingDate = \DateTimeImmutable::createFromFormat('Y-m-d', $dateValue);
                if ($bookingDate !== false) {
                    $booking->setBookingDate($bookingDate);
                }
            }

            $serviceIds = array_merge($request->request->all('services'), $request->request->all('services[]'));
            foreach ($serviceIds as $serviceId) {
                $service = $serviceRepository->find((int) $serviceId);
                if ($service !== null && $service->isAvailable()) {
                    $booking->addService($service);
                }
            }
        }

        $snapshot = $booking->getPricingSnapshot() ?? $bookingPricingService->buildPricingSnapshot($booking);
        $context = [
            'property' => $booking->getProperty()?->getTitle(),
            'bookingDate' => $booking->getBookingDate()?->format('Y-m-d'),
            'duration' => $booking->getDuration(),
            'currency' => $booking->getCurrency(),
            'total' => $snapshot['total'] ?? (float) $booking->getTotalPrice(),
            'seasonalLabel' => $snapshot['seasonalLabel'] ?? null,
            'timingLabel' => $snapshot['timingLabel'] ?? null,
            'services' => $snapshot['serviceLabels'] ?? [],
            'pricingNarrative' => $snapshot['narrative'] ?? null,
        ];

        return $this->json([
            'answer' => $geminiAssistant->generateBookingAssistantReply($context, (string) $request->request->get('prompt', '')),
            'provider' => $geminiAssistant->isConfigured() ? 'Gemini' : 'TravelXP fallback assistant',
        ]);
    }

>>>>>>> Stashed changes
    #[Route('/bookings/{id}/pay', name: 'booking_pay', requirements: ['id' => '\d+'], methods: ['POST'])]
    #[Route('/admin/bookings/{id}/pay', name: 'admin_booking_pay', requirements: ['id' => '\d+'], methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function pay(Request $request, Booking $booking, EntityManagerInterface $entityManager): Response
    {
        $isAdmin = str_starts_with((string) $request->attributes->get('_route'), 'admin_');
        if (!$isAdmin) {
<<<<<<< Updated upstream
            $authenticated = $this->getUser();
            $currentUser = $authenticated instanceof User ? $authenticated : null;
            if ($currentUser?->getId() === null || $booking->getUserId() !== $currentUser->getId()) {
                throw $this->createAccessDeniedException('You can only pay for your own bookings.');
            }
=======
            $this->assertBookingOwnership($booking);
>>>>>>> Stashed changes
        }

        if (!$this->isCsrfTokenValid('pay_booking_' . $booking->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('danger', 'Invalid payment request.');

            return $this->redirectToRoute($isAdmin ? 'admin_booking_show' : 'booking_show', ['id' => $booking->getId()]);
        }

        if ($booking->isCancelled()) {
            $this->addFlash('danger', 'Cancelled bookings cannot be paid.');

            return $this->redirectToRoute($isAdmin ? 'admin_booking_show' : 'booking_show', ['id' => $booking->getId()]);
        }

        if ($booking->isPaid()) {
            $this->addFlash('info', 'This booking is already paid.');

            return $this->redirectToRoute($isAdmin ? 'admin_booking_show' : 'booking_show', ['id' => $booking->getId()]);
        }

        $booking->setPaymentStatus(Booking::PAYMENT_STATUS_PAID);
<<<<<<< Updated upstream
        $booking->setPaymentReference(sprintf('PAY-%d-%s', $booking->getId(), (new \DateTimeImmutable())->format('YmdHis')));
=======
        $booking->setPaymentReference($this->generatePaymentReference($booking));
>>>>>>> Stashed changes
        if ($booking->getStatus() === Booking::STATUS_PENDING) {
            $booking->setStatus(Booking::STATUS_CONFIRMED);
        }

        $entityManager->flush();
        $this->addFlash('success', 'Payment captured successfully and the booking has been confirmed.');

        return $this->redirectToRoute($isAdmin ? 'admin_booking_show' : 'booking_show', ['id' => $booking->getId()]);
    }

    #[Route('/admin/bookings/{id}/cancel', name: 'admin_booking_cancel', requirements: ['id' => '\d+'], methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function cancel(Request $request, Booking $booking, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isCsrfTokenValid('cancel_booking_' . $booking->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('danger', 'Invalid CSRF token.');

            return $this->redirectToRoute('admin_booking_index');
        }

        $booking->setStatus(Booking::STATUS_CANCELLED);
        $entityManager->flush();
        $this->addFlash('success', 'Booking cancelled successfully.');

        return $this->redirectToRoute('admin_booking_index');
    }

    #[Route('/admin/bookings/{id}/delete', name: 'admin_booking_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Booking $booking, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isCsrfTokenValid('delete_booking_' . $booking->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('danger', 'Invalid CSRF token.');

            return $this->redirectToRoute('admin_booking_index');
        }

        $entityManager->remove($booking);
        $entityManager->flush();
        $this->addFlash('success', 'Booking deleted successfully.');

        return $this->redirectToRoute('admin_booking_index');
    }

    /**
     * @return array<string, string>
     */
    private function extractFilters(Request $request): array
    {
        return [
            'q' => (string) $request->query->get('q', ''),
            'status' => (string) $request->query->get('status', ''),
            'propertyId' => (string) $request->query->get('propertyId', ''),
            'fromDate' => (string) $request->query->get('fromDate', ''),
            'toDate' => (string) $request->query->get('toDate', ''),
            'minTotal' => (string) $request->query->get('minTotal', ''),
            'maxTotal' => (string) $request->query->get('maxTotal', ''),
            'futureOnly' => (string) $request->query->get('futureOnly', ''),
            'cancelledOnly' => (string) $request->query->get('cancelledOnly', ''),
            'sort' => (string) $request->query->get('sort', 'newest'),
        ];
    }

    private function assertBookingOwnership(Booking $booking): void
    {
        $authenticated = $this->getUser();
        $currentUser = $authenticated instanceof User ? $authenticated : null;
        if ($currentUser?->getId() === null || $booking->getUserId() !== $currentUser->getId()) {
            throw $this->createAccessDeniedException('You can only access your own bookings.');
        }
    }

    private function generatePaymentReference(Booking $booking): string
    {
        return sprintf('TXP-%d-%s', $booking->getId(), strtoupper(substr(bin2hex(random_bytes(4)), 0, 8)));
    }

    private function buildQrCodeUrl(Booking $booking, string $selectedCurrency, CurrencyConverterService $currencyConverter): string
    {
        $convertedTotal = $currencyConverter->convert((float) $booking->getTotalPrice(), 'USD', $selectedCurrency);
        $payload = implode(' | ', [
            'TravelXP Booking Payment',
            'Booking #' . $booking->getId(),
            'Property: ' . ($booking->getProperty()?->getTitle() ?? 'N/A'),
            'Amount: ' . $currencyConverter->formatAmount($convertedTotal, $selectedCurrency),
            'Status: ' . ucfirst($booking->getPaymentStatus()),
            'Ref: ' . ($booking->getPaymentReference() ?? 'pending'),
        ]);

        return 'https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=' . rawurlencode($payload);
    }
}
