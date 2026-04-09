<?php

namespace App\Controller;

use App\Entity\Quest;
use App\Entity\User;
use App\Form\QuestType;
use App\Form\UserGamificationType;
use App\Repository\QuestRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/gamification')]
#[IsGranted('ROLE_ADMIN')]
class AdminGamificationController extends AbstractController
{
    #[Route('/', name: 'app_admin_gamification_index', methods: ['GET'])]
    public function index(UserRepository $userRepository, QuestRepository $questRepository): Response
    {
        return $this->render('admin_gamification/index.html.twig', [
            'users' => $userRepository->findBy([], ['username' => 'ASC']),
            'quests' => $questRepository->findBy([], ['updatedAt' => 'DESC']),
        ]);
    }

    #[Route('/quest/new', name: 'app_admin_gamification_quest_new', methods: ['GET', 'POST'])]
    public function newQuest(Request $request, EntityManagerInterface $entityManager): Response
    {
        $quest = new Quest();
        $form = $this->createForm(QuestType::class, $quest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($quest);
            $entityManager->flush();
            $this->addFlash('success', 'Quest created successfully.');

            return $this->redirectToRoute('app_admin_gamification_index');
        }

        return $this->render('admin_gamification/quest_form.html.twig', [
            'title' => 'Create quest',
            'form' => $form,
        ]);
    }

    #[Route('/quest/{id}/edit', name: 'app_admin_gamification_quest_edit', methods: ['GET', 'POST'])]
    public function editQuest(Request $request, Quest $quest, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(QuestType::class, $quest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Quest updated successfully.');

            return $this->redirectToRoute('app_admin_gamification_index');
        }

        return $this->render('admin_gamification/quest_form.html.twig', [
            'title' => 'Edit quest',
            'form' => $form,
        ]);
    }

    #[Route('/quest/{id}/delete', name: 'app_admin_gamification_quest_delete', methods: ['POST'])]
    public function deleteQuest(Request $request, Quest $quest, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete_quest'.$quest->getId(), (string) $request->request->get('_token'))) {
            $entityManager->remove($quest);
            $entityManager->flush();
            $this->addFlash('success', 'Quest deleted.');
        }

        return $this->redirectToRoute('app_admin_gamification_index');
    }

    #[Route('/user/{id}/edit', name: 'app_admin_gamification_user_edit', methods: ['GET', 'POST'])]
    public function editUserGamification(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserGamificationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', sprintf('Gamification stats updated for %s.', $user->getUsername()));

            return $this->redirectToRoute('app_admin_gamification_index');
        }

        return $this->render('admin_gamification/user_form.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }
}
