<?php

namespace App\Controller;

use App\Entity\Blog;
use App\Entity\Comment;
use App\Entity\User;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class CommentController extends AbstractController
{
    #[Route('/blogs/{id}/comments/new', name: 'comment_new', requirements: ['id' => '\\d+'], methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, Blog $blog, EntityManagerInterface $entityManager, #[CurrentUser] ?User $currentUser): Response
    {
        if (!$currentUser instanceof User) {
            throw $this->createAccessDeniedException('Authentication required.');
        }

        $comment = new Comment();
        $comment->setBlog($blog);
        $comment->setAuthor($currentUser);

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            $this->addFlash('danger', 'Unable to add comment. Please check the form fields.');

            return $this->redirectToRoute('blog_show', ['id' => $blog->getId()]);
        }

        $entityManager->persist($comment);
        $entityManager->flush();

        $this->addFlash('success', 'Comment added.');

        return $this->redirectToRoute('blog_show', ['id' => $blog->getId()]);
    }

    #[Route('/comments/{id}/edit', name: 'comment_edit', requirements: ['id' => '\\d+'], methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, Comment $comment, EntityManagerInterface $entityManager): Response
    {
        if (!$this->canManageComment($comment)) {
            throw $this->createAccessDeniedException('You cannot edit this comment.');
        }

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Comment updated.');

            return $this->redirectToRoute('blog_show', ['id' => $comment->getBlog()?->getId()]);
        }

        return $this->render('comment/edit.html.twig', [
            'comment' => $comment,
            'form' => $form,
        ]);
    }

    #[Route('/comments/{id}/delete', name: 'comment_delete', requirements: ['id' => '\\d+'], methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, Comment $comment, EntityManagerInterface $entityManager): Response
    {
        if (!$this->canManageComment($comment)) {
            throw $this->createAccessDeniedException('You cannot delete this comment.');
        }

        if (!$this->isCsrfTokenValid('delete_comment_' . $comment->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('danger', 'Invalid CSRF token.');

            return $this->redirectToRoute('blog_show', ['id' => $comment->getBlog()?->getId()]);
        }

        $blogId = $comment->getBlog()?->getId();

        $entityManager->remove($comment);
        $entityManager->flush();

        $this->addFlash('success', 'Comment deleted.');

        return $this->redirectToRoute('blog_show', ['id' => $blogId]);
    }

    #[Route('/comments/{id}/react/{type}', name: 'comment_react', requirements: ['id' => '\\d+', 'type' => 'like|dislike'], methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function react(Request $request, Comment $comment, string $type, EntityManagerInterface $entityManager, #[CurrentUser] ?User $currentUser): Response
    {
        if (!$currentUser instanceof User) {
            throw $this->createAccessDeniedException('Authentication required.');
        }

        if (!$this->isCsrfTokenValid('react_comment_' . $comment->getId() . '_' . $type, (string) $request->request->get('_token'))) {
            $this->addFlash('danger', 'Invalid CSRF token.');

            return $this->redirectToRoute('blog_show', ['id' => $comment->getBlog()?->getId()]);
        }

        if ('like' === $type) {
            if ($comment->hasLikedBy($currentUser)) {
                $this->addFlash('info', 'You already liked this comment.');
            } else {
                $comment->addLikeBy($currentUser);
                $entityManager->flush();
                $this->addFlash('success', 'Like added.');
            }
        }

        if ('dislike' === $type) {
            if ($comment->hasDislikedBy($currentUser)) {
                $this->addFlash('info', 'You already disliked this comment.');
            } else {
                $comment->addDislikeBy($currentUser);
                $entityManager->flush();
                $this->addFlash('success', 'Dislike added.');
            }
        }

        return $this->redirectToRoute('blog_show', ['id' => $comment->getBlog()?->getId()]);
    }

    private function canManageComment(Comment $comment): bool
    {
        $currentUser = $this->getUser();
        if (!$currentUser instanceof User) {
            return false;
        }

        if ($this->isGranted('ROLE_ADMIN')) {
            return true;
        }

        return $comment->getAuthor()?->getId() === $currentUser->getId();
    }
}
