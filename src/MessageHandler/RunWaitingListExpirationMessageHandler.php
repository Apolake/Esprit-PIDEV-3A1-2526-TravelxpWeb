<?php

namespace App\MessageHandler;

use App\Message\RunWaitingListExpirationMessage;
use App\Service\SchedulerRunStateService;
use App\Service\WaitingListSchedulerService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class RunWaitingListExpirationMessageHandler
{
    public function __construct(
        private readonly WaitingListSchedulerService $waitingListSchedulerService,
        private readonly SchedulerRunStateService $schedulerRunStateService,
        private readonly EntityManagerInterface $entityManager,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function __invoke(RunWaitingListExpirationMessage $message): void
    {
        $startedAt = new \DateTimeImmutable();

        try {
            $result = $this->waitingListSchedulerService->runExpirationSweep(new \DateTimeImmutable());
            $this->entityManager->flush();
            $finishedAt = new \DateTimeImmutable();
            $this->schedulerRunStateService->markSuccess(
                SchedulerRunStateService::JOB_WAITING_LIST_EXPIRATION,
                $result,
                $startedAt,
                $finishedAt
            );

            $this->logger->info('Scheduled waiting-list sweep completed.', $result);
        } catch (\Throwable $exception) {
            $failedAt = new \DateTimeImmutable();
            $this->schedulerRunStateService->markFailure(
                SchedulerRunStateService::JOB_WAITING_LIST_EXPIRATION,
                ['exception' => $exception::class],
                $startedAt,
                $failedAt,
                $exception->getMessage()
            );

            $this->logger->error('Scheduled waiting-list sweep failed.', [
                'exception' => $exception::class,
                'message' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }
}
