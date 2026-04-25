<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\GamificationProgressService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home', methods: ['GET'])]
    public function index(GamificationProgressService $gamificationProgressService): Response
    {
        $authenticated = $this->getUser();
        $user = $authenticated instanceof User ? $authenticated : null;

        return $this->render('home/index.html.twig', [
            'gamification' => $gamificationProgressService->buildForUser($user),
        ]);
    }

    #[Route('/admin', name: 'admin_portal', methods: ['GET'])]
    public function adminPortal(): Response
    {
        return $this->redirectToRoute('admin_property_index');
    }

    #[Route('/user', name: 'user_portal', methods: ['GET'])]
    public function userPortal(): Response
    {
        return $this->redirectToRoute('property_index');
    }
}
