<?php

namespace App\Service;

use App\Entity\Trip;

class TripLocationService
{
    public function __construct(private readonly GeocodingService $geocodingService)
    {
    }

    public function syncTripCoordinates(Trip $trip): void
    {
        $destination = trim((string) $trip->getDestination());
        if ($destination === '') {
            return;
        }

        $resolved = $this->geocodingService->geocode($destination);
        if ($resolved === null) {
            return;
        }

        $trip
            ->setDestinationLatitude($resolved['lat'])
            ->setDestinationLongitude($resolved['lng']);
    }
}
