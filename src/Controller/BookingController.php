<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Entity\Property;
use App\Entity\User;
use App\Form\BookingType;
use App\Repository\BookingRepository;
use App\Repository\PropertyRepository;
use App\Repository\ServiceRepository;
use App\Service\BookingPricingService;
use App\Service\CurrencyConverterService;
use App\Service\GeminiAssistantService;
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
    public function index(Request $request, BookingRepository $bookingRepository, CurrencyConverterService $currencyConverter): Response
    {
        $isAdmin = str_starts_with((string) $request->attributes->get('_route'), 'admin_');
        if ($isAdmin) {
            $this->denyAccessUnlessGranted('ROLE_ADMIN');
        }
        $filters = $this->extractFilters($request);
        $selectedCurrency = $currencyConverter->normalizeCurrency($request->query->get('currency', 'USD'));
        $currentUser = $this->getCurrentUserOrNull();

        if (!$isAdmin && $currentUser?->getId() !== null) {
            $filters['userId'] = (string) $currentUser->getId();
        }

        $page = max(1, $request->query->getInt('page', 1));
        $perPage = $isAdmin ? 10 : 9;
        $qb = $bookingRepository->createFilteredQueryBuilder($filters)
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage);

        $paginator = new Paginator($qb, true);
        $totalItems = count($paginator);
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
                'totalPages' => max(1, (int) ceil($totalItems / $perPage)),
            ],
        ]);
    }

    #[Route('/admin/bookings/new', name: 'admin_booking_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        PropertyRepository $propertyRepository,
        CurrencyConverterService $currencyConverter,
        BookingPricingService $bookingPricingService,
    ): Response {
        $booking = new Booking();
        $booking->setCreatedAt(new \DateTimeImmutable());
        $booking->setStatus(Booking::STATUS_PENDING);
        $booking->setCurrency($currencyConverter->normalizeCurrency($request->query->get('currency', 'USD')));

        $propertyId = $request->query->getInt('propertyId');
        if ($propertyId > 0) {
            $property = $propertyRepository->find($propertyId);
            if ($property !== null) {
                $booking->setProperty($property);
            }
        }

        $form = $this->createForm(BookingType::class, $booking, [
            'allow_status_change' => true,
            'show_user_field' => true,
            'active_properties_only' => false,
            'supported_currencies' => $currencyConverter->getSupportedCurrenciesForFormChoices(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $bookingPricingService->applyPricing($booking);
            $entityManager->persist($booking);
            $entityManager->flush();
            $this->addFlash('success', 'Booking created successfully.');

            return $this->redirectToRoute('admin_booking_show', ['id' => $booking->getId()]);
        }

        return $this->render('booking/new.html.twig', [
            'isAdmin' => true,
            'form' => $form,
            'booking' => $booking,
        ]);
    }

    #[Route('/properties/{id}/reserve', name: 'booking_reserve', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function reserve(
        Request $request,
        Property $property,
        EntityManagerInterface $entityManager,
        CurrencyConverterService $currencyConverter,
        BookingPricingService $bookingPricingService,
    ): Response {
        if (!$property->isActive()) {
            throw $this->createNotFoundException('This property is not available for booking.');
        }

        $currentUser = $this->getCurrentUserOrNull();
        if ($currentUser?->getId() === null) {
            throw $this->createAccessDeniedException('User access is required.');
        }

        $booking = new Booking();
        $booking->setCreatedAt(new \DateTimeImmutable());
        $booking->setStatus(Booking::STATUS_PENDING);
        $booking->setCurrency($currencyConverter->normalizeCurrency($request->query->get('currency', 'USD')));
        $booking->setProperty($property);
        $booking->setUser($currentUser);

        $form = $this->createForm(BookingType::class, $booking, [
            'allow_status_change' => false,
            'show_user_field' => false,
            'active_properties_only' => true,
            'lock_property' => true,
            'supported_currencies' => $currencyConverter->getSupportedCurrenciesForFormChoices(),
        ]);
        $form->handleRequest($request);
        $booking->setProperty($property);
        $booking->setUser($currentUser);
        $booking->setStatus(Booking::STATUS_PENDING);

        if ($form->isSubmitted() && $form->isValid()) {
            $bookingPricingService->applyPricing($booking);
            $entityManager->persist($booking);
            $entityManager->flush();
            $this->addFlash('success', 'Reservation created successfully.');

            return $this->redirectToRoute('booking_show', ['id' => $booking->getId()]);
        }

        return $this->render('booking/new.html.twig', [
            'isAdmin' => false,
            'form' => $form,
            'booking' => $booking,
        ]);
    }

    #[Route('/admin/bookings/{id}', name: 'admin_booking_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    #[Route('/bookings/{id}', name: 'booking_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(Request $request, Booking $booking, CurrencyConverterService $currencyConverter, BookingPricingService $bookingPricingService): Response
    {
        $isAdmin = str_starts_with((string) $request->attributes->get('_route'), 'admin_');
        if ($isAdmin) {
            $this->denyAccessUnlessGranted('ROLE_ADMIN');
        }
        if (!$isAdmin) {
            $this->assertBookingOwnership($booking);
        }

        $selectedCurrency = $currencyConverter->normalizeCurrency($request->query->get('currency', $booking->getCurrency()));
        $conversion = $currencyConverter->getConversionData((float) $booking->getTotalPrice(), 'USD', $selectedCurrency);
        $pricingSnapshot = $booking->getPricingSnapshot() ?? $bookingPricingService->buildPricingSnapshot($booking);

        return $this->render('booking/show.html.twig', [
            'isAdmin' => $isAdmin,
            'booking' => $booking,
            'selectedCurrency' => $selectedCurrency,
            'supportedCurrencies' => $currencyConverter->getSupportedCurrenciesWithLabels(),
            'formattedConvertedTotal' => $currencyConverter->formatAmount($conversion['convertedAmount'], $selectedCurrency),
            'conversion' => $conversion,
            'pricingSnapshot' => $pricingSnapshot,
            'qrCodeUrl' => $this->buildQrCodeUrl($booking, $selectedCurrency, $currencyConverter),
        ]);
    }

    #[Route('/admin/bookings/{id}/edit', name: 'admin_booking_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(
        Request $request,
        Booking $booking,
        EntityManagerInterface $entityManager,
        CurrencyConverterService $currencyConverter,
        BookingPricingService $bookingPricingService,
    ): Response {
        $form = $this->createForm(BookingType::class, $booking, [
            'allow_status_change' => true,
            'show_user_field' => true,
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
        ]);
    }

    #[Route('/bookings/pricing-preview', name: 'booking_pricing_preview', methods: ['GET'])]
    #[Route('/admin/bookings/pricing-preview', name: 'admin_booking_pricing_preview', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function pricingPreview(
        Request $request,
        PropertyRepository $propertyRepository,
        ServiceRepository $serviceRepository,
        BookingPricingService $bookingPricingService,
        CurrencyConverterService $currencyConverter,
    ): JsonResponse {
        $isAdmin = str_starts_with((string) $request->attributes->get('_route'), 'admin_');
        if ($isAdmin) {
            $this->denyAccessUnlessGranted('ROLE_ADMIN');
        }

        $booking = $this->buildBookingFromPreviewRequest($request, $propertyRepository, $serviceRepository);
        if ($booking->getProperty() === null) {
            return $this->json(['error' => 'Select a property to preview pricing.'], Response::HTTP_BAD_REQUEST);
        }
        if (!$isAdmin && !$booking->getProperty()->isActive()) {
            return $this->json(['error' => 'This property is not available for booking.'], Response::HTTP_BAD_REQUEST);
        }

        $snapshot = $bookingPricingService->buildPricingSnapshot($booking);
        $selectedCurrency = $currencyConverter->normalizeCurrency($request->query->get('currency', 'USD'));
        $conversion = $currencyConverter->getConversionData((float) $snapshot['total'], 'USD', $selectedCurrency);

        return $this->json([
            'snapshot' => $snapshot,
            'conversion' => $conversion,
            'formattedConvertedTotal' => $currencyConverter->formatAmount($conversion['convertedAmount'], $selectedCurrency),
        ]);
    }

    #[Route('/bookings/currency-convert', name: 'booking_currency_convert', methods: ['GET'])]
    #[Route('/admin/bookings/currency-convert', name: 'admin_booking_currency_convert', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function currencyConvert(Request $request, CurrencyConverterService $currencyConverter): JsonResponse
    {
        if (str_starts_with((string) $request->attributes->get('_route'), 'admin_')) {
            $this->denyAccessUnlessGranted('ROLE_ADMIN');
        }

        $amount = (float) $request->query->get('amount', 0);
        $from = (string) $request->query->get('from', 'USD');
        $to = (string) $request->query->get('to', 'USD');
        $conversion = $currencyConverter->getConversionData($amount, $from, $to);

        return $this->json([
            'conversion' => $conversion,
            'formatted' => $currencyConverter->formatAmount($conversion['convertedAmount'], $conversion['to']),
        ]);
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
        CurrencyConverterService $currencyConverter,
        GeminiAssistantService $geminiAssistant,
    ): JsonResponse {
        $isAdmin = str_starts_with((string) $request->attributes->get('_route'), 'admin_');
        if ($isAdmin) {
            $this->denyAccessUnlessGranted('ROLE_ADMIN');
        }

        if (!$this->isCsrfTokenValid('booking_ai', (string) $request->request->get('_token'))) {
            return $this->json(['error' => 'Invalid CSRF token.'], Response::HTTP_FORBIDDEN);
        }

        $booking = null;
        $bookingId = $request->request->getInt('bookingId');
        if ($bookingId > 0) {
            $booking = $bookingRepository->find($bookingId);
            if (!$booking instanceof Booking) {
                return $this->json(['error' => 'Booking not found.'], Response::HTTP_NOT_FOUND);
            }

            if (!$isAdmin) {
                $this->assertBookingOwnership($booking);
            }
        } else {
            $booking = $this->buildBookingFromPreviewRequest($request, $propertyRepository, $serviceRepository);
        }

        if ($booking->getProperty() === null) {
            return $this->json(['error' => 'Select a property before asking for booking guidance.'], Response::HTTP_BAD_REQUEST);
        }
        if (!$isAdmin && !$booking->getProperty()->isActive()) {
            return $this->json(['error' => 'This property is not available for booking.'], Response::HTTP_BAD_REQUEST);
        }

        $snapshot = $booking->getPricingSnapshot() ?? $bookingPricingService->buildPricingSnapshot($booking);
        $selectedCurrency = $currencyConverter->normalizeCurrency($request->request->get('currency', $booking->getCurrency()));
        $conversion = $currencyConverter->getConversionData((float) $snapshot['total'], 'USD', $selectedCurrency);
        $context = $this->buildAssistantContext($booking, $snapshot, $conversion);
        $answer = $geminiAssistant->generateBookingAssistantReply($context, (string) $request->request->get('prompt', ''));

        return $this->json([
            'answer' => $answer,
            'provider' => $geminiAssistant->isConfigured() ? 'Gemini' : 'TravelXP assistant',
        ]);
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

    #[Route('/bookings/{id}/cancel', name: 'booking_cancel', requirements: ['id' => '\d+'], methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function cancelOwn(Request $request, Booking $booking, EntityManagerInterface $entityManager): Response
    {
        $this->assertBookingOwnership($booking);

        if (!$this->isCsrfTokenValid('cancel_booking_' . $booking->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('danger', 'Invalid CSRF token.');

            return $this->redirectToRoute('booking_show', ['id' => $booking->getId()]);
        }

        if ($booking->isCancelled()) {
            $this->addFlash('info', 'This booking is already cancelled.');

            return $this->redirectToRoute('booking_show', ['id' => $booking->getId()]);
        }

        if ($booking->isInPast()) {
            $this->addFlash('warning', 'Past bookings cannot be cancelled from the user side.');

            return $this->redirectToRoute('booking_show', ['id' => $booking->getId()]);
        }

        $booking->setStatus(Booking::STATUS_CANCELLED);
        $entityManager->flush();
        $this->addFlash('success', 'Your booking was cancelled.');

        return $this->redirectToRoute('booking_show', ['id' => $booking->getId()]);
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

    private function buildBookingFromPreviewRequest(Request $request, PropertyRepository $propertyRepository, ServiceRepository $serviceRepository): Booking
    {
        $booking = new Booking();
        $booking->setCurrency($request->get('currency', 'USD'));
        $booking->setDuration(max(1, (int) $request->get('duration', 1)));

        $property = $propertyRepository->find((int) $request->get('propertyId', 0));
        if ($property !== null) {
            $booking->setProperty($property);
        }

        $dateValue = trim((string) $request->get('bookingDate', ''));
        if ($dateValue !== '') {
            $bookingDate = \DateTimeImmutable::createFromFormat('Y-m-d', $dateValue);
            if ($bookingDate instanceof \DateTimeImmutable) {
                $booking->setBookingDate($bookingDate);
            }
        }

        $serviceIds = array_merge((array) $request->get('services', []), (array) $request->get('services[]', []));
        foreach ($serviceIds as $serviceId) {
            $service = $serviceRepository->find((int) $serviceId);
            if ($service !== null && $service->isAvailable()) {
                $booking->addService($service);
            }
        }

        return $booking;
    }

    /**
     * @param array<string, mixed> $snapshot
     * @param array<string, mixed> $conversion
     *
     * @return array<string, mixed>
     */
    private function buildAssistantContext(Booking $booking, array $snapshot, array $conversion): array
    {
        $property = $booking->getProperty();

        return [
            'property' => $property?->getTitle() ?? 'Selected property',
            'city' => $property?->getCity(),
            'country' => $property?->getCountry(),
            'bookingDate' => $booking->getBookingDate()?->format('Y-m-d'),
            'duration' => $snapshot['duration'] ?? $booking->getDuration(),
            'total' => $snapshot['total'] ?? $booking->getTotalPrice(),
            'convertedTotal' => $conversion['convertedAmount'] ?? null,
            'currency' => $conversion['to'] ?? $booking->getCurrency(),
            'seasonalLabel' => $snapshot['seasonalLabel'] ?? null,
            'timingLabel' => $snapshot['timingLabel'] ?? null,
            'offerDiscountPercent' => $snapshot['offerDiscountPercent'] ?? 0,
            'services' => $snapshot['serviceLabels'] ?? [],
        ];
    }

    private function assertBookingOwnership(Booking $booking): void
    {
        $currentUser = $this->getCurrentUserOrNull();
        if ($currentUser === null || $booking->getUser()?->getId() !== $currentUser->getId()) {
            throw $this->createAccessDeniedException('You can only view your own bookings.');
        }
    }

    private function getCurrentUserOrNull(): ?User
    {
        $authenticated = $this->getUser();

        return $authenticated instanceof User ? $authenticated : null;
    }

    private function buildQrCodeUrl(Booking $booking, string $currency, CurrencyConverterService $currencyConverter): string
    {
        $convertedTotal = $currencyConverter->convert((float) $booking->getTotalPrice(), 'USD', $currency);
        $payload = sprintf(
            'TravelXP booking #%s | %s | %s',
            $booking->getId() ?? 'new',
            $currencyConverter->formatAmount($convertedTotal, $currency),
            $booking->getProperty()?->getTitle() ?? 'Property booking',
        );

        return 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=' . rawurlencode($payload);
    }
}
