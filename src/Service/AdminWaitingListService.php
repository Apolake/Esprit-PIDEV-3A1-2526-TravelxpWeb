<?php

namespace App\Service;

use App\Entity\ActivityWaitingListEntry;
use App\Entity\TripWaitingListEntry;
use Doctrine\ORM\EntityManagerInterface;

class AdminWaitingListService
{
    private const UNAVAILABLE_STATUSES = ['CANCELLED', 'COMPLETED', 'DONE'];

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly NotificationService $notificationService,
    ) {
    }

    public function acceptTripEntry(TripWaitingListEntry $entry): ParticipationActionResult
    {
        $trip = $entry->getTrip();
        $user = $entry->getUser();

        if ($trip === null || $user === null) {
            $entry->setStatus(TripWaitingListEntry::STATUS_CANCELLED);
            $this->entityManager->flush();

            return new ParticipationActionResult('warning_trip_waiting_invalid', 'Waiting request is invalid and was cancelled.');
        }

        if ($entry->getStatus() !== TripWaitingListEntry::STATUS_WAITING) {
            return new ParticipationActionResult('info_trip_waiting_already_processed', 'This waiting request has already been processed.');
        }

        $now = new \DateTimeImmutable();
        if ($entry->isExpiredAt($now)) {
            $entry->setStatus(TripWaitingListEntry::STATUS_EXPIRED);
            $this->notificationService->create(
                $user,
                'TRIP_WAITLIST_EXPIRED',
                'Waiting request expired',
                sprintf('Your waiting request for "%s" expired before approval.', (string) $trip->getTripName()),
                ['tripId' => $trip->getId()]
            );
            $this->entityManager->flush();

            return new ParticipationActionResult('warning_trip_waiting_expired', 'Waiting request is expired and cannot be accepted.');
        }

        if (in_array($trip->getStatus(), self::UNAVAILABLE_STATUSES, true)) {
            return new ParticipationActionResult('warning_trip_unavailable', 'Trip is not available for approval.');
        }

        if ($trip->isParticipant($user)) {
            $entry->setStatus(TripWaitingListEntry::STATUS_CANCELLED);
            $this->entityManager->flush();

            return new ParticipationActionResult('info_trip_user_already_joined', 'User is already joined in this trip.');
        }

        if ($trip->getAvailableSeats() <= 0) {
            return new ParticipationActionResult('warning_trip_full', 'Trip has no available seats. Approval was not applied.');
        }

        $trip->addParticipant($user);
        $trip->recalculateAvailableSeatsFromJoinedCount($trip->getParticipants()->count());
        $entry->setStatus(TripWaitingListEntry::STATUS_PROMOTED);
        $entry->setPromotedAt($now);

        $this->notificationService->create(
            $user,
            'TRIP_WAITLIST_ACCEPTED',
            'Waiting request accepted',
            sprintf('Your waiting request for "%s" was accepted. You are now joined.', (string) $trip->getTripName()),
            ['tripId' => $trip->getId()]
        );

        $this->entityManager->flush();

        return new ParticipationActionResult('success_trip_waiting_accepted', 'Trip waiting request accepted successfully.');
    }

    public function rejectTripEntry(TripWaitingListEntry $entry): ParticipationActionResult
    {
        $trip = $entry->getTrip();
        $user = $entry->getUser();

        if ($entry->getStatus() !== TripWaitingListEntry::STATUS_WAITING) {
            return new ParticipationActionResult('info_trip_waiting_already_processed', 'This waiting request has already been processed.');
        }

        $entry->setStatus(TripWaitingListEntry::STATUS_REJECTED);

        if ($trip !== null && $user !== null) {
            $this->notificationService->create(
                $user,
                'TRIP_WAITLIST_REJECTED',
                'Waiting request rejected',
                sprintf('Your waiting request for "%s" was rejected by admin review.', (string) $trip->getTripName()),
                ['tripId' => $trip->getId()]
            );
        }

        $this->entityManager->flush();

        return new ParticipationActionResult('success_trip_waiting_rejected', 'Trip waiting request rejected.');
    }

    public function acceptActivityEntry(ActivityWaitingListEntry $entry): ParticipationActionResult
    {
        $activity = $entry->getActivity();
        $user = $entry->getUser();

        if ($activity === null || $user === null) {
            $entry->setStatus(ActivityWaitingListEntry::STATUS_CANCELLED);
            $this->entityManager->flush();

            return new ParticipationActionResult('warning_activity_waiting_invalid', 'Waiting request is invalid and was cancelled.');
        }

        if ($entry->getStatus() !== ActivityWaitingListEntry::STATUS_WAITING) {
            return new ParticipationActionResult('info_activity_waiting_already_processed', 'This waiting request has already been processed.');
        }

        $now = new \DateTimeImmutable();
        if ($entry->isExpiredAt($now)) {
            $entry->setStatus(ActivityWaitingListEntry::STATUS_EXPIRED);
            $this->notificationService->create(
                $user,
                'ACTIVITY_WAITLIST_EXPIRED',
                'Waiting request expired',
                sprintf('Your waiting request for "%s" expired before approval.', (string) $activity->getTitle()),
                ['activityId' => $activity->getId()]
            );
            $this->entityManager->flush();

            return new ParticipationActionResult('warning_activity_waiting_expired', 'Waiting request is expired and cannot be accepted.');
        }

        if (in_array($activity->getStatus(), self::UNAVAILABLE_STATUSES, true)) {
            return new ParticipationActionResult('warning_activity_unavailable', 'Activity is not available for approval.');
        }

        if ($activity->isParticipant($user)) {
            $entry->setStatus(ActivityWaitingListEntry::STATUS_CANCELLED);
            $this->entityManager->flush();

            return new ParticipationActionResult('info_activity_user_already_joined', 'User is already joined in this activity.');
        }

        $trip = $activity->getTrip();
        if ($trip !== null && !$trip->isParticipant($user)) {
            return new ParticipationActionResult('warning_trip_required', 'User must be joined to the related trip before activity approval.');
        }

        if ($activity->getAvailableSeats() <= 0) {
            return new ParticipationActionResult('warning_activity_full', 'Activity has no available seats. Approval was not applied.');
        }

        $activity->addParticipant($user);
        $activity->recalculateAvailableSeatsFromJoinedCount($activity->getParticipants()->count());
        $entry->setStatus(ActivityWaitingListEntry::STATUS_PROMOTED);
        $entry->setPromotedAt($now);

        $this->notificationService->create(
            $user,
            'ACTIVITY_WAITLIST_ACCEPTED',
            'Waiting request accepted',
            sprintf('Your waiting request for "%s" was accepted. You are now joined.', (string) $activity->getTitle()),
            ['activityId' => $activity->getId()]
        );

        $this->entityManager->flush();

        return new ParticipationActionResult('success_activity_waiting_accepted', 'Activity waiting request accepted successfully.');
    }

    public function rejectActivityEntry(ActivityWaitingListEntry $entry): ParticipationActionResult
    {
        $activity = $entry->getActivity();
        $user = $entry->getUser();

        if ($entry->getStatus() !== ActivityWaitingListEntry::STATUS_WAITING) {
            return new ParticipationActionResult('info_activity_waiting_already_processed', 'This waiting request has already been processed.');
        }

        $entry->setStatus(ActivityWaitingListEntry::STATUS_REJECTED);

        if ($activity !== null && $user !== null) {
            $this->notificationService->create(
                $user,
                'ACTIVITY_WAITLIST_REJECTED',
                'Waiting request rejected',
                sprintf('Your waiting request for "%s" was rejected by admin review.', (string) $activity->getTitle()),
                ['activityId' => $activity->getId()]
            );
        }

        $this->entityManager->flush();

        return new ParticipationActionResult('success_activity_waiting_rejected', 'Activity waiting request rejected.');
    }
}

