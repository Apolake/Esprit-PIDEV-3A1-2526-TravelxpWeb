<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class EntryController extends AbstractController
{
    #[Route('/', name: 'app_entry', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $connection = $entityManager->getConnection();

        $adminUsers = $connection->fetchAllAssociative(
            "SELECT id, username, email FROM users WHERE UPPER(COALESCE(role, '')) = 'ADMIN' ORDER BY username ASC"
        );
        $normalUsers = $connection->fetchAllAssociative(
            "SELECT id, username, email FROM users WHERE UPPER(COALESCE(role, '')) = 'USER' ORDER BY username ASC"
        );

        return $this->render('entry/index.html.twig', [
            'admin_users' => $adminUsers,
            'normal_users' => $normalUsers,
        ]);
    }

    #[Route('/entry/select', name: 'app_entry_select', methods: ['POST'])]
    public function select(Request $request, EntityManagerInterface $entityManager): Response
    {
        $mode = strtolower((string) $request->request->get('mode', ''));
        $userId = (int) $request->request->get('user_id', 0);

        if (!in_array($mode, ['admin', 'user'], true) || $userId <= 0) {
            return $this->redirectToRoute('app_entry');
        }

        $expectedRole = $mode === 'admin' ? 'ADMIN' : 'USER';
        $user = $entityManager->getConnection()->fetchAssociative(
            'SELECT id, role FROM users WHERE id = :id LIMIT 1',
            ['id' => $userId]
        );

        if (!$user || strtoupper((string) $user['role']) !== $expectedRole) {
            return $this->redirectToRoute('app_entry');
        }

        $session = $request->getSession();
        $session->set('active_user_id', (int) $user['id']);
        $session->set('active_role', $expectedRole);

        return $this->redirectToRoute('app_dashboard');
    }

    #[Route('/entry/reset', name: 'app_entry_reset', methods: ['POST'])]
    public function reset(Request $request): Response
    {
        $session = $request->getSession();
        $session->remove('active_user_id');
        $session->remove('active_role');

        return $this->redirectToRoute('app_entry');
    }
}
