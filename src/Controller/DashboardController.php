<?php

namespace App\Controller;

use App\Entity\Activity;
use App\Entity\Trip;
use App\Repository\ActivityRepository;
use App\Repository\TripRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_home', methods: ['GET'])]
    public function index(
        TripRepository $tripRepository,
        ActivityRepository $activityRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $dashboardData = $this->buildDashboardData($tripRepository, $activityRepository, $entityManager);

        return $this->render('dashboard/index.html.twig', [
            'dashboard_data' => $dashboardData,
        ]);
    }

    #[Route('/dashboard/state', name: 'app_dashboard_state', methods: ['GET'])]
    public function state(
        TripRepository $tripRepository,
        ActivityRepository $activityRepository,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        return $this->json(
            $this->buildDashboardData($tripRepository, $activityRepository, $entityManager)
        );
    }

    private function buildDashboardData(
        TripRepository $tripRepository,
        ActivityRepository $activityRepository,
        EntityManagerInterface $entityManager
    ): array {
        $connection = $entityManager->getConnection();

        $tripCount = (int) $tripRepository->count([]);
        $activityCount = (int) $activityRepository->count([]);
        $linkedActivityCount = (int) $connection->fetchOne('SELECT COUNT(*) FROM activities WHERE trip_id IS NOT NULL');
        $unlinkedActivityCount = (int) $connection->fetchOne('SELECT COUNT(*) FROM activities WHERE trip_id IS NULL');

        $tripStatusRows = $connection->fetchAllAssociative(
            "SELECT COALESCE(NULLIF(UPPER(status), ''), 'UNSPECIFIED') AS status_label, COUNT(*) AS total
             FROM trips
             GROUP BY status_label
             ORDER BY total DESC"
        );
        $activityStatusRows = $connection->fetchAllAssociative(
            "SELECT COALESCE(NULLIF(UPPER(status), ''), 'UNSPECIFIED') AS status_label, COUNT(*) AS total
             FROM activities
             GROUP BY status_label
             ORDER BY total DESC"
        );

        $tripCompletedCount = 0;
        $tripPlannedCount = 0;
        foreach ($tripStatusRows as $row) {
            $status = (string) $row['status_label'];
            $total = (int) $row['total'];
            if (in_array($status, ['COMPLETED', 'DONE'], true)) {
                $tripCompletedCount += $total;
            }
            if ($status === 'PLANNED') {
                $tripPlannedCount += $total;
            }
        }

        $activityCompletedCount = 0;
        $activityPlannedCount = 0;
        foreach ($activityStatusRows as $row) {
            $status = (string) $row['status_label'];
            $total = (int) $row['total'];
            if (in_array($status, ['COMPLETED', 'DONE'], true)) {
                $activityCompletedCount += $total;
            }
            if ($status === 'PLANNED') {
                $activityPlannedCount += $total;
            }
        }

        $finance = $connection->fetchAssociative(
            'SELECT
                COALESCE(SUM(budget_amount), 0) AS total_trip_budget,
                COALESCE(SUM(total_expenses), 0) AS total_trip_expenses
             FROM trips'
        ) ?: ['total_trip_budget' => 0, 'total_trip_expenses' => 0];

        $activityFinance = $connection->fetchAssociative(
            'SELECT
                COALESCE(SUM(cost_amount), 0) AS total_activity_cost,
                COALESCE(AVG(cost_amount), 0) AS avg_activity_cost
             FROM activities'
        ) ?: ['total_activity_cost' => 0, 'avg_activity_cost' => 0];

        $avgTripDuration = (float) $connection->fetchOne(
            'SELECT COALESCE(AVG(DATEDIFF(end_date, start_date) + 1), 0)
             FROM trips
             WHERE start_date IS NOT NULL AND end_date IS NOT NULL'
        );

        $upcomingTripsCount = (int) $connection->fetchOne(
            "SELECT COUNT(*)
             FROM trips
             WHERE start_date IS NOT NULL
               AND start_date >= CURRENT_DATE()
               AND COALESCE(NULLIF(UPPER(status), ''), 'PLANNED') NOT IN ('COMPLETED', 'DONE', 'CANCELLED')"
        );

        $todayActivitiesCount = (int) $connection->fetchOne(
            'SELECT COUNT(*) FROM activities WHERE activity_date = CURRENT_DATE()'
        );

        $topDestinations = $connection->fetchAllAssociative(
            "SELECT destination, COUNT(*) AS total
             FROM trips
             WHERE destination IS NOT NULL AND destination <> ''
             GROUP BY destination
             ORDER BY total DESC, destination ASC
             LIMIT 5"
        );

        $activityTypeBreakdown = $connection->fetchAllAssociative(
            "SELECT COALESCE(NULLIF(type, ''), 'General') AS type_label, COUNT(*) AS total
             FROM activities
             GROUP BY type_label
             ORDER BY total DESC, type_label ASC
             LIMIT 6"
        );

        $recentTrips = $tripRepository->findBy([], ['id' => 'DESC'], 8);
        $recentActivities = $activityRepository->findBy([], ['id' => 'DESC'], 8);
        $trips = $tripRepository->findBy([], ['id' => 'DESC'], 50);
        $activities = $activityRepository->findBy([], ['id' => 'DESC'], 50);

        return [
            'trip_count' => $tripCount,
            'activity_count' => $activityCount,
            'linked_activity_count' => $linkedActivityCount,
            'unlinked_activity_count' => $unlinkedActivityCount,
            'trip_completed_count' => $tripCompletedCount,
            'trip_planned_count' => $tripPlannedCount,
            'activity_completed_count' => $activityCompletedCount,
            'activity_planned_count' => $activityPlannedCount,
            'trip_status_rows' => array_map(
                static fn (array $row): array => [
                    'status_label' => (string) $row['status_label'],
                    'total' => (int) $row['total'],
                ],
                $tripStatusRows
            ),
            'activity_status_rows' => array_map(
                static fn (array $row): array => [
                    'status_label' => (string) $row['status_label'],
                    'total' => (int) $row['total'],
                ],
                $activityStatusRows
            ),
            'total_trip_budget' => (float) $finance['total_trip_budget'],
            'total_trip_expenses' => (float) $finance['total_trip_expenses'],
            'total_activity_cost' => (float) $activityFinance['total_activity_cost'],
            'avg_activity_cost' => (float) $activityFinance['avg_activity_cost'],
            'avg_trip_duration' => $avgTripDuration,
            'upcoming_trips_count' => $upcomingTripsCount,
            'today_activities_count' => $todayActivitiesCount,
            'top_destinations' => array_map(
                static fn (array $row): array => [
                    'destination' => (string) $row['destination'],
                    'total' => (int) $row['total'],
                ],
                $topDestinations
            ),
            'activity_type_breakdown' => array_map(
                static fn (array $row): array => [
                    'type_label' => (string) $row['type_label'],
                    'total' => (int) $row['total'],
                ],
                $activityTypeBreakdown
            ),
            'recent_trips' => array_map(
                static function (Trip $trip): array {
                    return [
                        'id' => $trip->getId(),
                        'tripName' => $trip->getTripName(),
                        'origin' => $trip->getOrigin(),
                        'destination' => $trip->getDestination(),
                        'status' => $trip->getStatus(),
                    ];
                },
                $recentTrips
            ),
            'recent_activities' => array_map(
                static function (Activity $activity): array {
                    return [
                        'id' => $activity->getId(),
                        'title' => $activity->getTitle(),
                        'status' => $activity->getStatus(),
                        'tripName' => $activity->getTrip()?->getTripName(),
                    ];
                },
                $recentActivities
            ),
            'trips' => array_map(
                static function (Trip $trip): array {
                    return [
                        'id' => $trip->getId(),
                        'tripName' => $trip->getTripName(),
                        'userId' => $trip->getUserId(),
                        'origin' => $trip->getOrigin(),
                        'destination' => $trip->getDestination(),
                        'startDate' => $trip->getStartDate()?->format('Y-m-d'),
                        'endDate' => $trip->getEndDate()?->format('Y-m-d'),
                        'currency' => $trip->getCurrency(),
                        'budgetAmount' => $trip->getBudgetAmount(),
                        'status' => $trip->getStatus(),
                    ];
                },
                $trips
            ),
            'activities' => array_map(
                static function (Activity $activity): array {
                    return [
                        'id' => $activity->getId(),
                        'title' => $activity->getTitle(),
                        'type' => $activity->getType(),
                        'tripId' => $activity->getTrip()?->getId(),
                        'tripName' => $activity->getTrip()?->getTripName(),
                        'activityDate' => $activity->getActivityDate()?->format('Y-m-d'),
                        'startTime' => $activity->getStartTime()?->format('H:i'),
                        'endTime' => $activity->getEndTime()?->format('H:i'),
                        'currency' => $activity->getCurrency(),
                        'costAmount' => $activity->getCostAmount(),
                        'status' => $activity->getStatus(),
                    ];
                },
                $activities
            ),
        ];
    }
}
