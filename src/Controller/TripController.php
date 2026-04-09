<?php

namespace App\Controller;

use App\Entity\Trip;
use App\Form\TripType;
use App\Repository\TripRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/trip')]
final class TripController extends AbstractController
{
    #[Route(name: 'app_trip_index', methods: ['GET'])]
    public function index(TripRepository $tripRepository): Response
    {
        return $this->render('trip/index.html.twig', [
            'trips' => $tripRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_trip_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $trip = new Trip();
        $form = $this->createForm(TripType::class, $trip, [
            'user_choices' => $this->getUserChoices($entityManager),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->validateTripBusinessRules($trip, $form, $entityManager)) {
                $entityManager->persist($trip);
                $entityManager->flush();

                return $this->redirectToRoute('app_trip_index', [], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->render('trip/new.html.twig', [
            'trip' => $trip,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_trip_show', methods: ['GET'])]
    public function show(?Trip $trip): Response
    {
        if ($trip === null) {
            return $this->redirectToRoute('app_trip_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('trip/show.html.twig', [
            'trip' => $trip,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_trip_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ?Trip $trip, EntityManagerInterface $entityManager): Response
    {
        if ($trip === null) {
            return $this->redirectToRoute('app_trip_index', [], Response::HTTP_SEE_OTHER);
        }

        $form = $this->createForm(TripType::class, $trip, [
            'user_choices' => $this->getUserChoices($entityManager),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->validateTripBusinessRules($trip, $form, $entityManager)) {
                $entityManager->flush();

                return $this->redirectToRoute('app_trip_index', [], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->render('trip/edit.html.twig', [
            'trip' => $trip,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_trip_delete', methods: ['POST'])]
    public function delete(Request $request, ?Trip $trip, EntityManagerInterface $entityManager): Response
    {
        if ($trip === null) {
            return $this->redirectToRoute('app_trip_index', [], Response::HTTP_SEE_OTHER);
        }

        if ($this->isCsrfTokenValid('delete'.$trip->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($trip);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_trip_index', [], Response::HTTP_SEE_OTHER);
    }

    private function isValidUserId(?int $userId, EntityManagerInterface $entityManager): bool
    {
        if ($userId === null) {
            return true;
        }

        $exists = $entityManager->getConnection()->fetchOne(
            'SELECT 1 FROM users WHERE id = :id LIMIT 1',
            ['id' => $userId]
        );

        return $exists !== false;
    }

    private function getUserChoices(EntityManagerInterface $entityManager): array
    {
        $rows = $entityManager->getConnection()->fetchAllAssociative(
            'SELECT id, username, email FROM users ORDER BY username ASC'
        );

        $choices = [];
        foreach ($rows as $row) {
            $id = (int) $row['id'];
            $label = sprintf('%s (%s) [#%d]', $row['username'], $row['email'], $id);
            $choices[$label] = $id;
        }

        return $choices;
    }

    private function validateTripBusinessRules(Trip $trip, FormInterface $form, EntityManagerInterface $entityManager): bool
    {
        $isValid = true;

        if (!$this->isValidUserId($trip->getUserId(), $entityManager)) {
            $form->get('userId')->addError(new FormError('Selected user does not exist.'));
            $isValid = false;
        }

        if (!$this->isValidParentTripId($trip, $entityManager)) {
            $form->get('parentId')->addError(new FormError('Parent trip must reference an existing trip and cannot be the same as this trip.'));
            $isValid = false;
        }

        if ($this->isDuplicateTrip($trip, $entityManager)) {
            $form->addError(new FormError('A trip with the same key details already exists.'));
            $isValid = false;
        }

        return $isValid;
    }

    private function isValidParentTripId(Trip $trip, EntityManagerInterface $entityManager): bool
    {
        $parentId = $trip->getParentId();
        if ($parentId === null) {
            return true;
        }

        if ($trip->getId() !== null && $trip->getId() === $parentId) {
            return false;
        }

        $exists = $entityManager->getConnection()->fetchOne(
            'SELECT 1 FROM trips WHERE id = :id LIMIT 1',
            ['id' => $parentId]
        );

        return $exists !== false;
    }

    private function isDuplicateTrip(Trip $trip, EntityManagerInterface $entityManager): bool
    {
        $tripName = mb_strtolower(trim((string) $trip->getTripName()));
        $origin = mb_strtolower(trim((string) ($trip->getOrigin() ?? '')));
        $destination = mb_strtolower(trim((string) ($trip->getDestination() ?? '')));
        $userId = $trip->getUserId();
        $startDate = $trip->getStartDate()?->format('Y-m-d');
        $endDate = $trip->getEndDate()?->format('Y-m-d');

        if ($tripName === '' || $startDate === null || $endDate === null) {
            return false;
        }

        $duplicateId = $entityManager->getConnection()->fetchOne(
            'SELECT id
             FROM trips
             WHERE LOWER(TRIM(trip_name)) = :trip_name
               AND COALESCE(LOWER(TRIM(origin)), \'\') = :origin
               AND COALESCE(LOWER(TRIM(destination)), \'\') = :destination
               AND COALESCE(user_id, -1) = :user_id
               AND start_date = :start_date
               AND end_date = :end_date
               AND (:current_id IS NULL OR id <> :current_id)
             LIMIT 1',
            [
                'trip_name' => $tripName,
                'origin' => $origin,
                'destination' => $destination,
                'user_id' => $userId ?? -1,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'current_id' => $trip->getId(),
            ]
        );

        return $duplicateId !== false;
    }
}
