<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfileType;
use App\Repository\LoginHistoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/profile')]
#[IsGranted('ROLE_USER')]
class ProfileController extends AbstractController
{
    #[Route('', name: 'app_profile_show', methods: ['GET'])]
    public function show(LoginHistoryRepository $loginHistoryRepository): Response
    {
        $user = $this->getCurrentUser();

        return $this->render('profile/show.html.twig', [
            'loginHistory' => $loginHistoryRepository->findRecentForUser($user, 15),
        ]);
    }

    #[Route('/edit', name: 'app_profile_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = $this->getCurrentUser();
        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $plainPassword = (string) ($form->get('plainPassword')->getData() ?? '');
            if ('' !== $plainPassword && !$this->isStrongPassword($plainPassword)) {
                $form->get('plainPassword')->addError(new FormError('Password must be at least 8 characters and include at least one letter and one number.'));
            }

            if ($form->isValid()) {
                if ('' !== $plainPassword) {
                    $user->setPassword($passwordHasher->hashPassword($user, $plainPassword));
                }

                $entityManager->flush();
                $this->addFlash('success', 'Profile updated successfully.');

                return $this->redirectToRoute('app_profile_show');
            }
        }

        return $this->render('profile/edit.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/delete', name: 'app_profile_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        TokenStorageInterface $tokenStorage
    ): Response {
        $user = $this->getCurrentUser();

        if (!$this->isCsrfTokenValid('delete_profile', (string) $request->request->get('_token'))) {
            $this->addFlash('error', 'Invalid CSRF token.');

            return $this->redirectToRoute('app_profile_show');
        }

        $password = (string) $request->request->get('current_password', '');
        if ('' === $password || !$passwordHasher->isPasswordValid($user, $password)) {
            $this->addFlash('error', 'Current password is incorrect.');

            return $this->redirectToRoute('app_profile_show');
        }

        $entityManager->remove($user);
        $entityManager->flush();

        $tokenStorage->setToken(null);
        $request->getSession()->invalidate();

        $this->addFlash('success', 'Your account has been deleted.');

        return $this->redirectToRoute('app_home');
    }

    private function getCurrentUser(): User
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw $this->createAccessDeniedException('User not authenticated.');
        }

        return $user;
    }

    private function isStrongPassword(string $password): bool
    {
        return strlen($password) >= 8
            && (bool) preg_match('/[A-Za-z]/', $password)
            && (bool) preg_match('/\d/', $password);
    }
}
