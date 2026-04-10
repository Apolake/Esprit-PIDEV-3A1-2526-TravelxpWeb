<?php

namespace App\Controller;

use App\Entity\Trip;
use App\Entity\User;
use App\Form\TripType;
use App\Repository\TripRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class TripController extends AbstractController
{
    #[Route('/trips', name: 'trip_index', methods: ['GET'])]
    #[Route('/admin/trips', name: 'admin_trip_index', methods: ['GET'])]
    public function index(Request $request, TripRepository $tripRepository): Response
    {
        $isAdmin = str_starts_with((string) $request->attributes->get('_route'), 'admin_');
        $currentUser = $this->getUser();
        $viewer = $currentUser instanceof User ? $currentUser : null;

        $filters = [
            'q' => (string) $request->query->get('q', ''),
            'status' => (string) $request->query->get('status', ''),
            'destination' => (string) $request->query->get('destination', ''),
            'myTrips' => (string) $request->query->get('myTrips', ''),
            'sort' => (string) $request->query->get('sort', 'newest'),
        ];

        $page = max(1, $request->query->getInt('page', 1));
        $perPage = $isAdmin ? 10 : 9;
        $qb = $tripRepository->createFilteredQueryBuilder($filters, $isAdmin, $viewer);
        $qb->setFirstResult(($page - 1) * $perPage)->setMaxResults($perPage);

        $paginator = new Paginator($qb, true);
        $totalItems = count($paginator);
        $totalPages = max(1, (int) ceil($totalItems / $perPage));

        return $this->render('trip/index.html.twig', [
            'isAdmin' => $isAdmin,
            'trips' => iterator_to_array($paginator),
            'filters' => $filters,
            'joinedTripIds' => $viewer ? $tripRepository->findJoinedTripIdsForUser($viewer) : [],
            'destinations' => $tripRepository->getDistinctDestinations(),
            'pagination' => [
                'page' => $page,
                'perPage' => $perPage,
                'totalItems' => $totalItems,
                'totalPages' => $totalPages,
            ],
        ]);
    }

    #[Route('/admin/trips/new', name: 'admin_trip_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $trip = new Trip();
        $form = $this->createForm(TripType::class, $trip);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($trip);
            $entityManager->flush();
            $this->addFlash('success', 'Trip created successfully.');

            return $this->redirectToRoute('admin_trip_index');
        }

        return $this->render('trip/new.html.twig', [
            'isAdmin' => true,
            'trip' => $trip,
            'form' => $form,
        ]);
    }

    #[Route('/trips/{id}', name: 'trip_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    #[Route('/admin/trips/{id}', name: 'admin_trip_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(Request $request, Trip $trip): Response
    {
        $isAdmin = str_starts_with((string) $request->attributes->get('_route'), 'admin_');
        $viewer = $this->getUser();
        $currentUser = $viewer instanceof User ? $viewer : null;

        return $this->render('trip/show.html.twig', [
            'isAdmin' => $isAdmin,
            'trip' => $trip,
            'isJoined' => $currentUser ? $trip->isParticipant($currentUser) : false,
        ]);
    }

    #[Route('/admin/trips/{id}/edit', name: 'admin_trip_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, Trip $trip, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TripType::class, $trip);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Trip updated successfully.');

            return $this->redirectToRoute('admin_trip_index');
        }

        return $this->render('trip/edit.html.twig', [
            'isAdmin' => true,
            'trip' => $trip,
            'form' => $form,
        ]);
    }

    #[Route('/admin/trips/{id}/delete', name: 'admin_trip_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Trip $trip, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isCsrfTokenValid('delete_trip_' . $trip->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('danger', 'Invalid CSRF token.');

            return $this->redirectToRoute('admin_trip_index');
        }

        $entityManager->remove($trip);
        $entityManager->flush();
        $this->addFlash('success', 'Trip deleted successfully.');

        return $this->redirectToRoute('admin_trip_index');
    }

    #[Route('/trips/{id}/join', name: 'trip_join', requirements: ['id' => '\d+'], methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function join(Request $request, Trip $trip, EntityManagerInterface $entityManager): Response
    {
        $currentUser = $this->getUser();
        if (!$currentUser instanceof User) {
            throw $this->createAccessDeniedException('Authentication required.');
        }

        if (!$this->isCsrfTokenValid('join_trip_' . $trip->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('danger', 'Invalid CSRF token.');

            return $this->redirectToRoute('trip_index');
        }

        if (!$trip->isParticipant($currentUser)) {
            $trip->addParticipant($currentUser);
            $entityManager->flush();
            $this->addFlash('success', 'You joined this trip.');
        } else {
            $this->addFlash('info', 'You are already participating in this trip.');
        }

        return $this->redirectToRoute('trip_index');
    }

    #[Route('/trips/{id}/leave', name: 'trip_leave', requirements: ['id' => '\d+'], methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function leave(Request $request, Trip $trip, EntityManagerInterface $entityManager): Response
    {
        $currentUser = $this->getUser();
        if (!$currentUser instanceof User) {
            throw $this->createAccessDeniedException('Authentication required.');
        }

        if (!$this->isCsrfTokenValid('leave_trip_' . $trip->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('danger', 'Invalid CSRF token.');

            return $this->redirectToRoute('trip_index');
        }

        if ($trip->isParticipant($currentUser)) {
            foreach ($trip->getActivities() as $activity) {
                if ($activity->isParticipant($currentUser)) {
                    $activity->removeParticipant($currentUser);
                }
            }
            $trip->removeParticipant($currentUser);
            $entityManager->flush();
            $this->addFlash('success', 'You left this trip.');
        } else {
            $this->addFlash('info', 'You are not participating in this trip.');
        }

        return $this->redirectToRoute('trip_index');
    }
}
