<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Entity\User;
use App\Repository\NotificationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class NotificationController extends AbstractController
{
    #[Route('/notifications', name: 'notification_index', methods: ['GET'])]
    public function index(Request $request, NotificationRepository $notificationRepository, PaginatorInterface $paginator): Response
    {
        $viewer = $this->getUser();
        if (!$viewer instanceof User) {
            throw $this->createAccessDeniedException('Authentication required.');
        }

        $page = max(1, $request->query->getInt('page', 1));
        $perPage = 20;
        $qb = $notificationRepository->createPagedByUserQueryBuilder($viewer);
        $pagination = $paginator->paginate($qb, $page, $perPage, [
            'distinct' => true,
        ]);
        $notificationItems = $pagination->getItems();
        if ($notificationItems instanceof \Traversable) {
            $notificationItems = iterator_to_array($notificationItems);
        }
        if (!is_array($notificationItems)) {
            $notificationItems = [];
        }
        $totalItems = (int) $pagination->getTotalItemCount();
        $totalPages = max(1, (int) ceil($totalItems / $perPage));

        return $this->render('notification/index.html.twig', [
            'notifications' => $notificationItems,
            'pagination' => [
                'page' => (int) $pagination->getCurrentPageNumber(),
                'perPage' => $perPage,
                'totalItems' => $totalItems,
                'totalPages' => $totalPages,
            ],
        ]);
    }

    #[Route('/notifications/{id}/read', name: 'notification_mark_read', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function markRead(
        Request $request,
        Notification $notification,
        EntityManagerInterface $entityManager,
    ): RedirectResponse {
        $viewer = $this->getUser();
        if (!$viewer instanceof User || $notification->getUser()?->getId() !== $viewer->getId()) {
            throw $this->createAccessDeniedException('You cannot modify this notification.');
        }

        if (!$this->isCsrfTokenValid('notification_read_' . $notification->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('danger', 'Invalid notification action.');

            return $this->redirectBack($request);
        }

        if (!$notification->isRead()) {
            $notification->markAsRead();
            $entityManager->flush();
        }

        return $this->redirectBack($request);
    }

    #[Route('/notifications/read-all', name: 'notification_mark_all_read', methods: ['POST'])]
    public function markAllRead(
        Request $request,
        NotificationRepository $notificationRepository,
        EntityManagerInterface $entityManager,
    ): RedirectResponse {
        $viewer = $this->getUser();
        if (!$viewer instanceof User) {
            throw $this->createAccessDeniedException('Authentication required.');
        }

        if (!$this->isCsrfTokenValid('notification_read_all', (string) $request->request->get('_token'))) {
            $this->addFlash('danger', 'Invalid notification action.');

            return $this->redirectBack($request);
        }

        foreach ($notificationRepository->findUnreadByUser($viewer, 200) as $notification) {
            $notification->markAsRead();
        }
        $entityManager->flush();

        $this->addFlash('success', 'All notifications marked as read.');

        return $this->redirectBack($request, 'notification_index');
    }

    private function redirectBack(Request $request, string $fallbackRoute = 'app_home'): RedirectResponse
    {
        $referer = (string) $request->headers->get('referer', '');
        if ($referer !== '') {
            return $this->redirect($referer);
        }

        return $this->redirectToRoute($fallbackRoute);
    }
}
