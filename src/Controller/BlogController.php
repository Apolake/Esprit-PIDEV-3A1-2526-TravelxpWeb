<?php

namespace App\Controller;

use App\Entity\Blog;
use App\Entity\Comment;
use App\Entity\User;
use App\Form\BlogType;
use App\Form\CommentType;
use App\Repository\BlogRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class BlogController extends AbstractController
{
    #[Route('/blogs', name: 'blog_index', methods: ['GET'])]
    public function index(Request $request, BlogRepository $blogRepository): Response
    {
        $filters = [
            'q' => (string) $request->query->get('q', ''),
        ];

        $page = max(1, $request->query->getInt('page', 1));
        $perPage = 8;

        $qb = $blogRepository->createFilteredQueryBuilder($filters)
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage);

        $paginator = new Paginator($qb, true);
        $totalItems = count($paginator);
        $totalPages = max(1, (int) ceil($totalItems / $perPage));

        return $this->render('blog/index.html.twig', [
            'blogs' => iterator_to_array($paginator),
            'filters' => $filters,
            'pagination' => [
                'page' => $page,
                'perPage' => $perPage,
                'totalItems' => $totalItems,
                'totalPages' => $totalPages,
            ],
        ]);
    }

    #[Route('/blogs/new', name: 'blog_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, EntityManagerInterface $entityManager, #[CurrentUser] ?User $currentUser): Response
    {
        if (!$currentUser instanceof User) {
            throw $this->createAccessDeniedException('Authentication required.');
        }

        $blog = new Blog();
        $blog->setAuthor($currentUser);

        $form = $this->createForm(BlogType::class, $blog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($blog);
            $entityManager->flush();

            $this->addFlash('success', 'Blog post created successfully.');

            return $this->redirectToRoute('blog_show', ['id' => $blog->getId()]);
        }

        return $this->render('blog/new.html.twig', [
            'blog' => $blog,
            'form' => $form,
        ]);
    }

    #[Route('/blogs/{id}', name: 'blog_show', requirements: ['id' => '\\d+'], methods: ['GET'])]
    public function show(Blog $blog, CommentRepository $commentRepository): Response
    {
        $commentForm = $this->createForm(CommentType::class, new Comment(), [
            'action' => $this->generateUrl('comment_new', ['id' => $blog->getId()]),
            'method' => 'POST',
        ]);

        return $this->render('blog/show.html.twig', [
            'blog' => $blog,
            'comments' => $commentRepository->findForBlog($blog),
            'commentForm' => $commentForm,
            'canManageBlog' => $this->canManageBlog($blog),
        ]);
    }

    #[Route('/blogs/{id}/edit', name: 'blog_edit', requirements: ['id' => '\\d+'], methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, Blog $blog, EntityManagerInterface $entityManager): Response
    {
        if (!$this->canManageBlog($blog)) {
            throw $this->createAccessDeniedException('You cannot edit this blog post.');
        }

        $form = $this->createForm(BlogType::class, $blog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Blog post updated successfully.');

            return $this->redirectToRoute('blog_show', ['id' => $blog->getId()]);
        }

        return $this->render('blog/edit.html.twig', [
            'blog' => $blog,
            'form' => $form,
        ]);
    }

    #[Route('/blogs/{id}/delete', name: 'blog_delete', requirements: ['id' => '\\d+'], methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, Blog $blog, EntityManagerInterface $entityManager): Response
    {
        if (!$this->canManageBlog($blog)) {
            throw $this->createAccessDeniedException('You cannot delete this blog post.');
        }

        if (!$this->isCsrfTokenValid('delete_blog_' . $blog->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('danger', 'Invalid CSRF token.');

            return $this->redirectToRoute('blog_show', ['id' => $blog->getId()]);
        }

        $entityManager->remove($blog);
        $entityManager->flush();

        $this->addFlash('success', 'Blog post deleted successfully.');

        return $this->redirectToRoute('blog_index');
    }

    #[Route('/blogs/{id}/react/{type}', name: 'blog_react', requirements: ['id' => '\\d+', 'type' => 'like|dislike'], methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function react(Request $request, Blog $blog, string $type, EntityManagerInterface $entityManager, #[CurrentUser] ?User $currentUser): Response
    {
        if (!$currentUser instanceof User) {
            throw $this->createAccessDeniedException('Authentication required.');
        }

        if (!$this->isCsrfTokenValid('react_blog_' . $blog->getId() . '_' . $type, (string) $request->request->get('_token'))) {
            $this->addFlash('danger', 'Invalid CSRF token.');

            return $this->redirectToRoute('blog_show', ['id' => $blog->getId()]);
        }

        if ('like' === $type) {
            if ($blog->hasLikedBy($currentUser)) {
                $this->addFlash('info', 'You already liked this blog.');
            } else {
                $blog->addLikeBy($currentUser);
                $entityManager->flush();
                $this->addFlash('success', 'Like added.');
            }
        }

        if ('dislike' === $type) {
            if ($blog->hasDislikedBy($currentUser)) {
                $this->addFlash('info', 'You already disliked this blog.');
            } else {
                $blog->addDislikeBy($currentUser);
                $entityManager->flush();
                $this->addFlash('success', 'Dislike added.');
            }
        }

        return $this->redirectToRoute('blog_show', ['id' => $blog->getId()]);
    }

    private function canManageBlog(Blog $blog): bool
    {
        $currentUser = $this->getUser();
        if (!$currentUser instanceof User) {
            return false;
        }

        if ($this->isGranted('ROLE_ADMIN')) {
            return true;
        }

        return $blog->getAuthor()?->getId() === $currentUser->getId();
    }
}
