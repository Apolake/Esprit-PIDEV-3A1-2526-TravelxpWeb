<?php

namespace App\Service;

use App\Repository\NotificationRepository;
use App\Repository\TripRepository;

class WeatherAlertSchedulerService
{
    public function __construct(
        private readonly TripRepository $tripRepository,
        private readonly TripWeatherService $tripWeatherService,
        private readonly NotificationService $notificationService,
        private readonly NotificationRepository $notificationRepository,
    ) {
    }

    /**
     * @return array{tripChecks:int, alerts:int}
     */
    public function runWeatherChecks(\DateTimeImmutable $now): array
    {
        $toDate = $now->modify('+5 days');
        $tripChecks = 0;
        $alerts = 0;

        foreach ($this->tripRepository->findUpcomingTripsForWeatherMonitoring($now, $toDate, 80) as $trip) {
            ++$tripChecks;
            $weather = $this->tripWeatherService->fetchForTrip($trip);
            if ($weather === null) {
                continue;
            }

            $warnings = array_values(
                array_filter(
                    array_map(static fn (mixed $value): string => trim((string) $value), (array) ($weather['warnings'] ?? [])),
                    static fn (string $warning): bool => $warning !== ''
                )
            );

            if ($warnings === []) {
                continue;
            }

            $title = sprintf('Weather alert for "%s"', (string) $trip->getTripName());
            $summary = implode(' ', array_slice($warnings, 0, 2));

            foreach ($trip->getParticipants() as $participant) {
                if ($this->notificationRepository->hasRecentByTypeAndTitle($participant, 'TRIP_WEATHER_WARNING', $title, $now->modify('-12 hours'))) {
                    continue;
                }

                $this->notificationService->create(
                    $participant,
                    'TRIP_WEATHER_WARNING',
                    $title,
                    $summary,
                    ['tripId' => $trip->getId(), 'warnings' => $warnings]
                );
                ++$alerts;
            }
        }

        return [
            'tripChecks' => $tripChecks,
            'alerts' => $alerts,
        ];
    }
}

