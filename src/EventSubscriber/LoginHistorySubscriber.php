<?php

namespace App\EventSubscriber;

use App\Entity\User;
use App\Service\LoginHistoryRecorder;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

class LoginHistorySubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly LoginHistoryRecorder $loginHistoryRecorder,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            LoginSuccessEvent::class => 'onLoginSuccess',
        ];
    }

    public function onLoginSuccess(LoginSuccessEvent $event): void
    {
        $user = $event->getUser();
        if (!$user instanceof User) {
            return;
        }

        if ($user->isTotpEnabled()) {
            return;
        }

        $this->loginHistoryRecorder->recordSuccessfulLogin($user, $event->getRequest());
    }
}
