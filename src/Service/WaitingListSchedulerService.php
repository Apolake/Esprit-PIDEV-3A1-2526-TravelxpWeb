<?php

namespace App\Service;

use App\Entity\ActivityWaitingListEntry;
use App\Entity\TripWaitingListEntry;
use App\Repository\ActivityWaitingListEntryRepository;
use App\Repository\NotificationRepository;
use App\Repository\TripWaitingListEntryRepository;

class WaitingListSchedulerService
{
    public function __construct(
        private readonly TripWaitingListEntryRepository $tripWaitingListEntryRepository,
        private readonly ActivityWaitingListEntryRepository $activityWaitingListEntryRepository,
        private readonly NotificationService $notificationService,
        private readonly NotificationRepository $notificationRepository,
    ) {
    }

    /**
     * @return array{tripExpired:int, activityExpired:int, cleanedNotifications:int}
     */
    public function runExpirationSweep(\DateTimeImmutable $now): array
    {
        $tripExpired = 0;
        foreach ($this->tripWaitingListEntryRepository->findExpiredWaitingEntriesBatch($now, 300) as $entry) {
            $trip = $entry->getTrip();
            $user = $entry->getUser();

            $entry->setStatus(TripWaitingListEntry::STATUS_EXPIRED);
            ++$tripExpired;

            if ($trip !== null && $user !== null) {
                $this->notificationService->create(
                    $user,
                    'TRIP_WAITLIST_EXPIRED',
                    'Waiting request expired',
                    sprintf('Your waiting request for "%s" has expired.', (string) $trip->getTripName()),
                    ['tripId' => $trip->getId()]
                );
            }
        }

        $activityExpired = 0;
        foreach ($this->activityWaitingListEntryRepository->findExpiredWaitingEntriesBatch($now, 300) as $entry) {
            $activity = $entry->getActivity();
            $user = $entry->getUser();

            $entry->setStatus(ActivityWaitingListEntry::STATUS_EXPIRED);
            ++$activityExpired;

            if ($activity !== null && $user !== null) {
                $this->notificationService->create(
                    $user,
                    'ACTIVITY_WAITLIST_EXPIRED',
                    'Waiting request expired',
                    sprintf('Your waiting request for "%s" has expired.', (string) $activity->getTitle()),
                    ['activityId' => $activity->getId()]
                );
            }
        }

        $cleanedNotifications = $this->notificationRepository->deleteReadOlderThan(
            $now->modify('-90 days')
        );

        return [
            'tripExpired' => $tripExpired,
            'activityExpired' => $activityExpired,
            'cleanedNotifications' => $cleanedNotifications,
        ];
    }
}
