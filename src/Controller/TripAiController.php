<?php

namespace App\Controller;

use App\Entity\Activity;
use App\Entity\Trip;
use App\Entity\User;
use App\Repository\ActivityRepository;
use App\Repository\TripRepository;
use App\Service\TripAiAssistantService;
use App\Service\TripWeatherService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class TripAiController extends AbstractController
{
    #[Route('/admin/ai/trips/generate', name: 'admin_trip_ai_generate', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function generateAdminInsight(
        Request $request,
        TripAiAssistantService $tripAiAssistantService,
        TripRepository $tripRepository,
        ActivityRepository $activityRepository,
        TripWeatherService $tripWeatherService,
    ): JsonResponse {
        if (\function_exists('set_time_limit')) {
            @set_time_limit(0);
        }

        $payload = $this->decodeJsonPayload($request);
        $token = (string) ($payload['token'] ?? '');
        if (!$this->isCsrfTokenValid('trip_ai_admin_tools', $token)) {
            return $this->json(['error' => 'Invalid CSRF token.'], 400);
        }

        $tool = (string) ($payload['tool'] ?? '');
        $contextInput = is_array($payload['context'] ?? null) ? $payload['context'] : [];
        $tripId = isset($payload['tripId']) ? (int) $payload['tripId'] : 0;

        $trip = $tripId > 0 ? $tripRepository->find($tripId) : null;
        $activities = $trip ? $activityRepository->findBy(['trip' => $trip], ['activityDate' => 'ASC', 'startTime' => 'ASC']) : [];
        $weather = $trip ? $tripWeatherService->fetchForTrip($trip) : null;
        $context = $this->buildTripContext($trip, $contextInput, $activities, $weather, null);

        $result = $tripAiAssistantService->generateAdminInsight($context, $tool);

        return $this->json([
            'title' => $result['title'],
            'content' => $result['content'],
            'tool' => $tool,
        ]);
    }

    #[Route('/trips/{id}/ai-assistant', name: 'trip_ai_assistant', requirements: ['id' => '\d+'], methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function answerUserQuestion(
        Request $request,
        Trip $trip,
        TripAiAssistantService $tripAiAssistantService,
        ActivityRepository $activityRepository,
        TripWeatherService $tripWeatherService,
    ): JsonResponse {
        if (\function_exists('set_time_limit')) {
            @set_time_limit(0);
        }

        $payload = $this->decodeJsonPayload($request);
        $token = (string) ($payload['token'] ?? '');
        if (!$this->isCsrfTokenValid('trip_ai_user_' . $trip->getId(), $token)) {
            return $this->json(['error' => 'Invalid CSRF token.'], 400);
        }

        $questionKey = (string) ($payload['questionKey'] ?? '');
        $message = trim((string) ($payload['message'] ?? ''));
        $requestTripId = isset($payload['tripId']) ? (int) $payload['tripId'] : 0;
        if ($requestTripId > 0 && $requestTripId !== $trip->getId()) {
            return $this->json(['error' => 'Trip context mismatch. Please reopen AI from the selected trip card.'], 400);
        }

        if (!$tripAiAssistantService->hasConfiguredLiveProvider()) {
            return $this->json([
                'error' => 'AI provider is not configured. Set GEMINI_API_KEY (or GOOGLE_API_KEY) and try again.',
                'code' => 'ai_not_configured',
            ], 503);
        }

        $history = $this->normalizeChatHistory($payload['history'] ?? null);
        $activities = $activityRepository->findBy(['trip' => $trip], ['activityDate' => 'ASC', 'startTime' => 'ASC']);
        $weather = $tripWeatherService->fetchForTrip($trip);
        $viewer = $this->getUser();
        $currentUser = $viewer instanceof User ? $viewer : null;
        $context = $this->buildTripContext($trip, [], $activities, $weather, $currentUser);

        if ($message !== '') {
            $answer = $tripAiAssistantService->answerUserFreeMessageLive($context, $message, $history);
        } else {
            $answer = $tripAiAssistantService->answerUserPresetQuestionLive($context, $questionKey);
        }

        if ($answer === null) {
            $providerError = $tripAiAssistantService->getLastProviderError();
            return $this->json([
                'error' => $providerError !== null && $providerError !== ''
                    ? ('AI provider request failed: ' . $providerError)
                    : 'AI provider request failed. Please try again in a moment.',
                'code' => 'ai_provider_error',
            ], 502);
        }

        return $this->json([
            'question' => $answer['question'],
            'answer' => $answer['answer'],
            'questionKey' => $questionKey,
            'tripId' => $trip->getId(),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function decodeJsonPayload(Request $request): array
    {
        try {
            $decoded = json_decode((string) $request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\Throwable) {
            return [];
        }

        return is_array($decoded) ? $decoded : [];
    }

    /**
     * @param list<Activity> $activities
     * @param array<string, mixed> $contextInput
     * @param array<string, mixed>|null $weather
     * @return array<string, mixed>
     */
    private function buildTripContext(
        ?Trip $trip,
        array $contextInput,
        array $activities,
        ?array $weather,
        ?User $currentUser,
    ): array {
        $tripName = $this->pickString($contextInput, 'tripName', $trip?->getTripName());
        $origin = $this->pickString($contextInput, 'origin', $trip?->getOrigin());
        $destination = $this->pickString($contextInput, 'destination', $trip?->getDestination());
        $status = $this->pickString($contextInput, 'status', $trip?->getStatus());
        $description = $this->pickString($contextInput, 'description', $trip?->getDescription());
        $notes = $this->pickString($contextInput, 'notes', $trip?->getNotes());
        $currency = $this->pickString($contextInput, 'currency', $trip?->getCurrency() ?? 'USD');

        $startDate = $this->pickString($contextInput, 'startDate', $trip?->getStartDate()?->format('Y-m-d'));
        $endDate = $this->pickString($contextInput, 'endDate', $trip?->getEndDate()?->format('Y-m-d'));
        $dateRange = trim(($startDate !== '' ? $startDate : 'TBD') . ' -> ' . ($endDate !== '' ? $endDate : 'TBD'));

        $budgetAmount = $this->pickFloat($contextInput, 'budgetAmount', $trip?->getBudgetAmount() ?? 0.0);
        $participantsCount = $trip ? $trip->getParticipants()->count() : 0;
        $totalCapacity = $trip ? $trip->getTotalCapacity() : (int) ($contextInput['totalCapacity'] ?? 0);
        $availableSeats = $trip ? $trip->getAvailableSeats() : (int) ($contextInput['availableSeats'] ?? 0);

        $durationDays = 1;
        if ($startDate !== '' && $endDate !== '') {
            try {
                $start = new \DateTimeImmutable($startDate);
                $end = new \DateTimeImmutable($endDate);
                $durationDays = max(1, (int) $start->diff($end)->days + 1);
            } catch (\Throwable) {
                $durationDays = 1;
            }
        }

        $weatherWarnings = [];
        if (is_array($weather['warnings'] ?? null)) {
            foreach ($weather['warnings'] as $warning) {
                if (is_string($warning) && trim($warning) !== '') {
                    $weatherWarnings[] = trim($warning);
                }
            }
        }

        $activityRows = [];
        $maxActivitiesInAiContext = 12;
        $activityIndex = 0;
        foreach ($activities as $activity) {
            if ($activityIndex >= $maxActivitiesInAiContext) {
                break;
            }
            if (!$activity instanceof Activity) {
                continue;
            }
            $activityRows[] = [
                'title' => (string) $activity->getTitle(),
                'type' => (string) ($activity->getType() ?: 'General'),
                'status' => (string) $activity->getStatus(),
                'date' => $activity->getActivityDate()?->format('Y-m-d'),
                'startTime' => $activity->getStartTime()?->format('H:i'),
                'endTime' => $activity->getEndTime()?->format('H:i'),
                'cost' => (float) ($activity->getCostAmount() ?? 0.0),
                'currency' => (string) $activity->getCurrency(),
            ];
            ++$activityIndex;
        }

        $month = '';
        if ($startDate !== '') {
            try {
                $month = (new \DateTimeImmutable($startDate))->format('F');
            } catch (\Throwable) {
                $month = '';
            }
        }
        $seasonHint = $month !== '' ? "Likely {$month} period conditions." : 'Season is not fully specified.';

        return [
            'tripId' => $trip?->getId(),
            'tripName' => $tripName,
            'origin' => $origin,
            'destination' => $destination,
            'dateRange' => $dateRange,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'durationDays' => $durationDays,
            'status' => $status,
            'budgetAmount' => $budgetAmount,
            'currency' => $currency,
            'description' => $description,
            'notes' => $notes,
            'participantsCount' => $participantsCount,
            'totalCapacity' => $totalCapacity,
            'availableSeats' => $availableSeats,
            'activities' => $activityRows,
            'activitiesTotal' => count($activities),
            'activitiesTruncated' => count($activities) > $maxActivitiesInAiContext,
            'weatherWarnings' => $weatherWarnings,
            'seasonHint' => $seasonHint,
            'userJoinedTrip' => $trip && $currentUser ? $trip->isParticipant($currentUser) : false,
        ];
    }

    /**
     * @param array<string, mixed> $context
     */
    private function pickString(array $context, string $key, ?string $fallback): string
    {
        $value = $context[$key] ?? null;
        if (is_string($value)) {
            return trim($value);
        }

        return trim((string) ($fallback ?? ''));
    }

    /**
     * @param array<string, mixed> $context
     */
    private function pickFloat(array $context, string $key, float $fallback): float
    {
        $value = $context[$key] ?? null;
        if (is_numeric($value)) {
            return (float) $value;
        }

        return $fallback;
    }

    /**
     * @return list<array{role: string, content: string}>
     */
    private function normalizeChatHistory(mixed $rawHistory): array
    {
        if (!is_array($rawHistory)) {
            return [];
        }

        $history = [];
        foreach (array_slice($rawHistory, -12) as $turn) {
            if (!is_array($turn)) {
                continue;
            }
            $role = strtolower(trim((string) ($turn['role'] ?? '')));
            $content = trim((string) ($turn['content'] ?? ''));
            if ($content === '' || !in_array($role, ['user', 'assistant'], true)) {
                continue;
            }
            $history[] = [
                'role' => $role,
                'content' => $content,
            ];
        }

        return $history;
    }
}
