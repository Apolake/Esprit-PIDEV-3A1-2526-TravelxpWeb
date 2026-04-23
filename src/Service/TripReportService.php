<?php

namespace App\Service;

use App\Entity\Trip;
use App\Repository\ActivityRepository;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class TripReportService
{
    public function __construct(
        private readonly ActivityRepository $activityRepository,
        private readonly TripWeatherService $tripWeatherService,
        private readonly CurrencyConverterService $currencyConverterService,
        private readonly TripQrCodeService $tripQrCodeService,
        private readonly UrlGeneratorInterface $urlGenerator,
    ) {
    }

    /**
     * @param array<string, mixed> $options
     * @return array<string, mixed>
     */
    public function buildTripReport(Trip $trip, ?string $displayCurrency = null, array $options = []): array
    {
        $targetCurrency = $this->currencyConverterService->normalizeCurrency($displayCurrency ?: $trip->getCurrency());
        $weather = $this->tripWeatherService->fetchForTrip($trip);
        $activities = $this->activityRepository->findBy(
            ['trip' => $trip],
            ['activityDate' => 'ASC', 'startTime' => 'ASC']
        );

        $activityRows = [];
        $totalActivityCostBase = 0.0;
        $totalActivityCostConverted = 0.0;
        foreach ($activities as $activity) {
            if (!$activity instanceof \App\Entity\Activity) {
                continue;
            }

            $baseAmount = (float) ($activity->getCostAmount() ?? 0.0);
            $convertedAmount = $this->currencyConverterService->convert($baseAmount, $activity->getCurrency(), $targetCurrency);
            $totalActivityCostBase += $baseAmount;
            $totalActivityCostConverted += $convertedAmount;

            $activityRows[] = [
                'id' => $activity->getId(),
                'title' => (string) $activity->getTitle(),
                'type' => (string) ($activity->getType() ?: 'General'),
                'status' => (string) $activity->getStatus(),
                'date' => $activity->getActivityDate()?->format('Y-m-d'),
                'startTime' => $activity->getStartTime()?->format('H:i'),
                'endTime' => $activity->getEndTime()?->format('H:i'),
                'locationName' => $activity->getLocationName(),
                'baseCurrency' => $activity->getCurrency(),
                'baseCost' => $baseAmount,
                'convertedCurrency' => $targetCurrency,
                'convertedCost' => $convertedAmount,
            ];
        }

        $budgetBase = (float) ($trip->getBudgetAmount() ?? 0.0);
        $budgetConverted = $this->currencyConverterService->convert($budgetBase, $trip->getCurrency(), $targetCurrency);
        $expensesBase = (float) ($trip->getTotalExpenses() ?? 0.0);
        $expensesConverted = $this->currencyConverterService->convert($expensesBase, $trip->getCurrency(), $targetCurrency);

        $tripUrl = $this->urlGenerator->generate('trip_show', ['id' => $trip->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        $qrDataUri = null;
        try {
            $qrDataUri = $this->tripQrCodeService->buildTripQrDataUri($trip, $tripUrl);
        } catch (\Throwable) {
            // Keep report/PDF generation alive even if QR generation fails.
        }
        $aiSummary = trim((string) ($options['aiSummary'] ?? ''));

        return [
            'trip' => $trip,
            'tripUrl' => $tripUrl,
            'generatedAt' => new \DateTimeImmutable(),
            'displayCurrency' => $targetCurrency,
            'supportedCurrencies' => $this->currencyConverterService->getSupportedCurrenciesWithLabels(),
            'budgetBase' => $budgetBase,
            'budgetBaseFormatted' => $this->currencyConverterService->formatAmount($budgetBase, $trip->getCurrency()),
            'budgetConverted' => $budgetConverted,
            'budgetConvertedFormatted' => $this->currencyConverterService->formatAmount($budgetConverted, $targetCurrency),
            'expensesBase' => $expensesBase,
            'expensesBaseFormatted' => $this->currencyConverterService->formatAmount($expensesBase, $trip->getCurrency()),
            'expensesConverted' => $expensesConverted,
            'expensesConvertedFormatted' => $this->currencyConverterService->formatAmount($expensesConverted, $targetCurrency),
            'totalActivityCostBase' => $totalActivityCostBase,
            'totalActivityCostBaseFormatted' => $this->currencyConverterService->formatAmount($totalActivityCostBase, $trip->getCurrency()),
            'totalActivityCostConverted' => $totalActivityCostConverted,
            'totalActivityCostConvertedFormatted' => $this->currencyConverterService->formatAmount($totalActivityCostConverted, $targetCurrency),
            'activities' => $activityRows,
            'weather' => $weather,
            'weatherWarnings' => is_array($weather['warnings'] ?? null) ? $weather['warnings'] : [],
            'qrDataUri' => $qrDataUri,
            'aiSummary' => $aiSummary,
        ];
    }
}
