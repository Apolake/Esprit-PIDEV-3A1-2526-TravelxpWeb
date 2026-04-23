<?php

namespace App\Twig;

use App\Entity\User;
use App\Repository\NotificationRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class NotificationExtension extends AbstractExtension
{
    public function __construct(
        private readonly Security $security,
        private readonly NotificationRepository $notificationRepository,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('unread_notifications_count', [$this, 'getUnreadNotificationsCount']),
            new TwigFunction('latest_notifications', [$this, 'getLatestNotifications']),
        ];
    }

    public function getUnreadNotificationsCount(): int
    {
        $user = $this->getCurrentUser();
        if ($user === null) {
            return 0;
        }

        return $this->notificationRepository->countUnreadByUser($user);
    }

    /**
     * @return array<int, \App\Entity\Notification>
     */
    public function getLatestNotifications(int $limit = 8): array
    {
        $user = $this->getCurrentUser();
        if ($user === null) {
            return [];
        }

        return $this->notificationRepository->findLatestByUser($user, max(1, $limit));
    }

    private function getCurrentUser(): ?User
    {
        $user = $this->security->getUser();

        return $user instanceof User ? $user : null;
    }
}
