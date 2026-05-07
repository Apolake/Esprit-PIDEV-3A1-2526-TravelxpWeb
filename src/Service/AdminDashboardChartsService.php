<?php

namespace App\Service;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

class AdminDashboardChartsService
{
    public function __construct(
        private readonly ChartBuilderInterface $chartBuilder,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @return array{
     *   tripsByStatus: Chart,
     *   activitiesByStatus: Chart,
     *   activitiesByType: Chart,
     *   tripsMonthlyTrend: Chart,
     *   seats: Chart,
     *   waitingList: Chart
     * }
     */
    public function buildCharts(): array
    {
        return [
            'tripsByStatus' => $this->buildTripsByStatusChart(),
            'activitiesByStatus' => $this->buildActivitiesByStatusChart(),
            'activitiesByType' => $this->buildActivitiesByTypeChart(),
            'tripsMonthlyTrend' => $this->buildTripsMonthlyTrendChart(),
            'seats' => $this->buildSeatsChart(),
            'waitingList' => $this->buildWaitingListChart(),
        ];
    }

    private function buildTripsByStatusChart(): Chart
    {
        $rows = $this->connection()->fetchAllAssociative(
            "SELECT UPPER(COALESCE(status, 'UNKNOWN')) AS label, COUNT(*) AS total
             FROM trips
             GROUP BY UPPER(COALESCE(status, 'UNKNOWN'))
             ORDER BY total DESC"
        );

        $labels = [];
        $values = [];
        foreach ($rows as $row) {
            $labels[] = (string) ($row['label'] ?? 'UNKNOWN');
            $values[] = (int) ($row['total'] ?? 0);
        }

        $chart = $this->chartBuilder->createChart(Chart::TYPE_DOUGHNUT);
        $chart->setData([
            'labels' => $labels,
            'datasets' => [[
                'label' => 'Trips',
                'data' => $values,
                'backgroundColor' => ['#4f8cff', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#0ea5e9', '#64748b'],
                'borderWidth' => 0,
            ]],
        ]);
        $chart->setOptions($this->baseChartOptions(true));

        return $chart;
    }

    private function buildActivitiesByStatusChart(): Chart
    {
        $rows = $this->connection()->fetchAllAssociative(
            "SELECT UPPER(COALESCE(status, 'UNKNOWN')) AS label, COUNT(*) AS total
             FROM activities
             GROUP BY UPPER(COALESCE(status, 'UNKNOWN'))
             ORDER BY total DESC"
        );

        $labels = [];
        $values = [];
        foreach ($rows as $row) {
            $labels[] = (string) ($row['label'] ?? 'UNKNOWN');
            $values[] = (int) ($row['total'] ?? 0);
        }

        $chart = $this->chartBuilder->createChart(Chart::TYPE_BAR);
        $chart->setData([
            'labels' => $labels,
            'datasets' => [[
                'label' => 'Activities',
                'data' => $values,
                'backgroundColor' => '#3b82f6',
                'borderRadius' => 8,
                'borderSkipped' => false,
            ]],
        ]);
        $chart->setOptions(array_merge($this->baseChartOptions(), [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => ['precision' => 0],
                ],
            ],
        ]));

        return $chart;
    }

    private function buildActivitiesByTypeChart(): Chart
    {
        $rows = $this->connection()->fetchAllAssociative(
            "SELECT COALESCE(NULLIF(TRIM(type), ''), 'GENERAL') AS label, COUNT(*) AS total
             FROM activities
             GROUP BY COALESCE(NULLIF(TRIM(type), ''), 'GENERAL')
             ORDER BY total DESC"
        );

        $labels = [];
        $values = [];
        foreach ($rows as $row) {
            $labels[] = strtoupper((string) ($row['label'] ?? 'GENERAL'));
            $values[] = (int) ($row['total'] ?? 0);
        }

        $chart = $this->chartBuilder->createChart(Chart::TYPE_POLAR_AREA);
        $chart->setData([
            'labels' => $labels,
            'datasets' => [[
                'label' => 'Activity Types',
                'data' => $values,
                'backgroundColor' => ['#2563eb', '#14b8a6', '#a855f7', '#f97316', '#eab308', '#22c55e', '#f43f5e'],
                'borderWidth' => 1,
            ]],
        ]);
        $chart->setOptions($this->baseChartOptions(true));

        return $chart;
    }

    private function buildTripsMonthlyTrendChart(): Chart
    {
        $months = $this->lastMonths(6);
        $monthLabels = array_column($months, 'label');
        $monthKeys = array_column($months, 'key');

        $rows = $this->connection()->fetchAllAssociative(
            "SELECT DATE_FORMAT(created_at, '%Y-%m') AS month_key, COUNT(*) AS total
             FROM trips
             WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
             GROUP BY DATE_FORMAT(created_at, '%Y-%m')
             ORDER BY month_key ASC"
        );

        $lookup = [];
        foreach ($rows as $row) {
            $lookup[(string) ($row['month_key'] ?? '')] = (int) ($row['total'] ?? 0);
        }

        $values = array_map(static fn (string $key): int => $lookup[$key] ?? 0, $monthKeys);

        $chart = $this->chartBuilder->createChart(Chart::TYPE_LINE);
        $chart->setData([
            'labels' => $monthLabels,
            'datasets' => [[
                'label' => 'Trips Created',
                'data' => $values,
                'borderColor' => '#38bdf8',
                'backgroundColor' => 'rgba(56, 189, 248, 0.2)',
                'fill' => true,
                'tension' => 0.35,
                'pointRadius' => 4,
                'pointBackgroundColor' => '#0ea5e9',
            ]],
        ]);
        $chart->setOptions(array_merge($this->baseChartOptions(), [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => ['precision' => 0],
                ],
            ],
        ]));

        return $chart;
    }

    private function buildSeatsChart(): Chart
    {
        $row = $this->connection()->fetchAssociative(
            "SELECT
                COALESCE(SUM(GREATEST(total_capacity - available_seats, 0)), 0) AS joined_seats,
                COALESCE(SUM(available_seats), 0) AS available_seats
             FROM trips"
        ) ?: [];

        $joinedSeats = (int) ($row['joined_seats'] ?? 0);
        $availableSeats = (int) ($row['available_seats'] ?? 0);

        $chart = $this->chartBuilder->createChart(Chart::TYPE_PIE);
        $chart->setData([
            'labels' => ['Joined Seats', 'Available Seats'],
            'datasets' => [[
                'label' => 'Seats',
                'data' => [$joinedSeats, $availableSeats],
                'backgroundColor' => ['#10b981', '#6366f1'],
            ]],
        ]);
        $chart->setOptions($this->baseChartOptions(true));

        return $chart;
    }

    private function buildWaitingListChart(): Chart
    {
        $tripWaiting = (int) $this->connection()->fetchOne(
            "SELECT COUNT(*) FROM trip_waiting_list WHERE status = 'WAITING'"
        );
        $activityWaiting = (int) $this->connection()->fetchOne(
            "SELECT COUNT(*) FROM activity_waiting_list WHERE status = 'WAITING'"
        );

        $chart = $this->chartBuilder->createChart(Chart::TYPE_DOUGHNUT);
        $chart->setData([
            'labels' => ['Trip Waiting List', 'Activity Waiting List'],
            'datasets' => [[
                'label' => 'Waiting Count',
                'data' => [$tripWaiting, $activityWaiting],
                'backgroundColor' => ['#f59e0b', '#f97316'],
                'borderWidth' => 0,
            ]],
        ]);
        $chart->setOptions($this->baseChartOptions(true));

        return $chart;
    }

    /**
     * @return array<string, mixed>
     */
    private function baseChartOptions(bool $showLegend = false): array
    {
        return [
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'display' => $showLegend,
                    'position' => 'bottom',
                ],
            ],
        ];
    }

    /**
     * @return list<array{key: string, label: string}>
     */
    private function lastMonths(int $count): array
    {
        $count = max(1, $count);
        $start = new \DateTimeImmutable('first day of this month');
        $cursor = $start->modify(sprintf('-%d months', $count - 1));
        $months = [];

        for ($i = 0; $i < $count; ++$i) {
            $months[] = [
                'key' => $cursor->format('Y-m'),
                'label' => $cursor->format('M Y'),
            ];
            $cursor = $cursor->modify('+1 month');
        }

        return $months;
    }

    private function connection(): Connection
    {
        return $this->entityManager->getConnection();
    }
}

