<?php

namespace App\Controller;

use App\Entity\Blog;
use App\Entity\Comment;
use App\Entity\User;
use App\Form\CommentType;
use App\Service\GrammarService;
use App\Service\ProfanityFilterService;
use App\Service\TranslationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class CommentController extends AbstractController
{
    #[Route('/blogs/{id}/comments/new', name: 'comment_new', requirements: ['id' => '\\d+'], methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, Blog $blog, EntityManagerInterface $entityManager, ProfanityFilterService $profanityFilterService, #[CurrentUser] ?User $currentUser): Response
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

        $comment->setContent($profanityFilterService->sanitize((string) $comment->getContent()));

        $entityManager->persist($comment);
        $entityManager->flush();

        $this->addFlash('success', 'Comment added.');

        return $this->redirectToRoute('blog_show', ['id' => $blog->getId()]);
    }

    #[Route('/comments/{id}/edit', name: 'comment_edit', requirements: ['id' => '\\d+'], methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, Comment $comment, EntityManagerInterface $entityManager, ProfanityFilterService $profanityFilterService): Response
    {
        if (!$this->canManageComment($comment)) {
            throw $this->createAccessDeniedException('You cannot edit this comment.');
        }

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setContent($profanityFilterService->sanitize((string) $comment->getContent()));
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

    #[Route('/comments/tools/grammar', name: 'comment_tool_grammar', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function grammarTool(Request $request, GrammarService $grammarService): JsonResponse
    {
        if (!$this->isCsrfTokenValid('comment_grammar_tool', (string) $request->request->get('_token'))) {
            return $this->json(['message' => 'Invalid CSRF token.'], Response::HTTP_FORBIDDEN);
        }

        $text = (string) $request->request->get('text', '');
        $language = (string) $request->request->get('language', 'en-US');

        return $this->json($grammarService->correct($text, $language));
    }

    #[Route('/comments/{id}/translate', name: 'comment_translate', requirements: ['id' => '\\d+'], methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function translateComment(Comment $comment, Request $request, TranslationService $translationService): JsonResponse
    {
        if (!$this->isCsrfTokenValid('comment_translate_' . $comment->getId(), (string) $request->request->get('_token'))) {
            return $this->json(['message' => 'Invalid CSRF token.'], Response::HTTP_FORBIDDEN);
        }

        $target = (string) $request->request->get('target', 'en');
        $result = $translationService->translate((string) $comment->getContent(), $target, 'auto');

        return $this->json([
            'originalText' => (string) $comment->getContent(),
            'translatedText' => $result['translatedText'],
            'targetLanguage' => strtolower($target),
            'provider' => $result['provider'],
            'error' => $result['error'],
        ]);
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
