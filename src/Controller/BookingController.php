<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Entity\User;
use App\Form\BookingType;
use App\Repository\BookingRepository;
use App\Repository\PropertyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class BookingController extends AbstractController
{
    #[Route('/admin/bookings', name: 'admin_booking_index', methods: ['GET'])]
    #[Route('/bookings', name: 'booking_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(Request $request, BookingRepository $bookingRepository): Response
    {
        $isAdmin = str_starts_with((string) $request->attributes->get('_route'), 'admin_');
        $filters = $this->extractFilters($request);
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

        return $this->render('booking/index.html.twig', [
            'isAdmin' => $isAdmin,
            'bookings' => iterator_to_array($paginator),
            'filters' => $filters,
            'properties' => $bookingRepository->getPropertiesForFilter(),
            'pagination' => [
                'page' => $page,
                'perPage' => $perPage,
                'totalItems' => $totalItems,
                'totalPages' => $totalPages,
            ],
        ]);
    }

    #[Route('/admin/bookings/new', name: 'admin_booking_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function new(Request $request, EntityManagerInterface $entityManager, PropertyRepository $propertyRepository): Response
    {
        $isAdmin = str_starts_with((string) $request->attributes->get('_route'), 'admin_');
        $authenticated = $this->getUser();
        $currentUser = $authenticated instanceof User ? $authenticated : null;

        $booking = new Booking();
        $booking->setCreatedAt(new \DateTimeImmutable());
        $booking->setStatus(Booking::STATUS_PENDING);
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
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$isAdmin && $currentUser?->getId() !== null) {
                $booking->setUserId($currentUser->getId());
            }
            $booking->setTotalPrice($this->calculateTotalPrice($booking));
            $entityManager->persist($booking);
            $entityManager->flush();
            $this->addFlash('success', 'Booking created successfully.');

            return $this->redirectToRoute($isAdmin ? 'admin_booking_index' : 'booking_index');
        }

        return $this->render('booking/new.html.twig', [
            'isAdmin' => $isAdmin,
            'form' => $form,
            'booking' => $booking,
        ]);
    }

    #[Route('/admin/bookings/{id}', name: 'admin_booking_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    #[Route('/bookings/{id}', name: 'booking_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(Request $request, Booking $booking): Response
    {
        $isAdmin = str_starts_with((string) $request->attributes->get('_route'), 'admin_');
        if (!$isAdmin) {
            $authenticated = $this->getUser();
            $currentUser = $authenticated instanceof User ? $authenticated : null;
            if ($currentUser?->getId() === null || $booking->getUserId() !== $currentUser->getId()) {
                throw $this->createAccessDeniedException('You can only view your own bookings.');
            }
        }

        return $this->render('booking/show.html.twig', [
            'isAdmin' => $isAdmin,
            'booking' => $booking,
        ]);
    }

    #[Route('/admin/bookings/{id}/edit', name: 'admin_booking_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, Booking $booking, EntityManagerInterface $entityManager): Response
    {
        $isAdmin = str_starts_with((string) $request->attributes->get('_route'), 'admin_');
        $form = $this->createForm(BookingType::class, $booking, [
            'allow_status_change' => true,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $booking->setTotalPrice($this->calculateTotalPrice($booking));
            $entityManager->flush();
            $this->addFlash('success', 'Booking updated successfully.');

            return $this->redirectToRoute('admin_booking_index');
        }

        return $this->render('booking/edit.html.twig', [
            'isAdmin' => $isAdmin,
            'form' => $form,
            'booking' => $booking,
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

    private function calculateTotalPrice(Booking $booking): string
    {
        $property = $booking->getProperty();
        $duration = max(1, (int) ($booking->getDuration() ?? 1));
        $nightlyRate = $property === null ? 0.0 : (float) $property->getPricePerNight();

        $activeDiscountPercent = $this->resolveActiveDiscountPercent($booking);
        $discountedNightlyRate = $nightlyRate * (1 - ($activeDiscountPercent / 100));

        $serviceTotal = 0.0;
        foreach ($booking->getServices() as $service) {
            $serviceTotal += (float) $service->getPrice();
        }

        $total = ($discountedNightlyRate * $duration) + $serviceTotal;

        return number_format(max(0, $total), 2, '.', '');
    }

    private function resolveActiveDiscountPercent(Booking $booking): float
    {
        $property = $booking->getProperty();
        $bookingDate = $booking->getBookingDate();
        if ($property === null || $bookingDate === null) {
            return 0.0;
        }

        $bestDiscount = 0.0;
        foreach ($property->getOffers() as $offer) {
            if (!$offer->isActive() || $offer->getStartDate() === null || $offer->getEndDate() === null) {
                continue;
            }

            if ($bookingDate >= $offer->getStartDate() && $bookingDate <= $offer->getEndDate()) {
                $bestDiscount = max($bestDiscount, (float) $offer->getDiscountPercentage());
            }
        }

        return min(100.0, max(0.0, $bestDiscount));
    }

}
