<?php

namespace App\Controller;

use App\Entity\Activity;
use App\Form\ActivityType;
use App\Repository\ActivityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/activity')]
final class ActivityController extends AbstractController
{
    #[Route(name: 'app_activity_index', methods: ['GET'])]
    public function index(ActivityRepository $activityRepository): Response
    {
        return $this->render('activity/index.html.twig', [
            'activities' => $activityRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_activity_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $activity = new Activity();
        $form = $this->createForm(ActivityType::class, $activity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->validateActivityBusinessRules($activity, $form, $entityManager)) {
                $entityManager->persist($activity);
                $entityManager->flush();

                return $this->redirectToRoute('app_activity_index', [], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->render('activity/new.html.twig', [
            'activity' => $activity,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_activity_show', methods: ['GET'])]
    public function show(?Activity $activity): Response
    {
        if ($activity === null) {
            return $this->redirectToRoute('app_activity_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('activity/show.html.twig', [
            'activity' => $activity,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_activity_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ?Activity $activity, EntityManagerInterface $entityManager): Response
    {
        if ($activity === null) {
            return $this->redirectToRoute('app_activity_index', [], Response::HTTP_SEE_OTHER);
        }

        $form = $this->createForm(ActivityType::class, $activity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->validateActivityBusinessRules($activity, $form, $entityManager)) {
                $entityManager->flush();

                return $this->redirectToRoute('app_activity_index', [], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->render('activity/edit.html.twig', [
            'activity' => $activity,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_activity_delete', methods: ['POST'])]
    public function delete(Request $request, ?Activity $activity, EntityManagerInterface $entityManager): Response
    {
        if ($activity === null) {
            return $this->redirectToRoute('app_activity_index', [], Response::HTTP_SEE_OTHER);
        }

        if ($this->isCsrfTokenValid('delete'.$activity->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($activity);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_activity_index', [], Response::HTTP_SEE_OTHER);
    }

    private function validateActivityBusinessRules(Activity $activity, FormInterface $form, EntityManagerInterface $entityManager): bool
    {
        if ($this->isDuplicateActivity($activity, $entityManager)) {
            $form->addError(new FormError('A similar activity already exists for the selected trip/date/time.'));

            return false;
        }

        return true;
    }

    private function isDuplicateActivity(Activity $activity, EntityManagerInterface $entityManager): bool
    {
        $title = mb_strtolower(trim((string) $activity->getTitle()));
        $type = mb_strtolower(trim((string) ($activity->getType() ?? '')));
        $tripId = $activity->getTrip()?->getId();
        $activityDate = $activity->getActivityDate()?->format('Y-m-d');
        $startTime = $activity->getStartTime()?->format('H:i:s');
        $endTime = $activity->getEndTime()?->format('H:i:s');

        if ($title === '') {
            return false;
        }

        $duplicateId = $entityManager->getConnection()->fetchOne(
            'SELECT id
             FROM activities
             WHERE LOWER(TRIM(title)) = :title
               AND COALESCE(LOWER(TRIM(type)), \'\') = :type
               AND COALESCE(trip_id, -1) = :trip_id
               AND COALESCE(activity_date, \'1000-01-01\') = COALESCE(:activity_date, \'1000-01-01\')
               AND COALESCE(start_time, \'00:00:00\') = COALESCE(:start_time, \'00:00:00\')
               AND COALESCE(end_time, \'00:00:00\') = COALESCE(:end_time, \'00:00:00\')
               AND (:current_id IS NULL OR id <> :current_id)
             LIMIT 1',
            [
                'title' => $title,
                'type' => $type,
                'trip_id' => $tripId ?? -1,
                'activity_date' => $activityDate,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'current_id' => $activity->getId(),
            ]
        );

        return $duplicateId !== false;
    }
}
