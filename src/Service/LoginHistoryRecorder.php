<?php

namespace App\Service;

use App\Entity\LoginHistory;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;

class LoginHistoryRecorder
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly IpApiLocationResolver $locationResolver,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function recordSuccessfulLogin(User $user, Request $request): void
    {
        try {
            $ipAddress = $this->extractIpAddress($request);
            $location = $this->locationResolver->resolve($ipAddress);

            $history = new LoginHistory();
            $history
                ->setUser($user)
                ->setIpAddress($ipAddress)
                ->setUserAgent((string) $request->headers->get('User-Agent', ''))
                ->setCountry($location['country'] ?? null)
                ->setRegion($location['region'] ?? null)
                ->setCity($location['city'] ?? null)
                ->setIsp($location['isp'] ?? null)
                ->setLatitude($location['latitude'] ?? null)
                ->setLongitude($location['longitude'] ?? null)
                ->setLoginAt(new \DateTimeImmutable());

            $this->entityManager->persist($history);
            $this->entityManager->flush();
        } catch (\Throwable $exception) {
            $this->logger->error('Failed to persist login history.', [
                'user_id' => $user->getId(),
                'message' => $exception->getMessage(),
                'exception_class' => $exception::class,
            ]);
        }
    }

    private function extractIpAddress(Request $request): string
    {
        $clientIp = trim((string) $request->getClientIp());
        if ('' !== $clientIp) {
            return $clientIp;
        }

        $remoteAddress = trim((string) $request->server->get('REMOTE_ADDR', ''));

        return '' === $remoteAddress ? 'unknown' : $remoteAddress;
    }
}
