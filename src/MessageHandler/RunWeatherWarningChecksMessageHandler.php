<?php

namespace App\MessageHandler;

use App\Message\RunWeatherWarningChecksMessage;
use App\Service\SchedulerRunStateService;
use App\Service\WeatherAlertSchedulerService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class RunWeatherWarningChecksMessageHandler
{
    public function __construct(
        private readonly WeatherAlertSchedulerService $weatherAlertSchedulerService,
        private readonly SchedulerRunStateService $schedulerRunStateService,
        private readonly EntityManagerInterface $entityManager,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function __invoke(RunWeatherWarningChecksMessage $message): void
    {
        $startedAt = new \DateTimeImmutable();

        try {
            $result = $this->weatherAlertSchedulerService->runWeatherChecks(new \DateTimeImmutable());
            $this->entityManager->flush();
            $finishedAt = new \DateTimeImmutable();
            $this->schedulerRunStateService->markSuccess(
                SchedulerRunStateService::JOB_WEATHER_WARNING_CHECKS,
                $result,
                $startedAt,
                $finishedAt
            );

            $this->logger->info('Scheduled weather checks completed.', $result);
        } catch (\Throwable $exception) {
            $failedAt = new \DateTimeImmutable();
            $this->schedulerRunStateService->markFailure(
                SchedulerRunStateService::JOB_WEATHER_WARNING_CHECKS,
                ['exception' => $exception::class],
                $startedAt,
                $failedAt,
                $exception->getMessage()
            );

            $this->logger->error('Scheduled weather checks failed.', [
                'exception' => $exception::class,
                'message' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }
}
