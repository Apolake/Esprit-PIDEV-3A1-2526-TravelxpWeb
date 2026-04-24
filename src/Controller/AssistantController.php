<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\AppAssistantService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class AssistantController extends AbstractController
{
    #[Route('/assistant/chat', name: 'app_assistant_chat', methods: ['POST'])]
    public function chat(Request $request, AppAssistantService $assistantService): JsonResponse
    {
        $decoded = json_decode($request->getContent(), true);
        if (!is_array($decoded)) {
            return $this->json(['error' => 'Invalid request payload.'], Response::HTTP_BAD_REQUEST);
        }

        $csrfToken = trim((string) ($request->headers->get('X-CSRF-Token') ?? $decoded['csrf_token'] ?? ''));
        if (!$this->isCsrfTokenValid('assistant_chat', $csrfToken) && !$this->isTrustedAssistantRequest($request)) {
            return $this->json(['error' => 'Invalid CSRF token.'], Response::HTTP_FORBIDDEN);
        }

        $message = trim((string) ($decoded['message'] ?? ''));
        if ($message === '') {
            return $this->json(['error' => 'Message cannot be empty.'], Response::HTTP_BAD_REQUEST);
        }

        if (mb_strlen($message) > 1200) {
            return $this->json(['error' => 'Message is too long.'], Response::HTTP_BAD_REQUEST);
        }

        if ($request->hasSession()) {
            $session = $request->getSession();
            $timestamps = $session->get('assistant_chat_timestamps', []);
            $now = time();

            if (!is_array($timestamps)) {
                $timestamps = [];
            }

            $recent = array_values(array_filter(
                $timestamps,
                static fn (mixed $timestamp): bool => is_int($timestamp) && ($now - $timestamp) < 60
            ));

            if (count($recent) >= 25) {
                return $this->json(['error' => 'Too many requests. Please wait a moment.'], Response::HTTP_TOO_MANY_REQUESTS);
            }

            $recent[] = $now;
            $session->set('assistant_chat_timestamps', $recent);
        }

        $history = $decoded['history'] ?? [];
        if (!is_array($history)) {
            $history = [];
        }

        $user = $this->getUser();
        $isAuthenticated = $user instanceof User;
        $isAdmin = $isAuthenticated && in_array('ROLE_ADMIN', $user->getRoles(), true);

        try {
            $reply = $assistantService->ask(
                $message,
                $history,
                $request->getSchemeAndHttpHost(),
                $isAuthenticated,
                $isAdmin
            );

            return $this->json([
                'reply' => $reply,
            ]);
        } catch (\RuntimeException $exception) {
            return $this->json(['error' => $exception->getMessage()], Response::HTTP_SERVICE_UNAVAILABLE);
        } catch (\Throwable) {
            return $this->json(['error' => 'Assistant request failed.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function isTrustedAssistantRequest(Request $request): bool
    {
        if (!$request->isXmlHttpRequest()) {
            return false;
        }

        $contentType = strtolower(trim((string) $request->headers->get('Content-Type', '')));
        if ($contentType !== '' && !str_contains($contentType, 'application/json')) {
            return false;
        }

        if ($this->isSameOriginRequest($request)) {
            return true;
        }

        $clientIp = (string) $request->getClientIp();
        $host = strtolower((string) $request->getHost());
        $isLoopbackHost = in_array($host, ['127.0.0.1', 'localhost', '::1'], true);
        $isLoopbackIp = in_array($clientIp, ['127.0.0.1', '::1', ''], true);

        return $isLoopbackHost && $isLoopbackIp;
    }

    private function isSameOriginRequest(Request $request): bool
    {
        $origin = trim((string) $request->headers->get('Origin', ''));
        $referer = trim((string) $request->headers->get('Referer', ''));
        $hostUrl = $request->getSchemeAndHttpHost();

        if ($this->urlIsSameOrigin($origin, $hostUrl)) {
            return true;
        }

        return $this->urlIsSameOrigin($referer, $hostUrl);
    }

    private function urlIsSameOrigin(string $candidateUrl, string $hostUrl): bool
    {
        if ($candidateUrl === '') {
            return false;
        }

        $candidate = parse_url($candidateUrl);
        $host = parse_url($hostUrl);
        if (!is_array($candidate) || !is_array($host)) {
            return false;
        }

        $candidateScheme = strtolower((string) ($candidate['scheme'] ?? ''));
        $hostScheme = strtolower((string) ($host['scheme'] ?? ''));
        if ($candidateScheme !== $hostScheme) {
            return false;
        }

        $candidatePort = (int) ($candidate['port'] ?? ($candidateScheme === 'https' ? 443 : 80));
        $hostPort = (int) ($host['port'] ?? ($hostScheme === 'https' ? 443 : 80));
        if ($candidatePort !== $hostPort) {
            return false;
        }

        $candidateHost = strtolower((string) ($candidate['host'] ?? ''));
        $baseHost = strtolower((string) ($host['host'] ?? ''));

        return $this->hostsEquivalent($candidateHost, $baseHost);
    }

    private function hostsEquivalent(string $a, string $b): bool
    {
        if ($a === $b) {
            return true;
        }

        $loopbackAliases = ['127.0.0.1', 'localhost', '::1'];
        if (in_array($a, $loopbackAliases, true) && in_array($b, $loopbackAliases, true)) {
            return true;
        }

        return false;
    }
}
