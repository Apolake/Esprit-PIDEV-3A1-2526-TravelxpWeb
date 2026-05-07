<?php

namespace App\Service;

use App\Entity\Trip;
use App\Entity\TripWaitingListEntry;
use App\Entity\User;
use App\Repository\TripWaitingListEntryRepository;
use Doctrine\ORM\EntityManagerInterface;

class TripParticipationService
{
    private const WAITING_TTL_HOURS = 48;
    private const UNAVAILABLE_STATUSES = ['CANCELLED', 'COMPLETED', 'DONE'];

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly TripWaitingListEntryRepository $tripWaitingListEntryRepository,
        private readonly ActivityParticipationService $activityParticipationService,
        private readonly NotificationService $notificationService,
    ) {
    }

    public function joinTrip(User $user, Trip $trip): ParticipationActionResult
    {
        if (in_array($trip->getStatus(), self::UNAVAILABLE_STATUSES, true)) {
            return new ParticipationActionResult('warning_trip_unavailable', 'This trip is no longer available for joining.');
        }

        if ($trip->isParticipant($user)) {
            return new ParticipationActionResult('info_already_joined_trip', 'You are already participating in this trip.');
        }

        $now = new \DateTimeImmutable();
        $this->expireStaleWaitingEntries($trip, $now);

        if ($trip->getAvailableSeats() > 0) {
            $trip->addParticipant($user);
            $trip->recalculateAvailableSeatsFromJoinedCount($trip->getParticipants()->count());
            $this->notificationService->create(
                $user,
                'TRIP_JOINED',
                'Trip joined',
                sprintf('You joined "%s".', (string) $trip->getTripName()),
                ['tripId' => $trip->getId()]
            );
            $this->entityManager->flush();

            return new ParticipationActionResult('success_joined_trip', 'Trip joined successfully.');
        }

        $existing = $this->tripWaitingListEntryRepository->findActiveWaitingEntryForUser($trip, $user);
        if ($existing !== null) {
            return new ParticipationActionResult('info_already_waiting_trip', 'You are already in this trip waiting list.');
        }

        $waitingEntry = (new TripWaitingListEntry())
            ->setTrip($trip)
            ->setUser($user)
            ->setStatus(TripWaitingListEntry::STATUS_WAITING)
            ->setQueuedAt($now)
            ->setExpiresAt($now->modify(sprintf('+%d hours', self::WAITING_TTL_HOURS)));

        $this->entityManager->persist($waitingEntry);
        $this->notificationService->create(
            $user,
            'TRIP_WAITLISTED',
            'Added to waiting list',
            sprintf('You were added to the waiting list for "%s".', (string) $trip->getTripName()),
            ['tripId' => $trip->getId()]
        );
        $this->entityManager->flush();

        return new ParticipationActionResult('warning_waitlisted_trip', 'Trip is full. You were added to the waiting list.');
    }

    public function leaveTrip(User $user, Trip $trip): ParticipationActionResult
    {
        if (!$trip->isParticipant($user)) {
            $waiting = $this->tripWaitingListEntryRepository->findActiveWaitingEntryForUser($trip, $user);
            if ($waiting !== null) {
                $waiting->setStatus(TripWaitingListEntry::STATUS_CANCELLED);
                $this->notificationService->create(
                    $user,
                    'TRIP_WAITLIST_CANCELLED',
                    'Removed from waiting list',
                    sprintf('You were removed from the waiting list for "%s".', (string) $trip->getTripName()),
                    ['tripId' => $trip->getId()]
                );
                $this->entityManager->flush();

                return new ParticipationActionResult('success_left_trip_waiting', 'You were removed from this trip waiting list.');
            }

            return new ParticipationActionResult('info_not_joined_trip', 'You are not participating in this trip.');
        }

        $this->activityParticipationService->removeUserFromTripActivities($user, $trip, false);
        $trip->removeParticipant($user);
        $trip->recalculateAvailableSeatsFromJoinedCount($trip->getParticipants()->count());
        $this->notificationService->create(
            $user,
            'TRIP_LEFT',
            'Trip left',
            sprintf('You left "%s".', (string) $trip->getTripName()),
            ['tripId' => $trip->getId()]
        );
        $this->promoteNextWaitingUsers($trip);
        $this->entityManager->flush();

        return new ParticipationActionResult('success_left_trip', 'Trip left successfully.');
    }

    private function promoteNextWaitingUsers(Trip $trip): void
    {
        $now = new \DateTimeImmutable();
        $this->expireStaleWaitingEntries($trip, $now);

        while ($trip->getAvailableSeats() > 0) {
            $next = $this->tripWaitingListEntryRepository->findNextWaitingEntry($trip, $now);
            if ($next === null) {
                break;
            }

            $candidate = $next->getUser();
            if ($candidate === null || $trip->isParticipant($candidate)) {
                $next->setStatus(TripWaitingListEntry::STATUS_CANCELLED);
                continue;
            }

            $trip->addParticipant($candidate);
            $trip->recalculateAvailableSeatsFromJoinedCount($trip->getParticipants()->count());
            $next->setStatus(TripWaitingListEntry::STATUS_PROMOTED);
            $next->setPromotedAt($now);
            $this->notificationService->create(
                $candidate,
                'TRIP_PROMOTED',
                'Spot available',
                sprintf('A seat became available and you were added to "%s".', (string) $trip->getTripName()),
                ['tripId' => $trip->getId()]
            );
        }
    }

    private function expireStaleWaitingEntries(Trip $trip, \DateTimeImmutable $now): void
    {
        foreach ($this->tripWaitingListEntryRepository->findExpiredWaitingEntries($trip, $now) as $expiredEntry) {
            $expiredEntry->setStatus(TripWaitingListEntry::STATUS_EXPIRED);
            $user = $expiredEntry->getUser();
            if ($user !== null) {
                $this->notificationService->create(
                    $user,
                    'TRIP_WAITLIST_EXPIRED',
                    'Waiting list expired',
                    sprintf('Your waiting request for "%s" has expired.', (string) $trip->getTripName()),
                    ['tripId' => $trip->getId()]
                );
            }
        }
    }
}
