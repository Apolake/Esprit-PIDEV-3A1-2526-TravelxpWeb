<?php

namespace App\Service;

use App\Entity\Notification;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class NotificationService
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function create(User $user, string $type, string $title, string $message, ?array $context = null): void
    {
        $notification = (new Notification())
            ->setUser($user)
            ->setType($type)
            ->setTitle($title)
            ->setMessage($message)
            ->setContext($context);

        $this->entityManager->persist($notification);
    }
}
