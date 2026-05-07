<?php

namespace App\Service;

use App\Entity\Activity;

class ActivityLocationService
{
    public function __construct(private readonly GeocodingService $geocodingService)
    {
    }

    public function syncActivityCoordinates(Activity $activity): void
    {
        $locationName = trim((string) $activity->getLocationName());
        if ($locationName !== '') {
            $resolved = $this->geocodingService->geocode($locationName);
            if ($resolved !== null) {
                $activity
                    ->setLocationLatitude($resolved['lat'])
                    ->setLocationLongitude($resolved['lng']);

                return;
            }
        }

        $trip = $activity->getTrip();
        if ($trip === null) {
            return;
        }

        if ($activity->getLocationLatitude() === null && $trip->getDestinationLatitude() !== null) {
            $activity->setLocationLatitude($trip->getDestinationLatitude());
        }
        if ($activity->getLocationLongitude() === null && $trip->getDestinationLongitude() !== null) {
            $activity->setLocationLongitude($trip->getDestinationLongitude());
        }
    }
}
