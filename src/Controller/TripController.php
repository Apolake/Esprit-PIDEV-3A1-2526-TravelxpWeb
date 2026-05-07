<?php

namespace App\Controller;

use App\Entity\Trip;
use App\Entity\User;
use App\Form\TripType;
use App\Repository\ActivityRepository;
use App\Repository\TripWaitingListEntryRepository;
use App\Repository\TripRepository;
use App\Repository\UserRepository;
use App\Service\TripParticipationService;
use App\Service\TripLocationService;
use App\Service\TripWeatherService;
use App\Service\GeocodingService;
use App\Service\CurrencyConverterService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class TripController extends AbstractController
{
    #[Route('/admin/location/suggest', name: 'admin_location_suggest', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function suggestLocations(Request $request, GeocodingService $geocodingService): JsonResponse
    {
        $q = (string) $request->query->get('q', '');
        if (mb_strlen(trim($q)) < 1) {
            return $this->json([]);
        }

        return $this->json($geocodingService->suggestPlaces($q, 8));
    }

    #[Route('/trips/browse', name: 'trip_browse', methods: ['GET'])]
    #[Route('/trips/my', name: 'trip_my', methods: ['GET'])]
    #[Route('/trips', name: 'trip_index', methods: ['GET'])]
    #[Route('/admin/trips', name: 'admin_trip_index', methods: ['GET'])]
    public function index(
        Request $request,
        TripRepository $tripRepository,
        TripWaitingListEntryRepository $tripWaitingListEntryRepository,
        EntityManagerInterface $entityManager,
        PaginatorInterface $paginator,
    ): Response
    {
        $routeName = (string) $request->attributes->get('_route');
        $isAdmin = str_starts_with($routeName, 'admin_');
        $isMyTripsPage = $routeName === 'trip_my';
        $currentUser = $this->getUser();
        $viewer = $currentUser instanceof User ? $currentUser : null;

        $filters = [
            'q' => (string) $request->query->get('q', ''),
            'status' => (string) $request->query->get('status', ''),
            'destination' => (string) $request->query->get('destination', ''),
            'myTrips' => $isMyTripsPage ? '1' : (string) $request->query->get('myTrips', ''),
            'sort' => (string) $request->query->get('sort', 'newest'),
        ];
        $view = (string) $request->query->get('view', $isAdmin ? 'table' : 'cards');
        if (!in_array($view, ['cards', 'table'], true)) {
            $view = $isAdmin ? 'table' : 'cards';
        }

        $page = max(1, $request->query->getInt('page', 1));
        $perPage = $isAdmin ? 10 : 9;
        $qb = $tripRepository->createFilteredQueryBuilder($filters, $isAdmin, $viewer);
        $pagination = $paginator->paginate($qb, $page, $perPage, [
            'distinct' => true,
        ]);
        $tripItems = $pagination->getItems();
        if ($tripItems instanceof \Traversable) {
            $tripItems = iterator_to_array($tripItems);
        }
        if (!is_array($tripItems)) {
            $tripItems = [];
        }

        $totalItems = (int) $pagination->getTotalItemCount();
        $totalPages = max(1, (int) ceil($totalItems / $perPage));
        $adminStats = null;

        if ($isAdmin) {
            $today = new \DateTimeImmutable('today');
            $totalTrips = (int) $tripRepository->count([]);
            $upcomingTrips = (int) $tripRepository->createQueryBuilder('t')
                ->select('COUNT(t.id)')
                ->andWhere('t.startDate >= :today')
                ->setParameter('today', $today)
                ->getQuery()
                ->getSingleScalarResult();
            $completedTrips = (int) $tripRepository->createQueryBuilder('t')
                ->select('COUNT(t.id)')
                ->andWhere('t.status IN (:statuses)')
                ->setParameter('statuses', ['COMPLETED', 'DONE'])
                ->getQuery()
                ->getSingleScalarResult();
            $joinedTrips = (int) $entityManager->createQuery(
                'SELECT COUNT(p.id) FROM App\Entity\Trip t JOIN t.participants p'
            )->getSingleScalarResult();

            $adminStats = [
                'totalTrips' => $totalTrips,
                'joinedTrips' => $joinedTrips,
                'upcomingTrips' => $upcomingTrips,
                'completedTrips' => $completedTrips,
            ];
        }

        return $this->render('trip/index.html.twig', [
            'isAdmin' => $isAdmin,
            'trips' => $tripItems,
            'filters' => $filters,
            'joinedTripIds' => $viewer ? $tripRepository->findJoinedTripIdsForUser($viewer) : [],
            'waitingTripIds' => $viewer ? $tripWaitingListEntryRepository->findActiveWaitingTripIdsForUser($viewer) : [],
            'destinations' => $tripRepository->getDistinctDestinations(),
            'routeName' => $routeName,
            'view' => $view,
            'userTripArea' => !$isAdmin,
            'tripAreaSection' => $isMyTripsPage ? 'my' : 'browse',
            'pagination' => [
                'page' => (int) $pagination->getCurrentPageNumber(),
                'perPage' => $perPage,
                'totalItems' => $totalItems,
                'totalPages' => $totalPages,
            ],
            'adminStats' => $adminStats,
        ]);
    }

    #[Route('/admin/trips/new', name: 'admin_trip_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        TripRepository $tripRepository,
        UserRepository $userRepository,
        TripLocationService $tripLocationService,
    ): Response
    {
        $trip = new Trip();
        $form = $this->createForm(TripType::class, $trip);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->hasInvalidForeignKeys($trip, $form, $tripRepository, $userRepository)) {
                // Errors are attached directly to relevant fields.
            } elseif ($tripRepository->hasDuplicateTrip($trip)) {
                $form->addError(new FormError('A trip with the same name, route, and date range already exists.'));
            } elseif ($tripRepository->hasSameDestinationTimeConflictForUser($trip)) {
                $form->addError(new FormError('This user already has a trip to the same destination in the same time period.'));
            } elseif ($trip->getTotalCapacity() < $trip->getParticipants()->count()) {
                $form->get('totalCapacity')->addError(new FormError('Total capacity cannot be lower than joined participants.'));
            } else {
                $tripLocationService->syncTripCoordinates($trip);
                $trip->recalculateAvailableSeatsFromJoinedCount($trip->getParticipants()->count());
                $entityManager->persist($trip);
                $entityManager->flush();
                $this->addFlash('success', 'Trip created successfully.');

                return $this->redirectToRoute('admin_trip_index');
            }
        }

        return $this->render('trip/new.html.twig', [
            'isAdmin' => true,
            'trip' => $trip,
            'form' => $form,
        ]);
    }

    #[Route('/trips/{id}', name: 'trip_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    #[Route('/admin/trips/{id}', name: 'admin_trip_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(
        Request $request,
        Trip $trip,
        ActivityRepository $activityRepository,
        TripWaitingListEntryRepository $tripWaitingListEntryRepository,
        TripWeatherService $tripWeatherService,
        CurrencyConverterService $currencyConverterService,
    ): Response
    {
        $isAdmin = str_starts_with((string) $request->attributes->get('_route'), 'admin_');
        $viewer = $this->getUser();
        $currentUser = $viewer instanceof User ? $viewer : null;
        $isJoined = $currentUser ? $trip->isParticipant($currentUser) : false;
        $isWaiting = $currentUser ? $tripWaitingListEntryRepository->findActiveWaitingEntryForUser($trip, $currentUser) !== null : false;
        $tripActivities = $activityRepository->createQueryBuilder('a')
            ->leftJoin('a.trip', 't')->addSelect('t')
            ->andWhere('a.trip = :trip')
            ->setParameter('trip', $trip)
            ->orderBy('a.activityDate', 'ASC')
            ->addOrderBy('a.startTime', 'ASC')
            ->getQuery()
            ->getResult();

        $joinedActivityIds = [];
        if ($currentUser) {
            $joinedActivityIds = $activityRepository->findJoinedActivityIdsForUser($currentUser);
        }

        $mapActivities = [];
        $selectedCurrency = $currencyConverterService->normalizeCurrency((string) $request->query->get('currency', $trip->getCurrency()));
        $convertedActivityCosts = [];
        $activityTotalsBase = 0.0;
        $activityTotalsConverted = 0.0;
        foreach ($tripActivities as $activity) {
            if (!$activity instanceof \App\Entity\Activity) {
                continue;
            }
            $activityAmount = (float) ($activity->getCostAmount() ?? 0.0);
            $activityConverted = $currencyConverterService->convert($activityAmount, $activity->getCurrency(), $selectedCurrency);
            $convertedActivityCosts[$activity->getId()] = $activityConverted;
            $activityTotalsBase += $activityAmount;
            $activityTotalsConverted += $activityConverted;

            $lat = $activity->getLocationLatitude() ?? $trip->getDestinationLatitude();
            $lng = $activity->getLocationLongitude() ?? $trip->getDestinationLongitude();
            if ($lat === null || $lng === null) {
                continue;
            }
            $mapActivities[] = [
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

        $budgetBase = (float) ($trip->getBudgetAmount() ?? 0.0);
        $budgetConverted = $currencyConverterService->convert($budgetBase, $trip->getCurrency(), $selectedCurrency);
        $expensesBase = (float) ($trip->getTotalExpenses() ?? 0.0);
        $expensesConverted = $currencyConverterService->convert($expensesBase, $trip->getCurrency(), $selectedCurrency);
        $showConvertedValues = $selectedCurrency !== $trip->getCurrency();

        return $this->render('trip/show.html.twig', [
            'isAdmin' => $isAdmin,
            'trip' => $trip,
            'isJoined' => $isJoined,
            'isWaiting' => $isWaiting,
            'tripActivities' => $tripActivities,
            'joinedActivityIds' => $joinedActivityIds,
            'userTripArea' => !$isAdmin,
            'tripAreaSection' => $isJoined ? 'my' : 'browse',
            'mapCenterLat' => $trip->getDestinationLatitude(),
            'mapCenterLng' => $trip->getDestinationLongitude(),
            'mapActivities' => $mapActivities,
            'tripWeather' => $tripWeatherService->fetchForTrip($trip),
            'selectedCurrency' => $selectedCurrency,
            'supportedCurrencies' => $currencyConverterService->getSupportedCurrenciesWithLabels(),
            'budgetConverted' => $budgetConverted,
            'expensesConverted' => $expensesConverted,
            'convertedActivityCosts' => $convertedActivityCosts,
            'activityTotalsBase' => $activityTotalsBase,
            'activityTotalsConverted' => $activityTotalsConverted,
            'showConvertedValues' => $showConvertedValues,
            'convertedCurrencySymbol' => $currencyConverterService->getSymbol($selectedCurrency),
            'currentUserEmail' => $currentUser?->getEmail(),
        ]);
    }

    #[Route('/admin/trips/{id}/edit', name: 'admin_trip_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(
        Request $request,
        Trip $trip,
        EntityManagerInterface $entityManager,
        TripRepository $tripRepository,
        UserRepository $userRepository,
        TripLocationService $tripLocationService,
    ): Response
    {
        $form = $this->createForm(TripType::class, $trip);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->hasInvalidForeignKeys($trip, $form, $tripRepository, $userRepository)) {
                // Errors are attached directly to relevant fields.
            } elseif ($tripRepository->hasDuplicateTrip($trip)) {
                $form->addError(new FormError('A trip with the same name, route, and date range already exists.'));
            } elseif ($tripRepository->hasSameDestinationTimeConflictForUser($trip)) {
                $form->addError(new FormError('This user already has a trip to the same destination in the same time period.'));
            } elseif ($trip->getTotalCapacity() < $trip->getParticipants()->count()) {
                $form->get('totalCapacity')->addError(new FormError('Total capacity cannot be lower than joined participants.'));
            } else {
                $tripLocationService->syncTripCoordinates($trip);
                $trip->recalculateAvailableSeatsFromJoinedCount($trip->getParticipants()->count());
                $entityManager->flush();
                $this->addFlash('success', 'Trip updated successfully.');

                return $this->redirectToRoute('admin_trip_index');
            }
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
    public function join(Request $request, Trip $trip, TripParticipationService $tripParticipationService): Response
    {
        $currentUser = $this->getUser();
        if (!$currentUser instanceof User) {
            throw $this->createAccessDeniedException('Authentication required.');
        }

        if (!$this->isCsrfTokenValid('join_trip_' . $trip->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('danger', 'Invalid CSRF token.');

            return $this->redirectBack($request, 'trip_browse');
        }

        $result = $tripParticipationService->joinTrip($currentUser, $trip);
        $this->addFlash($result->toFlashType(), $result->getMessage());

        return $this->redirectBack($request, 'trip_browse');
    }

    #[Route('/trips/{id}/leave', name: 'trip_leave', requirements: ['id' => '\d+'], methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function leave(Request $request, Trip $trip, TripParticipationService $tripParticipationService): Response
    {
        $currentUser = $this->getUser();
        if (!$currentUser instanceof User) {
            throw $this->createAccessDeniedException('Authentication required.');
        }

        if (!$this->isCsrfTokenValid('leave_trip_' . $trip->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('danger', 'Invalid CSRF token.');

            return $this->redirectBack($request, 'trip_my');
        }

        $result = $tripParticipationService->leaveTrip($currentUser, $trip);
        $this->addFlash($result->toFlashType(), $result->getMessage());

        return $this->redirectBack($request, 'trip_my');
    }

    private function hasInvalidForeignKeys(
        Trip $trip,
        FormInterface $form,
        TripRepository $tripRepository,
        UserRepository $userRepository,
    ): bool {
        $hasErrors = false;

        $userId = $trip->getUserId();
        if (null !== $userId && null === $userRepository->find($userId)) {
            $form->get('userId')->addError(new FormError(sprintf('User ID %d does not exist.', $userId)));
            $hasErrors = true;
        }

        $parentId = $trip->getParentId();
        if (null !== $parentId) {
            if (null === $tripRepository->find($parentId)) {
                $form->get('parentId')->addError(new FormError(sprintf('Parent trip ID %d does not exist.', $parentId)));
                $hasErrors = true;
            } elseif (null !== $trip->getId() && $trip->getId() === $parentId) {
                $form->get('parentId')->addError(new FormError('A trip cannot be its own parent.'));
                $hasErrors = true;
            }
        }

        return $hasErrors;
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
