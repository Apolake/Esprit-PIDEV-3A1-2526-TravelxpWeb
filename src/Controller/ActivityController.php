<?php

namespace App\Controller;

use App\Entity\Activity;
use App\Entity\User;
use App\Form\ActivityType;
use App\Repository\ActivityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ActivityController extends AbstractController
{
    #[Route('/activities', name: 'activity_index', methods: ['GET'])]
    #[Route('/admin/activities', name: 'admin_activity_index', methods: ['GET'])]
    public function index(Request $request, ActivityRepository $activityRepository): Response
    {
        $isAdmin = str_starts_with((string) $request->attributes->get('_route'), 'admin_');
        $viewer = $this->getUser();
        $currentUser = $viewer instanceof User ? $viewer : null;

        $filters = [
            'q' => (string) $request->query->get('q', ''),
            'status' => (string) $request->query->get('status', ''),
            'type' => (string) $request->query->get('type', ''),
            'tripId' => (string) $request->query->get('tripId', ''),
            'myActivities' => (string) $request->query->get('myActivities', ''),
            'sort' => (string) $request->query->get('sort', 'newest'),
        ];

        $page = max(1, $request->query->getInt('page', 1));
        $perPage = $isAdmin ? 10 : 9;
        $qb = $activityRepository->createFilteredQueryBuilder($filters, $isAdmin, $currentUser);
        $qb->setFirstResult(($page - 1) * $perPage)->setMaxResults($perPage);

        $paginator = new Paginator($qb, true);
        $totalItems = count($paginator);
        $totalPages = max(1, (int) ceil($totalItems / $perPage));

        return $this->render('activity/index.html.twig', [
            'isAdmin' => $isAdmin,
            'activities' => iterator_to_array($paginator),
            'filters' => $filters,
            'types' => $activityRepository->getDistinctTypes(),
            'trips' => $activityRepository->getTripsForFilter(),
            'joinedActivityIds' => $currentUser ? $activityRepository->findJoinedActivityIdsForUser($currentUser) : [],
            'pagination' => [
                'page' => $page,
                'perPage' => $perPage,
                'totalItems' => $totalItems,
                'totalPages' => $totalPages,
            ],
        ]);
    }

    #[Route('/admin/activities/new', name: 'admin_activity_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $activity = new Activity();
        $form = $this->createForm(ActivityType::class, $activity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($activity);
            $entityManager->flush();
            $this->addFlash('success', 'Activity created successfully.');

            return $this->redirectToRoute('admin_activity_index');
        }

        return $this->render('activity/new.html.twig', [
            'isAdmin' => true,
            'activity' => $activity,
            'form' => $form,
        ]);
    }

    #[Route('/activities/{id}', name: 'activity_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    #[Route('/admin/activities/{id}', name: 'admin_activity_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(Request $request, Activity $activity): Response
    {
        $isAdmin = str_starts_with((string) $request->attributes->get('_route'), 'admin_');
        $viewer = $this->getUser();
        $currentUser = $viewer instanceof User ? $viewer : null;

        return $this->render('activity/show.html.twig', [
            'isAdmin' => $isAdmin,
            'activity' => $activity,
            'isJoined' => $currentUser ? $activity->isParticipant($currentUser) : false,
            'canJoinTrip' => $currentUser && $activity->getTrip() ? $activity->getTrip()->isParticipant($currentUser) : true,
        ]);
    }

    #[Route('/admin/activities/{id}/edit', name: 'admin_activity_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, Activity $activity, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ActivityType::class, $activity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Activity updated successfully.');

            return $this->redirectToRoute('admin_activity_index');
        }

        return $this->render('activity/edit.html.twig', [
            'isAdmin' => true,
            'activity' => $activity,
            'form' => $form,
        ]);
    }

    #[Route('/admin/activities/{id}/delete', name: 'admin_activity_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Activity $activity, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isCsrfTokenValid('delete_activity_' . $activity->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('danger', 'Invalid CSRF token.');

            return $this->redirectToRoute('admin_activity_index');
        }

        $entityManager->remove($activity);
        $entityManager->flush();
        $this->addFlash('success', 'Activity deleted successfully.');

        return $this->redirectToRoute('admin_activity_index');
    }

    #[Route('/activities/{id}/join', name: 'activity_join', requirements: ['id' => '\d+'], methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function join(Request $request, Activity $activity, EntityManagerInterface $entityManager): Response
    {
        $currentUser = $this->getUser();
        if (!$currentUser instanceof User) {
            throw $this->createAccessDeniedException('Authentication required.');
        }

        if (!$this->isCsrfTokenValid('join_activity_' . $activity->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('danger', 'Invalid CSRF token.');

            return $this->redirectToRoute('activity_index');
        }

        if ($activity->getTrip() !== null && !$activity->getTrip()->isParticipant($currentUser)) {
            $this->addFlash('warning', 'Join the related trip first.');

            return $this->redirectToRoute('activity_index');
        }

        if (!$activity->isParticipant($currentUser)) {
            $activity->addParticipant($currentUser);
            $entityManager->flush();
            $this->addFlash('success', 'You joined this activity.');
        } else {
            $this->addFlash('info', 'You are already participating in this activity.');
        }

        return $this->redirectToRoute('activity_index');
    }

    #[Route('/activities/{id}/leave', name: 'activity_leave', requirements: ['id' => '\d+'], methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function leave(Request $request, Activity $activity, EntityManagerInterface $entityManager): Response
    {
        $currentUser = $this->getUser();
        if (!$currentUser instanceof User) {
            throw $this->createAccessDeniedException('Authentication required.');
        }

        if (!$this->isCsrfTokenValid('leave_activity_' . $activity->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('danger', 'Invalid CSRF token.');

            return $this->redirectToRoute('activity_index');
        }

        if ($activity->isParticipant($currentUser)) {
            $activity->removeParticipant($currentUser);
            $entityManager->flush();
            $this->addFlash('success', 'You left this activity.');
        } else {
            $this->addFlash('info', 'You are not participating in this activity.');
        }

        return $this->redirectToRoute('activity_index');
    }
}
//commited changes on this file to complete fix integration logic error 