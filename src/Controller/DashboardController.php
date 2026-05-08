<?php

namespace App\Controller;

use App\Entity\Activity;
use App\Entity\Trip;
use App\Repository\ActivityRepository;
use App\Repository\TripRepository;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/dashboard')]
final class DashboardController extends AbstractController
{
    #[Route('', name: 'app_dashboard', methods: ['GET'])]
    public function index(
        Request $request,
        TripRepository $tripRepository,
        ActivityRepository $activityRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $activeUser = $this->getActiveUserFromSession($request, $entityManager->getConnection());
        if ($activeUser === null) {
            return $this->redirectToRoute('app_entry');
        }

        return $this->render('dashboard/index.html.twig', [
            'dashboard_data' => $this->buildDashboardData($tripRepository, $activityRepository, $entityManager, $activeUser),
            'flow_role' => $activeUser['role'],
        ]);
    }

    #[Route('/state', name: 'app_dashboard_state', methods: ['GET'])]
    public function state(
        Request $request,
        TripRepository $tripRepository,
        ActivityRepository $activityRepository,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $activeUser = $this->getActiveUserFromSession($request, $entityManager->getConnection());
        if ($activeUser === null) {
            return $this->json(['ok' => false, 'message' => 'Session expired.'], 401);
        }

        return $this->json(
            $this->buildDashboardData($tripRepository, $activityRepository, $entityManager, $activeUser)
        );
    }

    #[Route('/trip-cards', name: 'app_dashboard_trip_cards', methods: ['GET'])]
    public function tripCards(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $connection = $entityManager->getConnection();
        $activeUser = $this->getActiveUserFromSession($request, $connection);
        if ($activeUser === null) {
            return $this->json(['ok' => false, 'message' => 'Session expired.'], 401);
        }

        $qb = $connection->createQueryBuilder();
        $qb
            ->select(
                't.id',
                't.trip_name',
                't.origin',
                't.destination',
                't.start_date',
                't.end_date',
                't.status',
                't.currency',
                'COALESCE(t.budget_amount, 0) AS budget_amount',
                'tp.user_id AS joined_by_user'
            )
            ->from('trips', 't')
            ->leftJoin('t', 'trip_participants', 'tp', 'tp.trip_id = t.id AND tp.user_id = :uid')
            ->setParameter('uid', $activeUser['id'])
            ->setMaxResults(160);

        $q = mb_strtolower(trim((string) $request->query->get('q', '')));
        if ($q !== '') {
            $qb->andWhere('(LOWER(t.trip_name) LIKE :q OR LOWER(COALESCE(t.origin, \'\')) LIKE :q OR LOWER(COALESCE(t.destination, \'\')) LIKE :q)')
                ->setParameter('q', '%'.$q.'%');
        }

        $status = strtoupper(trim((string) $request->query->get('status', '')));
        if ($status !== '') {
            $qb->andWhere('UPPER(COALESCE(t.status, \'\')) = :status')->setParameter('status', $status);
        }

        $minBudget = $request->query->get('minBudget');
        if ($minBudget !== null && $minBudget !== '' && is_numeric((string) $minBudget)) {
            $qb->andWhere('COALESCE(t.budget_amount, 0) >= :minBudget')->setParameter('minBudget', (float) $minBudget);
        }

        $maxBudget = $request->query->get('maxBudget');
        if ($maxBudget !== null && $maxBudget !== '' && is_numeric((string) $maxBudget)) {
            $qb->andWhere('COALESCE(t.budget_amount, 0) <= :maxBudget')->setParameter('maxBudget', (float) $maxBudget);
        }

        $sort = (string) $request->query->get('sort', 'recent');
        match ($sort) {
            'name_asc' => $qb->addOrderBy('t.trip_name', 'ASC'),
            'name_desc' => $qb->addOrderBy('t.trip_name', 'DESC'),
            'budget_asc' => $qb->addOrderBy('COALESCE(t.budget_amount,0)', 'ASC'),
            'budget_desc' => $qb->addOrderBy('COALESCE(t.budget_amount,0)', 'DESC'),
            'date_asc' => $qb->addOrderBy('t.start_date', 'ASC'),
            'date_desc' => $qb->addOrderBy('t.start_date', 'DESC'),
            default => $qb->addOrderBy('t.id', 'DESC'),
        };

        $scope = strtolower(trim((string) $request->query->get('scope', 'all')));
        if ($scope === 'mine') {
            $qb->andWhere('tp.user_id IS NOT NULL');
        }

        $rows = $qb->executeQuery()->fetchAllAssociative();

        return $this->json([
            'cards' => array_map(static fn (array $row): array => [
                'id' => (int) $row['id'],
                'tripName' => (string) $row['trip_name'],
                'origin' => $row['origin'],
                'destination' => $row['destination'],
                'startDate' => $row['start_date'],
                'endDate' => $row['end_date'],
                'status' => $row['status'] ?? 'PLANNED',
                'currency' => $row['currency'] ?? 'USD',
                'budgetAmount' => (float) $row['budget_amount'],
                'isJoined' => $row['joined_by_user'] !== null,
            ], $rows),
        ]);
    }

    #[Route('/activity-cards', name: 'app_dashboard_activity_cards', methods: ['GET'])]
    public function activityCards(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $connection = $entityManager->getConnection();
        $activeUser = $this->getActiveUserFromSession($request, $connection);
        if ($activeUser === null) {
            return $this->json(['ok' => false, 'message' => 'Session expired.'], 401);
        }

        $joinedTripIds = [];
        if ($activeUser['role'] === 'USER') {
            $joinedTripIds = array_map(
                static fn (array $r): int => (int) $r['trip_id'],
                $connection->fetchAllAssociative('SELECT trip_id FROM trip_participants WHERE user_id = :uid', ['uid' => $activeUser['id']])
            );
        }

        $qb = $connection->createQueryBuilder();
        $qb
            ->select(
                'a.id',
                'a.title',
                'a.type',
                'a.activity_date',
                'a.start_time',
                'a.end_time',
                'a.location_name',
                'a.status',
                'a.currency',
                'COALESCE(a.cost_amount, 0) AS cost_amount',
                'a.trip_id',
                't.trip_name',
                'tap.user_id AS joined_by_user'
            )
            ->from('activities', 'a')
            ->leftJoin('a', 'trips', 't', 't.id = a.trip_id')
            ->leftJoin('a', 'trip_activity_participants', 'tap', 'tap.activity_id = a.id AND tap.user_id = :uid')
            ->setParameter('uid', $activeUser['id'])
            ->setMaxResults(200);

        $q = mb_strtolower(trim((string) $request->query->get('q', '')));
        if ($q !== '') {
            $qb->andWhere('(LOWER(a.title) LIKE :q OR LOWER(COALESCE(a.type, \'\')) LIKE :q OR LOWER(COALESCE(a.location_name, \'\')) LIKE :q OR LOWER(COALESCE(t.trip_name, \'\')) LIKE :q)')
                ->setParameter('q', '%'.$q.'%');
        }

        $status = strtoupper(trim((string) $request->query->get('status', '')));
        if ($status !== '') {
            $qb->andWhere('UPPER(COALESCE(a.status, \'\')) = :status')->setParameter('status', $status);
        }

        $type = trim((string) $request->query->get('type', ''));
        if ($type !== '') {
            $qb->andWhere('LOWER(COALESCE(a.type, \'\')) = :type')->setParameter('type', mb_strtolower($type));
        }

        $tripId = $request->query->get('tripId');
        if ($tripId !== null && $tripId !== '') {
            $qb->andWhere('a.trip_id = :tripId')->setParameter('tripId', (int) $tripId);
        }

        if ($activeUser['role'] === 'USER') {
            if (count($joinedTripIds) === 0) {
                return $this->json(['cards' => []]);
            }

            if ($tripId === null || $tripId === '') {
                return $this->json(['cards' => []]);
            }

            if (!in_array((int) $tripId, $joinedTripIds, true)) {
                return $this->json(['cards' => []]);
            }

            $qb
                ->andWhere('a.trip_id IN (:joinedTripIds)')
                ->setParameter('joinedTripIds', $joinedTripIds, ArrayParameterType::INTEGER);
        }

        $sort = (string) $request->query->get('sort', 'recent');
        match ($sort) {
            'title_asc' => $qb->addOrderBy('a.title', 'ASC'),
            'title_desc' => $qb->addOrderBy('a.title', 'DESC'),
            'cost_asc' => $qb->addOrderBy('COALESCE(a.cost_amount,0)', 'ASC'),
            'cost_desc' => $qb->addOrderBy('COALESCE(a.cost_amount,0)', 'DESC'),
            'date_asc' => $qb->addOrderBy('a.activity_date', 'ASC'),
            'date_desc' => $qb->addOrderBy('a.activity_date', 'DESC'),
            default => $qb->addOrderBy('a.id', 'DESC'),
        };

        $rows = $qb->executeQuery()->fetchAllAssociative();

        return $this->json([
            'cards' => array_map(static fn (array $row): array => [
                'id' => (int) $row['id'],
                'title' => (string) $row['title'],
                'type' => $row['type'],
                'activityDate' => $row['activity_date'],
                'startTime' => $row['start_time'],
                'endTime' => $row['end_time'],
                'locationName' => $row['location_name'],
                'status' => $row['status'] ?? 'PLANNED',
                'currency' => $row['currency'] ?? 'USD',
                'costAmount' => (float) $row['cost_amount'],
                'tripId' => (int) $row['trip_id'],
                'tripName' => $row['trip_name'],
                'isJoined' => $row['joined_by_user'] !== null,
            ], $rows),
        ]);
    }

    #[Route('/join-trip/{tripId}', name: 'app_dashboard_join_trip', methods: ['POST'])]
    public function joinTrip(int $tripId, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $connection = $entityManager->getConnection();
        $activeUser = $this->getActiveUserFromSession($request, $connection);
        if ($activeUser === null || $activeUser['role'] !== 'USER') {
            return $this->json(['ok' => false, 'message' => 'Unauthorized action.'], 403);
        }

        $trip = $connection->fetchAssociative('SELECT id, COALESCE(budget_amount,0) AS budget_amount FROM trips WHERE id = :id', ['id' => $tripId]);
        if (!$trip) {
            return $this->json(['ok' => false, 'message' => 'Trip not found.'], 404);
        }

        $already = $connection->fetchOne(
            'SELECT 1 FROM trip_participants WHERE trip_id = :tripId AND user_id = :userId LIMIT 1',
            ['tripId' => $tripId, 'userId' => $activeUser['id']]
        );
        if ($already) {
            return $this->json(['ok' => true, 'message' => 'Trip already joined.']);
        }

        $price = (float) $trip['budget_amount'];
        if ($activeUser['balance'] < $price) {
            return $this->json(['ok' => false, 'message' => 'Insufficient balance for this trip.'], 422);
        }

        $connection->beginTransaction();
        try {
            $connection->insert('trip_participants', [
                'trip_id' => $tripId,
                'user_id' => $activeUser['id'],
                'joined_at' => (new \DateTimeImmutable())->format('Y-m-d H:i:s'),
            ]);
            $connection->executeStatement(
                'UPDATE users SET balance = balance - :price WHERE id = :id',
                ['price' => $price, 'id' => $activeUser['id']]
            );
            $connection->commit();
        } catch (\Throwable) {
            $connection->rollBack();

            return $this->json(['ok' => false, 'message' => 'Failed to join trip.'], 500);
        }

        return $this->json(['ok' => true, 'message' => 'Trip joined successfully.']);
    }

    #[Route('/leave-trip/{tripId}', name: 'app_dashboard_leave_trip', methods: ['POST'])]
    public function leaveTrip(int $tripId, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $connection = $entityManager->getConnection();
        $activeUser = $this->getActiveUserFromSession($request, $connection);
        if ($activeUser === null || $activeUser['role'] !== 'USER') {
            return $this->json(['ok' => false, 'message' => 'Unauthorized action.'], 403);
        }

        $trip = $connection->fetchAssociative(
            'SELECT id, COALESCE(budget_amount,0) AS budget_amount FROM trips WHERE id = :id',
            ['id' => $tripId]
        );
        if (!$trip) {
            return $this->json(['ok' => false, 'message' => 'Trip not found.'], 404);
        }

        $isJoined = $connection->fetchOne(
            'SELECT 1 FROM trip_participants WHERE trip_id = :tripId AND user_id = :userId LIMIT 1',
            ['tripId' => $tripId, 'userId' => $activeUser['id']]
        );
        if (!$isJoined) {
            return $this->json(['ok' => false, 'message' => 'Trip is not in My Trips.'], 422);
        }

        $activityCosts = (float) $connection->fetchOne(
            'SELECT COALESCE(SUM(a.cost_amount), 0)
             FROM trip_activity_participants tap
             JOIN activities a ON a.id = tap.activity_id
             WHERE tap.user_id = :uid AND a.trip_id = :tripId',
            ['uid' => $activeUser['id'], 'tripId' => $tripId]
        );
        $tripCost = (float) $trip['budget_amount'];
        $refund = $tripCost + $activityCosts;

        $connection->beginTransaction();
        try {
            $connection->executeStatement(
                'DELETE tap FROM trip_activity_participants tap
                 JOIN activities a ON a.id = tap.activity_id
                 WHERE tap.user_id = :uid AND a.trip_id = :tripId',
                ['uid' => $activeUser['id'], 'tripId' => $tripId]
            );
            $connection->executeStatement(
                'DELETE FROM trip_participants WHERE user_id = :uid AND trip_id = :tripId',
                ['uid' => $activeUser['id'], 'tripId' => $tripId]
            );
            $connection->executeStatement(
                'UPDATE users SET balance = balance + :refund WHERE id = :uid',
                ['refund' => $refund, 'uid' => $activeUser['id']]
            );
            $connection->commit();
        } catch (\Throwable) {
            $connection->rollBack();

            return $this->json(['ok' => false, 'message' => 'Failed to leave trip.'], 500);
        }

        return $this->json(['ok' => true, 'message' => 'Trip removed from My Trips.']);
    }

    #[Route('/join-activity/{activityId}', name: 'app_dashboard_join_activity', methods: ['POST'])]
    public function joinActivity(int $activityId, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $connection = $entityManager->getConnection();
        $activeUser = $this->getActiveUserFromSession($request, $connection);
        if ($activeUser === null || $activeUser['role'] !== 'USER') {
            return $this->json(['ok' => false, 'message' => 'Unauthorized action.'], 403);
        }

        $activity = $connection->fetchAssociative(
            'SELECT id, trip_id, COALESCE(cost_amount,0) AS cost_amount FROM activities WHERE id = :id',
            ['id' => $activityId]
        );
        if (!$activity) {
            return $this->json(['ok' => false, 'message' => 'Activity not found.'], 404);
        }

        $tripSelected = $connection->fetchOne(
            'SELECT 1 FROM trip_participants WHERE trip_id = :tripId AND user_id = :userId LIMIT 1',
            ['tripId' => (int) $activity['trip_id'], 'userId' => $activeUser['id']]
        );
        if (!$tripSelected) {
            return $this->json(['ok' => false, 'message' => 'Join the related trip first.'], 422);
        }

        $already = $connection->fetchOne(
            'SELECT 1 FROM trip_activity_participants WHERE activity_id = :activityId AND user_id = :userId LIMIT 1',
            ['activityId' => $activityId, 'userId' => $activeUser['id']]
        );
        if ($already) {
            return $this->json(['ok' => true, 'message' => 'Activity already added.']);
        }

        $cost = (float) $activity['cost_amount'];
        if ($activeUser['balance'] < $cost) {
            return $this->json(['ok' => false, 'message' => 'Insufficient balance for this activity.'], 422);
        }

        $connection->beginTransaction();
        try {
            $connection->insert('trip_activity_participants', [
                'activity_id' => $activityId,
                'user_id' => $activeUser['id'],
                'joined_at' => (new \DateTimeImmutable())->format('Y-m-d H:i:s'),
            ]);
            $connection->executeStatement(
                'UPDATE users SET balance = balance - :cost WHERE id = :id',
                ['cost' => $cost, 'id' => $activeUser['id']]
            );
            $connection->commit();
        } catch (\Throwable) {
            $connection->rollBack();

            return $this->json(['ok' => false, 'message' => 'Failed to add activity.'], 500);
        }

        return $this->json(['ok' => true, 'message' => 'Activity added successfully.']);
    }

    private function buildDashboardData(
        TripRepository $tripRepository,
        ActivityRepository $activityRepository,
        EntityManagerInterface $entityManager,
        array $activeUser
    ): array {
        $connection = $entityManager->getConnection();

        $tripCount = (int) $tripRepository->count([]);
        $activityCount = (int) $activityRepository->count([]);

        $tripStatusRows = $connection->fetchAllAssociative(
            "SELECT COALESCE(NULLIF(UPPER(status), ''), 'UNSPECIFIED') AS status_label, COUNT(*) AS total
             FROM trips GROUP BY status_label ORDER BY total DESC"
        );
        $activityStatusRows = $connection->fetchAllAssociative(
            "SELECT COALESCE(NULLIF(UPPER(status), ''), 'UNSPECIFIED') AS status_label, COUNT(*) AS total
             FROM activities GROUP BY status_label ORDER BY total DESC"
        );

        $tripCompletedCount = (int) $connection->fetchOne("SELECT COUNT(*) FROM trips WHERE UPPER(COALESCE(status,'')) IN ('COMPLETED','DONE')");
        $activityCompletedCount = (int) $connection->fetchOne("SELECT COUNT(*) FROM activities WHERE UPPER(COALESCE(status,'')) IN ('COMPLETED','DONE')");
        $upcomingTripsCount = (int) $connection->fetchOne(
            'SELECT COUNT(*) FROM trips WHERE start_date >= CURRENT_DATE()'
        );
        $todayActivitiesCount = (int) $connection->fetchOne(
            'SELECT COUNT(*) FROM activities WHERE activity_date = CURRENT_DATE()'
        );
        $avgTripDuration = (float) $connection->fetchOne(
            'SELECT COALESCE(AVG(DATEDIFF(end_date, start_date) + 1), 0) FROM trips WHERE start_date IS NOT NULL AND end_date IS NOT NULL'
        );
        $linkedActivityCount = (int) $connection->fetchOne(
            'SELECT COUNT(*) FROM activities WHERE trip_id IS NOT NULL'
        );
        $unlinkedActivityCount = (int) $connection->fetchOne(
            'SELECT COUNT(*) FROM activities WHERE trip_id IS NULL'
        );
        $topDestinationsRows = $connection->fetchAllAssociative(
            "SELECT COALESCE(NULLIF(destination, ''), 'Unknown') AS destination, COUNT(*) AS total
             FROM trips
             GROUP BY destination
             ORDER BY total DESC, destination ASC
             LIMIT 6"
        );
        $activityTypeBreakdownRows = $connection->fetchAllAssociative(
            "SELECT COALESCE(NULLIF(type, ''), 'General') AS type_label, COUNT(*) AS total
             FROM activities
             GROUP BY type_label
             ORDER BY total DESC, type_label ASC"
        );

        $finance = $connection->fetchAssociative(
            'SELECT COALESCE(SUM(budget_amount),0) AS total_trip_budget, COALESCE(SUM(total_expenses),0) AS total_trip_expenses FROM trips'
        ) ?: ['total_trip_budget' => 0, 'total_trip_expenses' => 0];
        $activityFinance = $connection->fetchAssociative(
            'SELECT COALESCE(SUM(cost_amount),0) AS total_activity_cost, COALESCE(AVG(cost_amount),0) AS avg_activity_cost FROM activities'
        ) ?: ['total_activity_cost' => 0, 'avg_activity_cost' => 0];

        $joinedTripIds = array_map(static fn (array $r): int => (int) $r['trip_id'], $connection->fetchAllAssociative('SELECT trip_id FROM trip_participants WHERE user_id = :uid', ['uid' => $activeUser['id']]));
        $joinedActivityIds = array_map(static fn (array $r): int => (int) $r['activity_id'], $connection->fetchAllAssociative('SELECT activity_id FROM trip_activity_participants WHERE user_id = :uid', ['uid' => $activeUser['id']]));

        $trips = $tripRepository->findBy([], ['id' => 'DESC'], 80);
        $activities = $activityRepository->findBy([], ['id' => 'DESC'], 120);

        return [
            'active_user' => $activeUser,
            'my_trip_ids' => $joinedTripIds,
            'my_activity_ids' => $joinedActivityIds,
            'trip_count' => $tripCount,
            'activity_count' => $activityCount,
            'trip_completed_count' => $tripCompletedCount,
            'activity_completed_count' => $activityCompletedCount,
            'upcoming_trips_count' => $upcomingTripsCount,
            'today_activities_count' => $todayActivitiesCount,
            'avg_trip_duration' => $avgTripDuration,
            'linked_activity_count' => $linkedActivityCount,
            'unlinked_activity_count' => $unlinkedActivityCount,
            'trip_status_rows' => array_map(static fn (array $r): array => ['status_label' => (string) $r['status_label'], 'total' => (int) $r['total']], $tripStatusRows),
            'activity_status_rows' => array_map(static fn (array $r): array => ['status_label' => (string) $r['status_label'], 'total' => (int) $r['total']], $activityStatusRows),
            'top_destinations' => array_map(static fn (array $r): array => ['destination' => (string) $r['destination'], 'total' => (int) $r['total']], $topDestinationsRows),
            'activity_type_breakdown' => array_map(static fn (array $r): array => ['type_label' => (string) $r['type_label'], 'total' => (int) $r['total']], $activityTypeBreakdownRows),
            'total_trip_budget' => (float) $finance['total_trip_budget'],
            'total_trip_expenses' => (float) $finance['total_trip_expenses'],
            'total_activity_cost' => (float) $activityFinance['total_activity_cost'],
            'avg_activity_cost' => (float) $activityFinance['avg_activity_cost'],
            'trips' => array_map(static fn (Trip $t): array => [
                'id' => $t->getId(),
                'tripName' => $t->getTripName(),
                'userId' => $t->getOwner()?->getId(),
                'origin' => $t->getOrigin(),
                'destination' => $t->getDestination(),
                'startDate' => $t->getStartDate()?->format('Y-m-d'),
                'endDate' => $t->getEndDate()?->format('Y-m-d'),
                'currency' => $t->getCurrency(),
                'budgetAmount' => $t->getBudgetAmount(),
                'status' => $t->getStatus(),
            ], $trips),
            'activities' => array_map(static fn (Activity $a): array => [
                'id' => $a->getId(),
                'title' => $a->getTitle(),
                'type' => $a->getType(),
                'tripId' => $a->getTrip()?->getId(),
                'tripName' => $a->getTrip()?->getTripName(),
                'activityDate' => $a->getActivityDate()?->format('Y-m-d'),
                'startTime' => $a->getStartTime()?->format('H:i'),
                'endTime' => $a->getEndTime()?->format('H:i'),
                'currency' => $a->getCurrency(),
                'costAmount' => $a->getCostAmount(),
                'status' => $a->getStatus(),
            ], $activities),
        ];
    }

    private function getActiveUserFromSession(Request $request, Connection $connection): ?array
    {
        $session = $request->getSession();
        $userId = (int) $session->get('active_user_id', 0);
        $role = strtoupper((string) $session->get('active_role', ''));
        if ($userId <= 0 || !in_array($role, ['ADMIN', 'USER'], true)) {
            return null;
        }

        $user = $connection->fetchAssociative(
            'SELECT id, username, email, role, COALESCE(balance,0) AS balance FROM users WHERE id = :id LIMIT 1',
            ['id' => $userId]
        );
        if (!$user || strtoupper((string) $user['role']) !== $role) {
            return null;
        }

        return [
            'id' => (int) $user['id'],
            'username' => (string) $user['username'],
            'email' => (string) $user['email'],
            'role' => strtoupper((string) $user['role']),
            'balance' => (float) $user['balance'],
        ];
    }
}
