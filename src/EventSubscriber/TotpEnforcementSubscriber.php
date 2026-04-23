<?php

namespace App\EventSubscriber;

use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

class TotpEnforcementSubscriber implements EventSubscriberInterface
{
    private const FIREWALL_NAME = 'main';

    /**
     * @var list<string>
     */
    private array $whitelistedRoutes = [
        'app_2fa_challenge',
        'app_logout',
        'app_login',
    ];

    public function __construct(
        private readonly Security $security,
        private readonly UrlGeneratorInterface $urlGenerator,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', -10],
            LoginSuccessEvent::class => 'onLoginSuccess',
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $route = (string) $request->attributes->get('_route', '');
        if (
            '' === $route
            || in_array($route, $this->whitelistedRoutes, true)
            || str_starts_with($route, '_profiler')
            || str_starts_with($route, '_wdt')
        ) {
            return;
        }

        $user = $this->security->getUser();
        if (!$user instanceof User || !$user->isTotpEnabled()) {
            return;
        }

        if (!$request->hasSession()) {
            return;
        }

        $session = $request->getSession();
        if ((int) $session->get('totp_verified_user_id', 0) === (int) $user->getId()) {
            return;
        }

        if ($request->isMethod('GET')) {
            $session->set('totp_target_path', $request->getRequestUri());
        }

        $event->setResponse(new RedirectResponse(
            $this->urlGenerator->generate('app_2fa_challenge')
        ));
    }

    public function onLoginSuccess(LoginSuccessEvent $event): void
    {
        $request = $event->getRequest();
        if (!$request->hasSession()) {
            return;
        }

        $session = $request->getSession();
        $session->remove('totp_verified_user_id');
        $session->remove('totp_target_path');

        $user = $event->getUser();
        if (!$user instanceof User || !$user->isTotpEnabled()) {
            return;
        }

        $firewallTargetPathKey = sprintf('_security.%s.target_path', self::FIREWALL_NAME);
        $defaultTargetPath = (string) $session->get($firewallTargetPathKey, '');
        $requestedTargetPath = (string) $request->request->get('_target_path', '');
        $targetPath = $requestedTargetPath !== '' ? $requestedTargetPath : $defaultTargetPath;

        if (
            $targetPath !== ''
            && str_starts_with($targetPath, '/')
            && !str_starts_with($targetPath, '/2fa/challenge')
            && !str_starts_with($targetPath, '/login')
        ) {
            $session->set('totp_target_path', $targetPath);
        }

        $event->setResponse(new RedirectResponse(
            $this->urlGenerator->generate('app_2fa_challenge')
        ));
    }
}
