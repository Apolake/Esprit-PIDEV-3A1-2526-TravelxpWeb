<?php

namespace App\Service;

use App\Entity\Activity;
use App\Entity\ActivityWaitingListEntry;
use App\Entity\Trip;
use App\Entity\User;
use App\Repository\ActivityWaitingListEntryRepository;
use Doctrine\ORM\EntityManagerInterface;

class ActivityParticipationService
{
    private const WAITING_TTL_HOURS = 48;
    private const UNAVAILABLE_STATUSES = ['CANCELLED', 'COMPLETED', 'DONE'];

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ActivityWaitingListEntryRepository $activityWaitingListEntryRepository,
        private readonly NotificationService $notificationService,
    ) {
    }

    public function joinActivity(User $user, Activity $activity): ParticipationActionResult
    {
        if ($activity->getTrip() === null) {
            return new ParticipationActionResult('warning_invalid_activity', 'This activity is not linked to any trip.');
        }

        if (!$activity->getTrip()->isParticipant($user)) {
            return new ParticipationActionResult('warning_trip_required', 'Join the related trip first.');
        }

        if (in_array($activity->getStatus(), self::UNAVAILABLE_STATUSES, true)) {
            return new ParticipationActionResult('warning_activity_unavailable', 'This activity is no longer available for joining.');
        }

        if ($activity->isParticipant($user)) {
            return new ParticipationActionResult('info_already_joined_activity', 'You are already participating in this activity.');
        }

        $now = new \DateTimeImmutable();
        $this->expireStaleWaitingEntries($activity, $now);

        if ($activity->getAvailableSeats() > 0) {
            $activity->addParticipant($user);
            $activity->recalculateAvailableSeatsFromJoinedCount($activity->getParticipants()->count());
            $this->notificationService->create(
                $user,
                'ACTIVITY_JOINED',
                'Activity joined',
                sprintf('You joined "%s".', (string) $activity->getTitle()),
                ['activityId' => $activity->getId()]
            );
            $this->entityManager->flush();

            return new ParticipationActionResult('success_joined_activity', 'Activity added successfully.');
        }

        $existing = $this->activityWaitingListEntryRepository->findActiveWaitingEntryForUser($activity, $user);
        if ($existing !== null) {
            return new ParticipationActionResult('info_already_waiting_activity', 'You are already in this activity waiting list.');
        }

        $waitingEntry = (new ActivityWaitingListEntry())
            ->setActivity($activity)
            ->setUser($user)
            ->setStatus(ActivityWaitingListEntry::STATUS_WAITING)
            ->setQueuedAt($now)
            ->setExpiresAt($now->modify(sprintf('+%d hours', self::WAITING_TTL_HOURS)));

        $this->entityManager->persist($waitingEntry);
        $this->notificationService->create(
            $user,
            'ACTIVITY_WAITLISTED',
            'Added to waiting list',
            sprintf('You were added to the waiting list for "%s".', (string) $activity->getTitle()),
            ['activityId' => $activity->getId()]
        );
        $this->entityManager->flush();

        return new ParticipationActionResult('warning_waitlisted_activity', 'Activity is full. You were added to the waiting list.');
    }

    public function leaveActivity(User $user, Activity $activity): ParticipationActionResult
    {
        $left = $this->removeUserFromActivity($user, $activity, true);
        if ($left) {
            return new ParticipationActionResult('success_left_activity', 'Activity removed successfully.');
        }

        $waiting = $this->activityWaitingListEntryRepository->findActiveWaitingEntryForUser($activity, $user);
        if ($waiting !== null) {
            $waiting->setStatus(ActivityWaitingListEntry::STATUS_CANCELLED);
            $this->notificationService->create(
                $user,
                'ACTIVITY_WAITLIST_CANCELLED',
                'Removed from waiting list',
                sprintf('You were removed from the waiting list for "%s".', (string) $activity->getTitle()),
                ['activityId' => $activity->getId()]
            );
            $this->entityManager->flush();

            return new ParticipationActionResult('success_left_activity_waiting', 'You were removed from this activity waiting list.');
        }

        return new ParticipationActionResult('info_not_joined_activity', 'You are not participating in this activity.');
    }

    public function removeUserFromTripActivities(User $user, Trip $trip, bool $flush = true): void
    {
        foreach ($trip->getActivities() as $activity) {
            $this->removeUserFromActivity($user, $activity, false);
        }

        foreach ($this->activityWaitingListEntryRepository->findActiveWaitingEntriesForTripAndUser($trip, $user) as $waitingEntry) {
            $waitingEntry->setStatus(ActivityWaitingListEntry::STATUS_CANCELLED);
        }

        if ($flush) {
            $this->entityManager->flush();
        }
    }

    private function removeUserFromActivity(User $user, Activity $activity, bool $flush): bool
    {
        if (!$activity->isParticipant($user)) {
            return false;
        }

        $activity->removeParticipant($user);
        $activity->recalculateAvailableSeatsFromJoinedCount($activity->getParticipants()->count());
        $this->notificationService->create(
            $user,
            'ACTIVITY_LEFT',
            'Activity left',
            sprintf('You left "%s".', (string) $activity->getTitle()),
            ['activityId' => $activity->getId()]
        );
        $this->promoteNextWaitingUser($activity);

        if ($flush) {
            $this->entityManager->flush();
        }

        return true;
    }

    private function promoteNextWaitingUser(Activity $activity): void
    {
        $now = new \DateTimeImmutable();
        $this->expireStaleWaitingEntries($activity, $now);

        while ($activity->getAvailableSeats() > 0) {
            $next = $this->activityWaitingListEntryRepository->findNextWaitingEntry($activity, $now);
            if ($next === null) {
                break;
            }

            $candidate = $next->getUser();
            if ($candidate === null || $activity->isParticipant($candidate)) {
                $next->setStatus(ActivityWaitingListEntry::STATUS_CANCELLED);
                continue;
            }
            $trip = $activity->getTrip();
            if ($trip !== null && !$trip->isParticipant($candidate)) {
                $next->setStatus(ActivityWaitingListEntry::STATUS_CANCELLED);
                continue;
            }

            $activity->addParticipant($candidate);
            $activity->recalculateAvailableSeatsFromJoinedCount($activity->getParticipants()->count());
            $next->setStatus(ActivityWaitingListEntry::STATUS_PROMOTED);
            $next->setPromotedAt($now);
            $this->notificationService->create(
                $candidate,
                'ACTIVITY_PROMOTED',
                'Spot available',
                sprintf('A seat became available and you were added to "%s".', (string) $activity->getTitle()),
                ['activityId' => $activity->getId()]
            );
        }
    }

    private function expireStaleWaitingEntries(Activity $activity, \DateTimeImmutable $now): void
    {
        foreach ($this->activityWaitingListEntryRepository->findExpiredWaitingEntries($activity, $now) as $expiredEntry) {
            $expiredEntry->setStatus(ActivityWaitingListEntry::STATUS_EXPIRED);
            $user = $expiredEntry->getUser();
            if ($user !== null) {
                $this->notificationService->create(
                    $user,
                    'ACTIVITY_WAITLIST_EXPIRED',
                    'Waiting list expired',
                    sprintf('Your waiting request for "%s" has expired.', (string) $activity->getTitle()),
                    ['activityId' => $activity->getId()]
                );
            }
        }
    }
}
