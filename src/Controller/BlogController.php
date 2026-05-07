<?php

namespace App\Controller;

use App\Entity\Blog;
use App\Entity\Comment;
use App\Entity\User;
use App\Form\BlogType;
use App\Form\CommentType;
use App\Repository\BlogRepository;
use App\Repository\CommentRepository;
use App\Service\AiSummarizerService;
use App\Service\CommentSentimentService;
use App\Service\GrammarService;
use App\Service\ProfanityFilterService;
use App\Service\ReadTimeEstimatorService;
use App\Service\TranslationService;
use Dompdf\Dompdf;
use Dompdf\Options;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class BlogController extends AbstractController
{
    #[Route('/blogs', name: 'blog_index', methods: ['GET'])]
    public function index(Request $request, BlogRepository $blogRepository, ReadTimeEstimatorService $readTimeEstimator): Response
    {
        $filters = [
            'q' => (string) $request->query->get('q', ''),
            'sort' => (string) $request->query->get('sort', 'latest'),
            'authorId' => (string) $request->query->get('authorId', ''),
            'fromDate' => (string) $request->query->get('fromDate', ''),
            'toDate' => (string) $request->query->get('toDate', ''),
        ];

        $page = max(1, $request->query->getInt('page', 1));
        $perPage = 8;

        $qb = $blogRepository->createFilteredQueryBuilder($filters)
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage);

        $paginator = new Paginator($qb, true);
        $totalItems = count($paginator);
        $totalPages = max(1, (int) ceil($totalItems / $perPage));
        $blogs = iterator_to_array($paginator);

        $readTimes = [];
        $blogIds = [];
        foreach ($blogs as $blog) {
            if (!$blog instanceof Blog || $blog->getId() === null) {
                continue;
            }

            $id = $blog->getId();
            $blogIds[] = $id;
            $readTimes[$id] = $readTimeEstimator->estimateMinutes($blog->getContent());
        }

        return $this->render('blog/index.html.twig', [
            'blogs' => $blogs,
            'filters' => $filters,
            'authors' => $blogRepository->getAuthorsForFilter(),
            'readTimes' => $readTimes,
            'reactionCounts' => $blogRepository->getCountsForBlogIds($blogIds),
            'pagination' => [
                'page' => $page,
                'perPage' => $perPage,
                'totalItems' => $totalItems,
                'totalPages' => $totalPages,
            ],
        ]);
    }

    #[Route('/blogs/suggest', name: 'blog_suggest', methods: ['GET'])]
    public function suggest(Request $request, BlogRepository $blogRepository): JsonResponse
    {
        $q = trim((string) $request->query->get('q', ''));
        if ($q === '' || mb_strlen($q) < 2) {
            return $this->json([
                'suggestions' => [],
            ]);
        }

        return $this->json([
            'suggestions' => $blogRepository->findLiveSuggestions($q),
        ]);
    }

    #[Route('/blogs/new', name: 'blog_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, EntityManagerInterface $entityManager, ProfanityFilterService $profanityFilterService, #[CurrentUser] ?User $currentUser): Response
    {
        if (!$currentUser instanceof User) {
            throw $this->createAccessDeniedException('Authentication required.');
        }

        $blog = new Blog();
        $blog->setAuthor($currentUser);

        $form = $this->createForm(BlogType::class, $blog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $blog->setTitle($profanityFilterService->sanitize((string) $blog->getTitle()));
            $blog->setContent($profanityFilterService->sanitize((string) $blog->getContent()));
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
    public function show(Blog $blog, Request $request, CommentRepository $commentRepository, ReadTimeEstimatorService $readTimeEstimator, CommentSentimentService $commentSentimentService, BlogRepository $blogRepository): Response
    {
        $commentFilters = [
            'sort' => (string) $request->query->get('commentSort', 'latest'),
            'authorId' => (string) $request->query->get('commentAuthorId', ''),
            'fromDate' => (string) $request->query->get('commentFromDate', ''),
            'toDate' => (string) $request->query->get('commentToDate', ''),
        ];

        $commentForm = $this->createForm(CommentType::class, new Comment(), [
            'action' => $this->generateUrl('comment_new', ['id' => $blog->getId()]),
            'method' => 'POST',
        ]);

        $comments = $commentRepository->findForBlog($blog, $commentFilters);
        $sentiments = [];
        $commentIds = [];
        foreach ($comments as $comment) {
            if (!$comment instanceof Comment || $comment->getId() === null) {
                continue;
            }

            $id = $comment->getId();
            $commentIds[] = $id;
            $sentiments[$id] = $commentSentimentService->detect($comment);
        }

        $blogId = $blog->getId() ?? 0;
        $blogReactionCounts = $blogRepository->getCountsForBlogIds([$blogId]);

        return $this->render('blog/show.html.twig', [
            'blog' => $blog,
            'comments' => $comments,
            'commentForm' => $commentForm,
            'commentFilters' => $commentFilters,
            'commentAuthors' => $commentRepository->getAuthorsForBlog($blog),
            'sentiments' => $sentiments,
            'readTime' => $readTimeEstimator->estimateMinutes($blog->getContent()),
            'commentReactions' => $commentRepository->getReactionCountsForCommentIds($commentIds),
            'blogReaction' => $blogReactionCounts[$blogId] ?? ['likes' => 0, 'dislikes' => 0, 'comments' => 0],
            'canManageBlog' => $this->canManageBlog($blog),
        ]);
    }

    #[Route('/blogs/{id}/edit', name: 'blog_edit', requirements: ['id' => '\\d+'], methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, Blog $blog, EntityManagerInterface $entityManager, ProfanityFilterService $profanityFilterService): Response
    {
        if (!$this->canManageBlog($blog)) {
            throw $this->createAccessDeniedException('You cannot edit this blog post.');
        }

        $form = $this->createForm(BlogType::class, $blog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $blog->setTitle($profanityFilterService->sanitize((string) $blog->getTitle()));
            $blog->setContent($profanityFilterService->sanitize((string) $blog->getContent()));
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

    #[Route('/blogs/{id}/export/pdf', name: 'blog_export_pdf', requirements: ['id' => '\\d+'], methods: ['GET'])]
    public function exportBlogPdf(Blog $blog, ReadTimeEstimatorService $readTimeEstimator, BlogRepository $blogRepository): Response
    {
        $blogId = $blog->getId() ?? 0;
        $blogReactionCounts = $blogRepository->getCountsForBlogIds([$blogId]);

        $html = $this->renderView('blog/pdf_blog.html.twig', [
            'blog' => $blog,
            'readTime' => $readTimeEstimator->estimateMinutes($blog->getContent()),
            'generatedAt' => new \DateTimeImmutable(),
            'blogReaction' => $blogReactionCounts[$blogId] ?? ['likes' => 0, 'dislikes' => 0, 'comments' => 0],
        ]);

        $dompdf = new Dompdf(new Options([
            'defaultFont' => 'DejaVu Sans',
            'isRemoteEnabled' => true,
        ]));
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return new Response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="blog-' . ($blog->getId() ?? 'export') . '.pdf"',
        ]);
    }

    #[Route('/blogs/{id}/comments/export/pdf', name: 'blog_comments_export_pdf', requirements: ['id' => '\\d+'], methods: ['GET'])]
    public function exportBlogCommentsPdf(Blog $blog, CommentRepository $commentRepository): Response
    {
        $comments = $commentRepository->findForBlog($blog, ['sort' => 'latest']);
        $commentIds = [];
        foreach ($comments as $comment) {
            if ($comment->getId() !== null) {
                $commentIds[] = $comment->getId();
            }
        }

        $html = $this->renderView('blog/pdf_comments.html.twig', [
            'blog' => $blog,
            'comments' => $comments,
            'generatedAt' => new \DateTimeImmutable(),
            'commentReactions' => $commentRepository->getReactionCountsForCommentIds($commentIds),
        ]);

        $dompdf = new Dompdf(new Options([
            'defaultFont' => 'DejaVu Sans',
            'isRemoteEnabled' => true,
        ]));
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return new Response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="blog-comments-' . ($blog->getId() ?? 'export') . '.pdf"',
        ]);
    }

    #[Route('/blogs/tools/grammar', name: 'blog_tool_grammar', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function grammarTool(Request $request, GrammarService $grammarService): JsonResponse
    {
        if (!$this->isCsrfTokenValid('blog_grammar_tool', (string) $request->request->get('_token'))) {
            return $this->json(['message' => 'Invalid CSRF token.'], Response::HTTP_FORBIDDEN);
        }

        $text = (string) $request->request->get('text', '');
        $language = (string) $request->request->get('language', 'en-US');
        $result = $grammarService->correct($text, $language);

        return $this->json($result);
    }

    #[Route('/blogs/{id}/translate', name: 'blog_translate', requirements: ['id' => '\\d+'], methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function translateBlog(Blog $blog, Request $request, TranslationService $translationService): JsonResponse
    {
        if (!$this->isCsrfTokenValid('blog_translate_' . $blog->getId(), (string) $request->request->get('_token'))) {
            return $this->json(['message' => 'Invalid CSRF token.'], Response::HTTP_FORBIDDEN);
        }

        $target = (string) $request->request->get('target', 'en');
        $result = $translationService->translate((string) $blog->getContent(), $target, 'auto');

        return $this->json([
            'originalText' => (string) $blog->getContent(),
            'translatedText' => $result['translatedText'],
            'targetLanguage' => strtolower($target),
            'provider' => $result['provider'],
            'error' => $result['error'],
        ]);
    }

    #[Route('/blogs/{id}/ai/summarize', name: 'blog_ai_summarize', requirements: ['id' => '\\d+'], methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function summarize(Blog $blog, Request $request, AiSummarizerService $aiSummarizerService): JsonResponse
    {
        if (!$this->isCsrfTokenValid('blog_ai_summarize_' . $blog->getId(), (string) $request->request->get('_token'))) {
            return $this->json(['message' => 'Invalid CSRF token.'], Response::HTTP_FORBIDDEN);
        }

        $result = $aiSummarizerService->summarize((string) $blog->getContent());

        return $this->json($result);
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
