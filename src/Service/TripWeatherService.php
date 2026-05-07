<?php

namespace App\Service;

use App\Entity\Trip;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class TripWeatherService
{
    public function __construct(private readonly HttpClientInterface $httpClient)
    {
    }

    /**
     * @return array<string, mixed>|null
     */
    public function fetchForTrip(Trip $trip): ?array
    {
        $lat = $trip->getDestinationLatitude();
        $lng = $trip->getDestinationLongitude();
        if ($lat === null || $lng === null) {
            return null;
        }

        try {
            $response = $this->httpClient->request('GET', 'https://api.open-meteo.com/v1/forecast', [
                'query' => [
                    'latitude' => $lat,
                    'longitude' => $lng,
                    'current' => 'temperature_2m,weather_code,wind_speed_10m',
                    'daily' => 'weather_code,temperature_2m_max,temperature_2m_min,precipitation_probability_max,wind_speed_10m_max',
                    'forecast_days' => 5,
                    'timezone' => 'auto',
                ],
                'timeout' => 6,
            ]);

            if ($response->getStatusCode() !== 200) {
                return $this->buildPlanningFallback($trip, (float) $lat);
            }

            $payload = $response->toArray(false);
            if (!isset($payload['current'], $payload['daily'])) {
                return $this->buildPlanningFallback($trip, (float) $lat);
            }

            $current = is_array($payload['current']) ? $payload['current'] : [];
            $daily = is_array($payload['daily']) ? $payload['daily'] : [];
            $warnings = $this->buildWarnings($daily);

            $currentCode = (int) ($current['weather_code'] ?? -1);

            return [
                'current' => [
                    'temperature' => isset($current['temperature_2m']) ? (float) $current['temperature_2m'] : null,
                    'wind' => isset($current['wind_speed_10m']) ? (float) $current['wind_speed_10m'] : null,
                    'code' => $currentCode,
                    'label' => $this->weatherLabel($currentCode),
                    'icon' => $this->weatherIcon($currentCode),
                ],
                'daily' => $this->dailyRows($daily),
                'warnings' => $warnings,
            ];
        } catch (\Throwable) {
            return $this->buildPlanningFallback($trip, (float) $lat);
        }
    }

    /**
     * Build a lightweight planning estimate when live weather API is unavailable.
     *
     * @return array<string, mixed>
     */
    private function buildPlanningFallback(Trip $trip, float $latitude): array
    {
        $baseDate = $trip->getStartDate() ?? new \DateTimeImmutable('today');
        $month = (int) $baseDate->format('n');
        $northernHemisphere = $latitude >= 0;

        $season = match (true) {
            in_array($month, [12, 1, 2], true) => $northernHemisphere ? 'winter' : 'summer',
            in_array($month, [3, 4, 5], true) => $northernHemisphere ? 'spring' : 'autumn',
            in_array($month, [6, 7, 8], true) => $northernHemisphere ? 'summer' : 'winter',
            default => $northernHemisphere ? 'autumn' : 'spring',
        };

        $seasonBase = match ($season) {
            'winter' => 10.0,
            'spring' => 20.0,
            'summer' => 30.0,
            default => 22.0,
        };

        $latitudeCooling = min(14.0, abs($latitude) / 90.0 * 14.0);
        $avg = max(4.0, $seasonBase - $latitudeCooling);
        $wind = 14.0;
        $daily = [];
        for ($i = 0; $i < 5; $i++) {
            $date = $baseDate->modify(sprintf('+%d day', $i));
            $daily[] = [
                'date' => $date->format('Y-m-d'),
                'weatherCode' => 1,
                'label' => 'Partly cloudy',
                'icon' => 'fa-solid fa-cloud-sun',
                'maxTemp' => round($avg + 2 + ($i % 2), 1),
                'minTemp' => round(max(0.0, $avg - 4 - ($i % 2)), 1),
                'rainProbability' => 25 + ($i * 5),
                'windMax' => $wind + ($i * 0.8),
            ];
        }

        return [
            'current' => [
                'temperature' => round($avg, 1),
                'wind' => $wind,
                'code' => 1,
                'label' => 'Partly cloudy (estimate)',
                'icon' => 'fa-solid fa-cloud-sun',
            ],
            'daily' => $daily,
            'warnings' => [
                'Live weather is temporarily unavailable. Showing a planning estimate.',
            ],
        ];
    }

    /**
     * @param array<string, mixed> $daily
     * @return array<int, array<string, mixed>>
     */
    private function dailyRows(array $daily): array
    {
        $dates = is_array($daily['time'] ?? null) ? $daily['time'] : [];
        $codes = is_array($daily['weather_code'] ?? null) ? $daily['weather_code'] : [];
        $max = is_array($daily['temperature_2m_max'] ?? null) ? $daily['temperature_2m_max'] : [];
        $min = is_array($daily['temperature_2m_min'] ?? null) ? $daily['temperature_2m_min'] : [];
        $rain = is_array($daily['precipitation_probability_max'] ?? null) ? $daily['precipitation_probability_max'] : [];
        $wind = is_array($daily['wind_speed_10m_max'] ?? null) ? $daily['wind_speed_10m_max'] : [];

        $rows = [];
        foreach ($dates as $i => $date) {
            $code = isset($codes[$i]) ? (int) $codes[$i] : -1;
            $rows[] = [
                'date' => (string) $date,
                'weatherCode' => $code,
                'label' => $this->weatherLabel($code),
                'icon' => $this->weatherIcon($code),
                'maxTemp' => isset($max[$i]) ? (float) $max[$i] : null,
                'minTemp' => isset($min[$i]) ? (float) $min[$i] : null,
                'rainProbability' => isset($rain[$i]) ? (int) $rain[$i] : null,
                'windMax' => isset($wind[$i]) ? (float) $wind[$i] : null,
            ];
        }

        return $rows;
    }

    /**
     * @param array<string, mixed> $daily
     * @return list<string>
     */
    private function buildWarnings(array $daily): array
    {
        $warnings = [];
        $rain = is_array($daily['precipitation_probability_max'] ?? null) ? $daily['precipitation_probability_max'] : [];
        $wind = is_array($daily['wind_speed_10m_max'] ?? null) ? $daily['wind_speed_10m_max'] : [];
        $maxTemp = is_array($daily['temperature_2m_max'] ?? null) ? $daily['temperature_2m_max'] : [];

        foreach (array_slice($rain, 0, 3) as $value) {
            if ((int) $value >= 70) {
                $warnings[] = 'Rain expected during upcoming days. Outdoor activities may be affected.';
                break;
            }
        }

        foreach (array_slice($wind, 0, 3) as $value) {
            if ((float) $value >= 40.0) {
                $warnings[] = 'Strong wind expected. Plan transport and outdoor activities carefully.';
                break;
            }
        }

        foreach (array_slice($maxTemp, 0, 3) as $value) {
            if ((float) $value >= 35.0) {
                $warnings[] = 'High temperature warning. Stay hydrated during trip activities.';
                break;
            }
        }

        return $warnings;
    }

    private function weatherLabel(int $code): string
    {
        return match (true) {
            $code === 0 => 'Clear sky',
            in_array($code, [1, 2, 3], true) => 'Partly cloudy',
            in_array($code, [45, 48], true) => 'Fog',
            in_array($code, [51, 53, 55, 56, 57], true) => 'Drizzle',
            in_array($code, [61, 63, 65, 66, 67], true) => 'Rain',
            in_array($code, [71, 73, 75, 77], true) => 'Snow',
            in_array($code, [80, 81, 82], true) => 'Rain showers',
            in_array($code, [85, 86], true) => 'Snow showers',
            in_array($code, [95, 96, 99], true) => 'Thunderstorm',
            default => 'Unknown',
        };
    }

    private function weatherIcon(int $code): string
    {
        return match (true) {
            $code === 0 => 'fa-solid fa-sun',
            in_array($code, [1, 2, 3], true) => 'fa-solid fa-cloud-sun',
            in_array($code, [45, 48], true) => 'fa-solid fa-smog',
            in_array($code, [51, 53, 55, 56, 57], true) => 'fa-solid fa-cloud-rain',
            in_array($code, [61, 63, 65, 66, 67, 80, 81, 82], true) => 'fa-solid fa-cloud-showers-heavy',
            in_array($code, [71, 73, 75, 77, 85, 86], true) => 'fa-solid fa-snowflake',
            in_array($code, [95, 96, 99], true) => 'fa-solid fa-bolt',
            default => 'fa-solid fa-cloud',
        };
    }
}
