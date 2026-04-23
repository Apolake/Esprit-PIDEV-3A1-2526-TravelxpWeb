<?php

namespace App\Controller;

use App\Entity\Activity;
use App\Entity\User;
use App\Form\ActivityType;
use App\Repository\ActivityWaitingListEntryRepository;
use App\Repository\ActivityRepository;
use App\Repository\TripRepository;
use App\Service\ActivityLocationService;
use App\Service\ActivityParticipationService;
use App\Service\CurrencyConverterService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Form\FormError;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ActivityController extends AbstractController
{
    #[Route('/trips/my-activities', name: 'trip_my_activities', methods: ['GET'])]
    #[Route('/activities', name: 'activity_index', methods: ['GET'])]
    #[Route('/admin/activities', name: 'admin_activity_index', methods: ['GET'])]
    public function index(
        Request $request,
        ActivityRepository $activityRepository,
        TripRepository $tripRepository,
        ActivityWaitingListEntryRepository $activityWaitingListEntryRepository,
        EntityManagerInterface $entityManager,
        PaginatorInterface $paginator,
    ): Response
    {
        $routeName = (string) $request->attributes->get('_route');
        $isAdmin = str_starts_with($routeName, 'admin_');
        $isMyActivitiesPage = $routeName === 'trip_my_activities';
        $viewer = $this->getUser();
        $currentUser = $viewer instanceof User ? $viewer : null;

        $filters = [
            'q' => (string) $request->query->get('q', ''),
            'status' => (string) $request->query->get('status', ''),
            'type' => (string) $request->query->get('type', ''),
            'tripId' => (string) $request->query->get('tripId', ''),
            'myActivities' => $isMyActivitiesPage ? '1' : (string) $request->query->get('myActivities', ''),
            'sort' => (string) $request->query->get('sort', 'newest'),
        ];
        $view = (string) $request->query->get('view', $isAdmin ? 'table' : 'cards');
        if (!in_array($view, ['cards', 'table'], true)) {
            $view = $isAdmin ? 'table' : 'cards';
        }

        $page = max(1, $request->query->getInt('page', 1));
        $perPage = $isAdmin ? 10 : 9;
        $qb = $activityRepository->createFilteredQueryBuilder($filters, $isAdmin, $currentUser);
        $pagination = $paginator->paginate($qb, $page, $perPage, [
            'distinct' => true,
        ]);
        $activityItems = $pagination->getItems();
        if ($activityItems instanceof \Traversable) {
            $activityItems = iterator_to_array($activityItems);
        }
        if (!is_array($activityItems)) {
            $activityItems = [];
        }

        $totalItems = (int) $pagination->getTotalItemCount();
        $totalPages = max(1, (int) ceil($totalItems / $perPage));
        $adminStats = null;

        if ($isAdmin) {
            $totalActivities = (int) $activityRepository->count([]);
            $ongoingActivities = (int) $activityRepository->createQueryBuilder('a')
                ->select('COUNT(a.id)')
                ->andWhere('a.status = :status')
                ->setParameter('status', 'ONGOING')
                ->getQuery()
                ->getSingleScalarResult();
            $completedActivities = (int) $activityRepository->createQueryBuilder('a')
                ->select('COUNT(a.id)')
                ->andWhere('a.status IN (:statuses)')
                ->setParameter('statuses', ['COMPLETED', 'DONE'])
                ->getQuery()
                ->getSingleScalarResult();
            $joinedActivities = (int) $entityManager->createQuery(
                'SELECT COUNT(p.id) FROM App\Entity\Activity a JOIN a.participants p'
            )->getSingleScalarResult();

            $adminStats = [
                'totalActivities' => $totalActivities,
                'joinedActivities' => $joinedActivities,
                'ongoingActivities' => $ongoingActivities,
                'completedActivities' => $completedActivities,
            ];
        }

        return $this->render('activity/index.html.twig', [
            'isAdmin' => $isAdmin,
            'activities' => $activityItems,
            'filters' => $filters,
            'types' => $activityRepository->getDistinctTypes(),
            'trips' => $activityRepository->getTripsForFilter(),
            'joinedActivityIds' => $currentUser ? $activityRepository->findJoinedActivityIdsForUser($currentUser) : [],
            'joinedTripIds' => $currentUser ? $tripRepository->findJoinedTripIdsForUser($currentUser) : [],
            'waitingActivityIds' => $currentUser ? $activityWaitingListEntryRepository->findActiveWaitingActivityIdsForUser($currentUser) : [],
            'routeName' => $routeName,
            'view' => $view,
            'userTripArea' => !$isAdmin,
            'tripAreaSection' => 'activities',
            'isMyActivitiesPage' => $isMyActivitiesPage,
            'pagination' => [
                'page' => (int) $pagination->getCurrentPageNumber(),
                'perPage' => $perPage,
                'totalItems' => $totalItems,
                'totalPages' => $totalPages,
            ],
            'adminStats' => $adminStats,
        ]);
    }

    #[Route('/trips/calendar', name: 'trip_activity_calendar', methods: ['GET'])]
    #[Route('/admin/activities/calendar', name: 'admin_activity_calendar', methods: ['GET'])]
    public function calendar(Request $request): Response
    {
        $routeName = (string) $request->attributes->get('_route');
        $isAdmin = str_starts_with($routeName, 'admin_');

        if ($isAdmin) {
            $this->denyAccessUnlessGranted('ROLE_ADMIN');
        } else {
            $this->denyAccessUnlessGranted('ROLE_USER');
        }

        return $this->render('activity/calendar.html.twig', [
            'isAdmin' => $isAdmin,
            'eventFeedUrl' => $this->generateUrl($isAdmin ? 'admin_activity_calendar_events' : 'trip_activity_calendar_events'),
            'userTripArea' => !$isAdmin,
            'tripAreaSection' => 'activities',
        ]);
    }

    #[Route('/trips/calendar/events', name: 'trip_activity_calendar_events', methods: ['GET'])]
    #[Route('/admin/activities/calendar/events', name: 'admin_activity_calendar_events', methods: ['GET'])]
    public function calendarEvents(Request $request, ActivityRepository $activityRepository): JsonResponse
    {
        $routeName = (string) $request->attributes->get('_route');
        $isAdmin = str_starts_with($routeName, 'admin_');
        $viewer = $this->getUser();
        $currentUser = $viewer instanceof User ? $viewer : null;

        if ($isAdmin) {
            $this->denyAccessUnlessGranted('ROLE_ADMIN');
        } else {
            $this->denyAccessUnlessGranted('ROLE_USER');
        }

        $events = [];
        foreach ($activityRepository->findCalendarActivitiesForUser($currentUser, $isAdmin) as $activity) {
            $date = $activity->getActivityDate();
            if ($date === null) {
                continue;
            }

            $startDateTime = null;
            if ($activity->getStartTime() !== null) {
                $startDateTime = \DateTimeImmutable::createFromFormat(
                    'Y-m-d H:i:s',
                    $date->format('Y-m-d') . ' ' . $activity->getStartTime()->format('H:i:s')
                );
            }

            $endDateTime = null;
            if ($activity->getEndTime() !== null) {
                $endDateTime = \DateTimeImmutable::createFromFormat(
                    'Y-m-d H:i:s',
                    $date->format('Y-m-d') . ' ' . $activity->getEndTime()->format('H:i:s')
                );
            }

            $events[] = [
                'id' => $activity->getId(),
                'title' => $activity->getTitle(),
                'start' => ($startDateTime ?: $date)->format(\DateTimeInterface::ATOM),
                'end' => $endDateTime?->format(\DateTimeInterface::ATOM),
                'allDay' => $startDateTime === null,
                'extendedProps' => [
                    'tripName' => $activity->getTrip()?->getTripName(),
                    'tripId' => $activity->getTrip()?->getId(),
                    'type' => $activity->getType(),
                    'status' => $activity->getStatus(),
                    'location' => $activity->getLocationName(),
                    'cost' => sprintf('%s %s', $activity->getCurrency(), number_format((float) ($activity->getCostAmount() ?? 0), 2, '.', ',')),
                    'participants' => $activity->getParticipants()->count(),
                    'capacity' => $activity->getTotalCapacity(),
                    'availableSeats' => $activity->getAvailableSeats(),
                    'detailsUrl' => $this->generateUrl($isAdmin ? 'admin_activity_show' : 'activity_show', ['id' => $activity->getId()]),
                ],
            ];
        }

        return $this->json($events);
    }

    #[Route('/admin/activities/new', name: 'admin_activity_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        ActivityRepository $activityRepository,
        ActivityLocationService $activityLocationService,
    ): Response
    {
        $activity = new Activity();
        $form = $this->createForm(ActivityType::class, $activity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($activityRepository->hasDuplicateActivity($activity)) {
                $form->addError(new FormError('A similar activity already exists for this trip and schedule.'));
            } elseif ($activity->getTotalCapacity() < $activity->getParticipants()->count()) {
                $form->get('totalCapacity')->addError(new FormError('Total capacity cannot be lower than joined participants.'));
            } else {
                $activityLocationService->syncActivityCoordinates($activity);
                $activity->recalculateAvailableSeatsFromJoinedCount($activity->getParticipants()->count());
                $entityManager->persist($activity);
                $entityManager->flush();
                $this->addFlash('success', 'Activity created successfully.');

                return $this->redirectToRoute('admin_activity_index');
            }
        }

        return $this->render('activity/new.html.twig', [
            'isAdmin' => true,
            'activity' => $activity,
            'form' => $form,
        ]);
    }

    #[Route('/activities/{id}', name: 'activity_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    #[Route('/admin/activities/{id}', name: 'admin_activity_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(
        Request $request,
        Activity $activity,
        ActivityWaitingListEntryRepository $activityWaitingListEntryRepository,
        ActivityLocationService $activityLocationService,
        EntityManagerInterface $entityManager,
        CurrencyConverterService $currencyConverterService,
    ): Response
    {
        $isAdmin = str_starts_with((string) $request->attributes->get('_route'), 'admin_');
        $viewer = $this->getUser();
        $currentUser = $viewer instanceof User ? $viewer : null;
        $isJoined = $currentUser ? $activity->isParticipant($currentUser) : false;
        $isWaiting = $currentUser ? $activityWaitingListEntryRepository->findActiveWaitingEntryForUser($activity, $currentUser) !== null : false;
        $hasLinkedTrip = $activity->getTrip() !== null;
        $canJoinTrip = $currentUser && $activity->getTrip() ? $activity->getTrip()->isParticipant($currentUser) : false;
        $selectedCurrency = $currencyConverterService->normalizeCurrency((string) $request->query->get('currency', $activity->getCurrency()));

        if ($activity->getLocationLatitude() === null || $activity->getLocationLongitude() === null) {
            $beforeLat = $activity->getLocationLatitude();
            $beforeLng = $activity->getLocationLongitude();
            $activityLocationService->syncActivityCoordinates($activity);
            if ($activity->getLocationLatitude() !== $beforeLat || $activity->getLocationLongitude() !== $beforeLng) {
                try {
                    $entityManager->flush();
                } catch (\Throwable) {
                    // Keep graceful behavior if coordinates cannot be persisted.
                }
            }
        }

        $lat = $activity->getLocationLatitude() ?? $activity->getTrip()?->getDestinationLatitude();
        $lng = $activity->getLocationLongitude() ?? $activity->getTrip()?->getDestinationLongitude();
        $activityMapItems = [];
        if ($lat !== null && $lng !== null) {
            $activityMapItems[] = [
                'id' => $activity->getId(),
                'title' => $activity->getTitle(),
                'type' => $activity->getType() ?: 'General',
                'status' => $activity->getStatus(),
                'date' => $activity->getActivityDate()?->format('M d, Y') ?? '-',
                'time' => $activity->getStartTime()?->format('H:i') ?? '-',
                'lat' => $lat,
                'lng' => $lng,
                'detailsUrl' => $this->generateUrl($isAdmin ? 'admin_activity_show' : 'activity_show', ['id' => $activity->getId()]),
            ];
        }

        $activityCostBase = (float) ($activity->getCostAmount() ?? 0.0);
        $activityCostConverted = $currencyConverterService->convert($activityCostBase, $activity->getCurrency(), $selectedCurrency);
        $showConvertedValues = $selectedCurrency !== $activity->getCurrency();
        $externalMapUrl = ($lat !== null && $lng !== null)
            ? sprintf('https://www.openstreetmap.org/?mlat=%s&mlon=%s#map=14/%s/%s', $lat, $lng, $lat, $lng)
            : null;

        return $this->render('activity/show.html.twig', [
            'isAdmin' => $isAdmin,
            'activity' => $activity,
            'isJoined' => $isJoined,
            'isWaiting' => $isWaiting,
            'hasLinkedTrip' => $hasLinkedTrip,
            'canJoinTrip' => $canJoinTrip,
            'selectedCurrency' => $selectedCurrency,
            'supportedCurrencies' => $currencyConverterService->getSupportedCurrenciesWithLabels(),
            'activityCostConverted' => $activityCostConverted,
            'showConvertedValues' => $showConvertedValues,
            'convertedCurrencySymbol' => $currencyConverterService->getSymbol($selectedCurrency),
            'mapCenterLat' => $lat,
            'mapCenterLng' => $lng,
            'activityMapItems' => $activityMapItems,
            'hasActivityMapCoordinates' => $lat !== null && $lng !== null,
            'externalMapUrl' => $externalMapUrl,
        ]);
    }

    #[Route('/admin/activities/{id}/edit', name: 'admin_activity_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(
        Request $request,
        Activity $activity,
        EntityManagerInterface $entityManager,
        ActivityRepository $activityRepository,
        ActivityLocationService $activityLocationService,
    ): Response
    {
        $form = $this->createForm(ActivityType::class, $activity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($activityRepository->hasDuplicateActivity($activity)) {
                $form->addError(new FormError('A similar activity already exists for this trip and schedule.'));
            } elseif ($activity->getTotalCapacity() < $activity->getParticipants()->count()) {
                $form->get('totalCapacity')->addError(new FormError('Total capacity cannot be lower than joined participants.'));
            } else {
                $activityLocationService->syncActivityCoordinates($activity);
                $activity->recalculateAvailableSeatsFromJoinedCount($activity->getParticipants()->count());
                $entityManager->flush();
                $this->addFlash('success', 'Activity updated successfully.');

                return $this->redirectToRoute('admin_activity_index');
            }
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
    public function join(Request $request, Activity $activity, ActivityParticipationService $activityParticipationService): Response
    {
        $currentUser = $this->getUser();
        if (!$currentUser instanceof User) {
            throw $this->createAccessDeniedException('Authentication required.');
        }

        if (!$this->isCsrfTokenValid('join_activity_' . $activity->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('danger', 'Invalid CSRF token.');

            return $this->redirectBack($request, 'trip_my_activities');
        }

        $result = $activityParticipationService->joinActivity($currentUser, $activity);
        $this->addFlash($result->toFlashType(), $result->getMessage());

        return $this->redirectBack($request, 'trip_my_activities');
    }

    #[Route('/activities/{id}/leave', name: 'activity_leave', requirements: ['id' => '\d+'], methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function leave(Request $request, Activity $activity, ActivityParticipationService $activityParticipationService): Response
    {
        $currentUser = $this->getUser();
        if (!$currentUser instanceof User) {
            throw $this->createAccessDeniedException('Authentication required.');
        }

        if (!$this->isCsrfTokenValid('leave_activity_' . $activity->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('danger', 'Invalid CSRF token.');

            return $this->redirectBack($request, 'trip_my_activities');
        }

        $result = $activityParticipationService->leaveActivity($currentUser, $activity);
        $this->addFlash($result->toFlashType(), $result->getMessage());

        return $this->redirectBack($request, 'trip_my_activities');
    }

    private function redirectBack(Request $request, string $fallbackRoute): RedirectResponse
    {
        $referer = (string) $request->headers->get('referer', '');
        if ($referer !== '') {
            return $this->redirect($referer);
        }

        return $this->redirectToRoute($fallbackRoute);
    }
}
