<?php

namespace App\Service;

use App\Entity\Booking;

class BookingPricingService
{
    /**
     * @return array<string, mixed>
     */
    public function buildPricingSnapshot(Booking $booking): array
    {
        $property = $booking->getProperty();
        $bookingDate = $booking->getBookingDate();
        $duration = max(1, (int) ($booking->getDuration() ?? 1));
        $nightlyRate = $property === null ? 0.0 : (float) $property->getPricePerNight();

        $seasonalAdjustment = $this->resolveSeasonalAdjustment($bookingDate);
        $timingAdjustment = $this->resolveTimingAdjustment($bookingDate, $duration);
        $dynamicNightlyRate = $nightlyRate * $seasonalAdjustment['multiplier'] * $timingAdjustment['multiplier'];
        $lodgingSubtotal = $dynamicNightlyRate * $duration;
        $offerDiscountPercent = $this->resolveActiveDiscountPercent($booking);
        $offerDiscountAmount = $lodgingSubtotal * ($offerDiscountPercent / 100);

        $serviceTotal = 0.0;
        $serviceLabels = [];
        foreach ($booking->getServices() as $service) {
            $serviceTotal += (float) $service->getPrice();
            $serviceLabels[] = sprintf('%s (%s)', (string) $service->getProviderName(), (string) $service->getServiceType());
        }

        $total = max(0.0, $lodgingSubtotal - $offerDiscountAmount + $serviceTotal);
        $dynamicDelta = ($dynamicNightlyRate - $nightlyRate) * $duration;

        return [
            'currency' => 'USD',
            'duration' => $duration,
            'baseNightlyRate' => round($nightlyRate, 2),
            'baseLodgingSubtotal' => round($nightlyRate * $duration, 2),
            'dynamicNightlyRate' => round($dynamicNightlyRate, 2),
            'dynamicLodgingSubtotal' => round($lodgingSubtotal, 2),
            'dynamicPricingDelta' => round($dynamicDelta, 2),
            'seasonalMultiplier' => round($seasonalAdjustment['multiplier'], 4),
            'seasonalLabel' => $seasonalAdjustment['label'],
            'timingMultiplier' => round($timingAdjustment['multiplier'], 4),
            'timingLabel' => $timingAdjustment['label'],
            'offerDiscountPercent' => round($offerDiscountPercent, 2),
            'offerDiscountAmount' => round($offerDiscountAmount, 2),
            'serviceTotal' => round($serviceTotal, 2),
            'serviceLabels' => $serviceLabels,
            'total' => round($total, 2),
            'narrative' => $this->buildNarrative($seasonalAdjustment['label'], $timingAdjustment['label'], $dynamicDelta, $offerDiscountPercent),
        ];
    }

    public function applyPricing(Booking $booking): void
    {
        $snapshot = $this->buildPricingSnapshot($booking);
        $booking->setTotalPrice((float) $snapshot['total']);
        $booking->setPricingSnapshot($snapshot);
    }

    /**
     * @return array{multiplier: float, label: string}
     */
    private function resolveSeasonalAdjustment(?\DateTimeImmutable $bookingDate): array
    {
        if ($bookingDate === null) {
            return ['multiplier' => 1.0, 'label' => 'Standard season rate'];
        }

        $month = (int) $bookingDate->format('n');
        $day = (int) $bookingDate->format('j');

        if (($month === 12 && $day >= 15) || $month === 1) {
            return ['multiplier' => 1.24, 'label' => 'Holiday peak season'];
        }

        if (in_array($month, [6, 7, 8], true)) {
            return ['multiplier' => 1.18, 'label' => 'Summer peak season'];
        }

        if (in_array($month, [4, 5, 9, 10], true)) {
            return ['multiplier' => 1.08, 'label' => 'Shoulder season uplift'];
        }

        return ['multiplier' => 0.93, 'label' => 'Low-season advantage'];
    }

    /**
     * @return array{multiplier: float, label: string}
     */
    private function resolveTimingAdjustment(?\DateTimeImmutable $bookingDate, int $duration): array
    {
        if ($bookingDate === null) {
            return ['multiplier' => 1.0, 'label' => 'Standard booking window'];
        }

        $today = new \DateTimeImmutable('today');
        $leadDays = max(0, (int) $today->diff($bookingDate)->format('%r%a'));
        $weekday = (int) $bookingDate->format('N');
        $multiplier = 1.0;
        $labels = [];

        if (in_array($weekday, [5, 6], true)) {
            $multiplier *= 1.06;
            $labels[] = 'weekend demand';
        }

        if ($leadDays >= 60) {
            $multiplier *= 0.95;
            $labels[] = 'early-bird discount';
        } elseif ($leadDays <= 3) {
            $multiplier *= 1.07;
            $labels[] = 'last-minute premium';
        }

        if ($duration >= 14) {
            $multiplier *= 0.88;
            $labels[] = 'extended-stay discount';
        } elseif ($duration >= 7) {
            $multiplier *= 0.93;
            $labels[] = 'long-stay discount';
        }

        return [
            'multiplier' => $multiplier,
            'label' => ucfirst(implode(', ', $labels === [] ? ['standard booking window'] : $labels)),
        ];
    }

    private function resolveActiveDiscountPercent(Booking $booking): float
    {
        $property = $booking->getProperty();
        $bookingDate = $booking->getBookingDate();
        if ($property === null || $bookingDate === null) {
            return 0.0;
        }

        $bestDiscount = 0.0;
        foreach ($property->getOffers() as $offer) {
            if (!$offer->isActive() || $offer->getStartDate() === null || $offer->getEndDate() === null) {
                continue;
            }

            if ($bookingDate >= $offer->getStartDate() && $bookingDate <= $offer->getEndDate()) {
                $bestDiscount = max($bestDiscount, (float) $offer->getDiscountPercentage());
            }
        }

        return min(100.0, max(0.0, $bestDiscount));
    }

    private function buildNarrative(string $seasonLabel, string $timingLabel, float $dynamicDelta, float $offerDiscountPercent): string
    {
        $direction = $dynamicDelta >= 0 ? 'above' : 'below';
        $parts = [
            sprintf('Dynamic pricing places this stay %s the base rate by $%s.', $direction, number_format(abs($dynamicDelta), 2, '.', ',')),
            sprintf('Seasonal factor: %s.', $seasonLabel),
            sprintf('Timing factor: %s.', $timingLabel),
        ];

        if ($offerDiscountPercent > 0.0) {
            $parts[] = sprintf('An active offer adds a %.2f%% discount.', $offerDiscountPercent);
        }

        return implode(' ', $parts);
    }
}
