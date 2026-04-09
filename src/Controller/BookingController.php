<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Form\BookingType;
use App\Repository\BookingRepository;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookingController extends AbstractController
{
    #[Route('/admin/bookings', name: 'admin_booking_index', methods: ['GET'])]
    public function adminIndex(Request $request, BookingRepository $bookingRepository, PaginatorInterface $paginator): Response
    {
        $filters = $this->extractFilters($request);
        $qb = $bookingRepository->createFilteredQueryBuilder($filters);
        $pagination = $paginator->paginate($qb, $request->query->getInt('page', 1), 10);

        return $this->render('booking/admin/index.html.twig', [
            'pagination' => $pagination,
            'filters' => $filters,
            'properties' => $bookingRepository->getPropertiesForFilter(),
        ]);
    }

    #[Route('/admin/bookings/new', name: 'admin_booking_new', methods: ['GET', 'POST'])]
    public function adminNew(Request $request, EntityManagerInterface $entityManager): Response
    {
        $booking = new Booking();
        $booking->setCreatedAt(new \DateTime());
        $booking->setStatus(Booking::STATUS_PENDING);

        $form = $this->createForm(BookingType::class, $booking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($booking);
            $entityManager->flush();
            $this->addFlash('success', 'Booking created successfully.');

            return $this->redirectToRoute('admin_booking_index');
        }

        return $this->render('booking/admin/new.html.twig', [
            'form' => $form,
            'booking' => $booking,
        ]);
    }

    #[Route('/admin/bookings/{id}', name: 'admin_booking_show', requirements: ['id' => '\\d+'], methods: ['GET'])]
    public function adminShow(Booking $booking): Response
    {
        return $this->render('booking/admin/show.html.twig', [
            'booking' => $booking,
            'canEdit' => $this->canEdit($booking),
            'canCancel' => $this->canCancel($booking),
            'canDelete' => $this->canDelete($booking),
        ]);
    }

    #[Route('/admin/bookings/{id}/edit', name: 'admin_booking_edit', requirements: ['id' => '\\d+'], methods: ['GET', 'POST'])]
    public function adminEdit(Request $request, Booking $booking, EntityManagerInterface $entityManager): Response
    {
        if (!$this->canEdit($booking)) {
            $this->addFlash('warning', 'This booking cannot be edited.');

            return $this->redirectToRoute('admin_booking_show', ['id' => $booking->getId()]);
        }

        $form = $this->createForm(BookingType::class, $booking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Booking updated successfully.');

            return $this->redirectToRoute('admin_booking_index');
        }

        return $this->render('booking/admin/edit.html.twig', [
            'form' => $form,
            'booking' => $booking,
        ]);
    }

    #[Route('/admin/bookings/{id}/cancel', name: 'admin_booking_cancel', requirements: ['id' => '\\d+'], methods: ['POST'])]
    public function adminCancel(Request $request, Booking $booking, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isCsrfTokenValid('cancel_booking_' . $booking->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('danger', 'Invalid CSRF token.');

            return $this->redirectToRoute('admin_booking_index');
        }

        if (!$this->canCancel($booking)) {
            $this->addFlash('warning', 'This booking cannot be cancelled.');

            return $this->redirectToRoute('admin_booking_index');
        }

        // Cancelled bookings are not deleted to keep historical records.
        $booking->setStatus(Booking::STATUS_CANCELLED);
        $entityManager->flush();

        $this->addFlash('success', 'Booking cancelled successfully.');

        return $this->redirectToRoute('admin_booking_index');
    }

    #[Route('/admin/bookings/{id}/delete', name: 'admin_booking_delete', requirements: ['id' => '\\d+'], methods: ['POST'])]
    public function adminDelete(Request $request, Booking $booking, EntityManagerInterface $entityManager): Response
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

    #[Route('/bookings', name: 'booking_index', methods: ['GET'])]
    public function index(Request $request, BookingRepository $bookingRepository, PaginatorInterface $paginator): Response
    {
        $filters = $this->extractFilters($request);
        $qb = $bookingRepository->createFilteredQueryBuilder($filters);
        $pagination = $paginator->paginate($qb, $request->query->getInt('page', 1), 9);

        return $this->render('booking/frontend/index.html.twig', [
            'pagination' => $pagination,
            'filters' => $filters,
            'properties' => $bookingRepository->getPropertiesForFilter(),
        ]);
    }

    #[Route('/bookings/new', name: 'booking_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $booking = new Booking();
        $booking->setCreatedAt(new \DateTime());
        $booking->setStatus(Booking::STATUS_PENDING);

        $form = $this->createForm(BookingType::class, $booking, [
            'allow_status_change' => false,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($booking);
            $entityManager->flush();
            $this->addFlash('success', 'Booking created successfully.');

            return $this->redirectToRoute('booking_index');
        }

        return $this->render('booking/frontend/new.html.twig', [
            'form' => $form,
            'booking' => $booking,
        ]);
    }

    #[Route('/bookings/{id}', name: 'booking_show', requirements: ['id' => '\\d+'], methods: ['GET'])]
    public function show(Booking $booking): Response
    {
        return $this->render('booking/frontend/show.html.twig', [
            'booking' => $booking,
            'canEdit' => $this->canEdit($booking),
            'canCancel' => $this->canCancel($booking),
            'canDelete' => $this->canDelete($booking),
        ]);
    }

    #[Route('/bookings/{id}/edit', name: 'booking_edit', requirements: ['id' => '\\d+'], methods: ['GET', 'POST'])]
    public function edit(Request $request, Booking $booking, EntityManagerInterface $entityManager): Response
    {
        if (!$this->canEdit($booking)) {
            $this->addFlash('warning', 'This booking cannot be edited.');

            return $this->redirectToRoute('booking_show', ['id' => $booking->getId()]);
        }

        $form = $this->createForm(BookingType::class, $booking, [
            'allow_status_change' => false,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Booking updated successfully.');

            return $this->redirectToRoute('booking_show', ['id' => $booking->getId()]);
        }

        return $this->render('booking/frontend/edit.html.twig', [
            'form' => $form,
            'booking' => $booking,
        ]);
    }

    #[Route('/bookings/{id}/cancel', name: 'booking_cancel', requirements: ['id' => '\\d+'], methods: ['POST'])]
    public function cancel(Request $request, Booking $booking, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isCsrfTokenValid('cancel_booking_' . $booking->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('danger', 'Invalid CSRF token.');

            return $this->redirectToRoute('booking_index');
        }

        if (!$this->canCancel($booking)) {
            $this->addFlash('warning', 'This booking cannot be cancelled.');

            return $this->redirectToRoute('booking_show', ['id' => $booking->getId()]);
        }

        // Cancelled bookings are not deleted to preserve booking history.
        $booking->setStatus(Booking::STATUS_CANCELLED);
        $entityManager->flush();

        $this->addFlash('success', 'Booking cancelled successfully.');

        return $this->redirectToRoute('booking_index');
    }

    #[Route('/bookings/{id}/delete', name: 'booking_delete', requirements: ['id' => '\\d+'], methods: ['POST'])]
    public function delete(Request $request, Booking $booking, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isCsrfTokenValid('delete_booking_' . $booking->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('danger', 'Invalid CSRF token.');

            return $this->redirectToRoute('booking_index');
        }

        $entityManager->remove($booking);
        $entityManager->flush();

        $this->addFlash('success', 'Booking deleted successfully.');

        return $this->redirectToRoute('booking_index');
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

    private function canEdit(Booking $booking): bool
    {
        return !$booking->isCancelled() && !$booking->isInPast();
    }

    private function canCancel(Booking $booking): bool
    {
        return !$booking->isCancelled() && !$booking->isInPast();
    }

    private function canDelete(Booking $booking): bool
    {
        return true;
    }
}
