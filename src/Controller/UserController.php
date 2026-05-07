<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/users')]
#[IsGranted('ROLE_ADMIN')]
final class UserController extends AbstractController
{
    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(Request $request, UserRepository $userRepository, PaginatorInterface $paginator): Response
    {
        $query = $request->query->getString('q');
        $role = $request->query->getString('role');
        $sortBy = $request->query->getString('sort', 'createdAt');
        $direction = $request->query->getString('direction', 'DESC');
        $page = max(1, $request->query->getInt('page', 1));
        $perPage = 12;

        $qb = $userRepository->createAdminFilteredQueryBuilder($query, $role, $sortBy, $direction);
        $pagination = $paginator->paginate($qb, $page, $perPage, [
            'distinct' => true,
        ]);
        $userItems = $pagination->getItems();
        if ($userItems instanceof \Traversable) {
            $userItems = iterator_to_array($userItems);
        }
        if (!is_array($userItems)) {
            $userItems = [];
        }
        $totalItems = (int) $pagination->getTotalItemCount();
        $totalPages = max(1, (int) ceil($totalItems / $perPage));

        if ($request->isXmlHttpRequest()) {
            return $this->render('user/_table.html.twig', [
                'users' => $userItems,
                'pagination' => [
                    'page' => (int) $pagination->getCurrentPageNumber(),
                    'perPage' => $perPage,
                    'totalItems' => $totalItems,
                    'totalPages' => $totalPages,
                ],
            ]);
        }

        return $this->render('user/index.html.twig', [
            'users' => $userItems,
            'filters' => [
                'q' => $query,
                'role' => $role,
                'sort' => $sortBy,
                'direction' => $direction,
            ],
            'pagination' => [
                'page' => (int) $pagination->getCurrentPageNumber(),
                'perPage' => $perPage,
                'totalItems' => $totalItems,
                'totalPages' => $totalPages,
            ],
        ]);
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();
        $user->setRoles(['ROLE_USER']);
        $form = $this->createForm(UserType::class, $user, [
            'password_required' => true,
            'role' => 'ROLE_USER',
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $plainPassword = (string) ($form->get('plainPassword')->getData() ?? '');
            if (!$this->isStrongPassword($plainPassword)) {
                $form->get('plainPassword')->addError(new FormError('Password must be at least 8 characters and include at least one letter and one number.'));
            }

            if ($form->isValid()) {
                $selectedRole = (string) $form->get('role')->getData();
                $user->setRoles([$selectedRole]);
                $user->setPassword($passwordHasher->hashPassword($user, $plainPassword));

                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', 'User created successfully.');

                return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $form = $this->createForm(UserType::class, $user, [
            'password_required' => false,
            'role' => $user->getPrimaryRole(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $plainPassword = (string) ($form->get('plainPassword')->getData() ?? '');
            if ('' !== $plainPassword && !$this->isStrongPassword($plainPassword)) {
                $form->get('plainPassword')->addError(new FormError('Password must be at least 8 characters and include at least one letter and one number.'));
            }

            if ($form->isValid()) {
                $selectedRole = (string) $form->get('role')->getData();
                $user->setRoles([$selectedRole]);

                if ('' !== $plainPassword) {
                    $user->setPassword($passwordHasher->hashPassword($user, $plainPassword));
                }

                $entityManager->flush();
                $this->addFlash('success', 'User updated successfully.');

                return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage): Response
    {
        if (!$this->isCsrfTokenValid('delete'.$user->getId(), $request->getPayload()->getString('_token'))) {
            $this->addFlash('error', 'Invalid CSRF token.');

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        $authenticated = $this->getUser();
        $isDeletingSelf = $authenticated instanceof User && $authenticated->getId() === $user->getId();

        $entityManager->remove($user);
        $entityManager->flush();

        if ($isDeletingSelf) {
            $tokenStorage->setToken(null);
            $request->getSession()->invalidate();

            return $this->redirectToRoute('app_home');
        }

        $this->addFlash('success', 'User deleted successfully.');

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }

    private function isStrongPassword(string $password): bool
    {
        return strlen($password) >= 8
            && (bool) preg_match('/[A-Za-z]/', $password)
            && (bool) preg_match('/\d/', $password);
    }
}
