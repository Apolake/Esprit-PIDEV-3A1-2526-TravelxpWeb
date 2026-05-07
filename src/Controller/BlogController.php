<?php

namespace App\Controller;

use App\Entity\Blog;
use App\Entity\Comment;
use App\Entity\User;
use App\Dev\DoctrinePerformanceProfiler;
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
    public function index(Request $request, BlogRepository $blogRepository, ReadTimeEstimatorService $readTimeEstimator, DoctrinePerformanceProfiler $doctrinePerformanceProfiler): Response
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
        $listingResult = $doctrinePerformanceProfiler->profileEntity('Blog', null, 'index_listing', static function () use ($paginator): array {
            return [
                'totalItems' => count($paginator),
                'blogs' => iterator_to_array($paginator),
            ];
        });

        $totalItems = $listingResult['totalItems'];
        $blogs = $listingResult['blogs'];
        $totalPages = max(1, (int) ceil($totalItems / $perPage));

        $readTimes = [];
        $blogIds = [];
        foreach ($blogs as $blog) {
            if (!$blog instanceof Blog || $blog->getId() === null) {
                continue;
            }

            $id = $blog->getId();
            $blogIds[] = $id;
            $readTimes[$id] = $doctrinePerformanceProfiler->profileEntity('Blog', $id, 'estimate_read_time', static fn () => $readTimeEstimator->estimateMinutes($blog->getContent()));
        }

        // Batch-fetch likes/dislikes/comments counts to avoid per-entity COUNT queries
        $reactionCounts = $doctrinePerformanceProfiler->profileEntity('Blog', null, 'batch_reaction_counts', static fn () => $blogRepository->getCountsForBlogIds($blogIds));

        return $this->render('blog/index.html.twig', [
            'blogs' => $blogs,
            'filters' => $filters,
            'authors' => $blogRepository->getAuthorsForFilter(),
            'readTimes' => $readTimes,
            'reactionCounts' => $reactionCounts,
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
    public function show(Blog $blog, Request $request, CommentRepository $commentRepository, ReadTimeEstimatorService $readTimeEstimator, CommentSentimentService $commentSentimentService, BlogRepository $blogRepository, DoctrinePerformanceProfiler $doctrinePerformanceProfiler): Response
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

        $comments = $doctrinePerformanceProfiler->profileEntity('Comment', $blog->getId(), 'find_for_blog', static fn () => $commentRepository->findForBlog($blog, $commentFilters));
        $sentiments = [];
        $commentIds = [];
        foreach ($comments as $comment) {
            if ($comment->getId() === null) {
                continue;
            }

            $cid = $comment->getId();
            $commentIds[] = $cid;
            $sentiments[$cid] = $doctrinePerformanceProfiler->profileEntity('Comment', $cid, 'detect_sentiment', static fn () => $commentSentimentService->detect($comment));
        }

        // Batch reaction counts for comments
        $commentReactions = $doctrinePerformanceProfiler->profileEntity('Comment', $blog->getId(), 'batch_reaction_counts', static fn () => $commentRepository->getReactionCountsForCommentIds($commentIds));
        $blogReaction = $doctrinePerformanceProfiler->profileEntity('Blog', $blog->getId(), 'batch_reaction_counts', static fn () => $blogRepository->getCountsForBlogIds([$blog->getId() ?? 0]));
        $blogReaction = $blogReaction[$blog->getId() ?? 0] ?? ['likes' => 0, 'dislikes' => 0, 'comments' => 0];

        return $this->render('blog/show.html.twig', [
            'blog' => $blog,
            'comments' => $comments,
            'commentForm' => $commentForm,
            'commentFilters' => $commentFilters,
            'commentAuthors' => $commentRepository->getAuthorsForBlog($blog),
            'sentiments' => $sentiments,
            'readTime' => $readTimeEstimator->estimateMinutes($blog->getContent()),
            'commentReactions' => $commentReactions,
            'blogReaction' => $blogReaction,
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
    public function exportBlogPdf(Blog $blog, ReadTimeEstimatorService $readTimeEstimator, \App\Repository\BlogRepository $blogRepository, DoctrinePerformanceProfiler $doctrinePerformanceProfiler): Response
    {
        $readTime = $doctrinePerformanceProfiler->profileEntity('Blog', $blog->getId(), 'export_pdf_read_time', static fn () => $readTimeEstimator->estimateMinutes($blog->getContent()));
        $blogReaction = $doctrinePerformanceProfiler->profileEntity('Blog', $blog->getId(), 'export_pdf_reaction_counts', static fn () => $blogRepository->getCountsForBlogIds([$blog->getId() ?? 0]));
        $blogReaction = $blogReaction[$blog->getId() ?? 0] ?? ['likes' => 0, 'dislikes' => 0, 'comments' => 0];

        $html = $this->renderView('blog/pdf_blog.html.twig', [
            'blog' => $blog,
            'readTime' => $readTime,
            'generatedAt' => new \DateTimeImmutable(),
            'blogReaction' => $blogReaction,
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
    public function exportBlogCommentsPdf(Blog $blog, CommentRepository $commentRepository, DoctrinePerformanceProfiler $doctrinePerformanceProfiler): Response
    {
        $comments = $doctrinePerformanceProfiler->profileEntity('Comment', $blog->getId(), 'export_comments_query', static fn () => $commentRepository->findForBlog($blog, ['sort' => 'latest']));
        $commentIds = array_map(fn($c) => $c->getId(), $comments);
        $commentReactions = $doctrinePerformanceProfiler->profileEntity('Comment', $blog->getId(), 'export_comments_reaction_counts', static fn () => $commentRepository->getReactionCountsForCommentIds($commentIds));

        $html = $this->renderView('blog/pdf_comments.html.twig', [
            'blog' => $blog,
            'comments' => $comments,
            'generatedAt' => new \DateTimeImmutable(),
            'commentReactions' => $commentReactions,
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
//-------------------------------------------------- reined logic by ai proofreading 

<?php

namespace App\Controller;

use App\Entity\Activity;
use App\Entity\User;
use App\Form\ActivityType;
use App\Repository\ActivityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ActivityController extends AbstractController
{
    #[Route('/activities', name: 'activity_index', methods: ['GET'])]
    #[Route('/admin/activities', name: 'admin_activity_index', methods: ['GET'])]
    public function index(Request $request, ActivityRepository $activityRepository): Response
    {
        $isAdmin = str_starts_with((string) $request->attributes->get('_route'), 'admin_');
        $viewer = $this->getUser();
        $currentUser = $viewer instanceof User ? $viewer : null;

        $filters = [
            'q' => (string) $request->query->get('q', ''),
            'status' => (string) $request->query->get('status', ''),
            'type' => (string) $request->query->get('type', ''),
            'tripId' => (string) $request->query->get('tripId', ''),
            'myActivities' => (string) $request->query->get('myActivities', ''),
            'sort' => (string) $request->query->get('sort', 'newest'),
        ];

        $page = max(1, $request->query->getInt('page', 1));
        $perPage = $isAdmin ? 10 : 9;
        $qb = $activityRepository->createFilteredQueryBuilder($filters, $isAdmin, $currentUser);
        $qb->setFirstResult(($page - 1) * $perPage)->setMaxResults($perPage);

        $paginator = new Paginator($qb, true);
        $totalItems = count($paginator);
        $totalPages = max(1, (int) ceil($totalItems / $perPage));

        return $this->render('activity/index.html.twig', [
            'isAdmin' => $isAdmin,
            'activities' => iterator_to_array($paginator),
            'filters' => $filters,
            'types' => $activityRepository->getDistinctTypes(),
            'trips' => $activityRepository->getTripsForFilter(),
            'joinedActivityIds' => $currentUser ? $activityRepository->findJoinedActivityIdsForUser($currentUser) : [],
            'pagination' => [
                'page' => $page,
                'perPage' => $perPage,
                'totalItems' => $totalItems,
                'totalPages' => $totalPages,
            ],
        ]);
    }

    #[Route('/admin/activities/new', name: 'admin_activity_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $activity = new Activity();
        $form = $this->createForm(ActivityType::class, $activity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($activity);
            $entityManager->flush();
            $this->addFlash('success', 'Activity created successfully.');

            return $this->redirectToRoute('admin_activity_index');
        }

        return $this->render('activity/new.html.twig', [
            'isAdmin' => true,
            'activity' => $activity,
            'form' => $form,
        ]);
    }

    #[Route('/activities/{id}', name: 'activity_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    #[Route('/admin/activities/{id}', name: 'admin_activity_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(Request $request, Activity $activity): Response
    {
        $isAdmin = str_starts_with((string) $request->attributes->get('_route'), 'admin_');
        $viewer = $this->getUser();
        $currentUser = $viewer instanceof User ? $viewer : null;

        return $this->render('activity/show.html.twig', [
            'isAdmin' => $isAdmin,
            'activity' => $activity,
            'isJoined' => $currentUser ? $activity->isParticipant($currentUser) : false,
            'canJoinTrip' => $currentUser && $activity->getTrip() ? $activity->getTrip()->isParticipant($currentUser) : true,
        ]);
    }

    #[Route('/admin/activities/{id}/edit', name: 'admin_activity_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, Activity $activity, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ActivityType::class, $activity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Activity updated successfully.');

            return $this->redirectToRoute('admin_activity_index');
        }

        return $this->render('activity/edit.html.twig', [
            'isAdmin' => true,
            'activity' => $activity,
            'form' => $form,
        ]);
    }

    #[Route('/admin/activities/{id}/delete', name: 'admin_activity_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Activity $activity, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isCsrfTokenValid('delete_activity_' . $activity->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('danger', 'Invalid CSRF token.');

            return $this->redirectToRoute('admin_activity_index');
        }

        $entityManager->remove($activity);
        $entityManager->flush();
        $this->addFlash('success', 'Activity deleted successfully.');

        return $this->redirectToRoute('admin_activity_index');
    }

    #[Route('/activities/{id}/join', name: 'activity_join', requirements: ['id' => '\d+'], methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function join(Request $request, Activity $activity, EntityManagerInterface $entityManager): Response
    {
        $currentUser = $this->getUser();
        if (!$currentUser instanceof User) {
            throw $this->createAccessDeniedException('Authentication required.');
        }

        if (!$this->isCsrfTokenValid('join_activity_' . $activity->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('danger', 'Invalid CSRF token.');

            return $this->redirectToRoute('activity_index');
        }

        if ($activity->getTrip() !== null && !$activity->getTrip()->isParticipant($currentUser)) {
            $this->addFlash('warning', 'Join the related trip first.');

            return $this->redirectToRoute('activity_index');
        }

        if (!$activity->isParticipant($currentUser)) {
            $activity->addParticipant($currentUser);
            $entityManager->flush();
            $this->addFlash('success', 'You joined this activity.');
        } else {
            $this->addFlash('info', 'You are already participating in this activity.');
        }

        return $this->redirectToRoute('activity_index');
    }

    #[Route('/activities/{id}/leave', name: 'activity_leave', requirements: ['id' => '\d+'], methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function leave(Request $request, Activity $activity, EntityManagerInterface $entityManager): Response
    {
        $currentUser = $this->getUser();
        if (!$currentUser instanceof User) {
            throw $this->createAccessDeniedException('Authentication required.');
        }

        if (!$this->isCsrfTokenValid('leave_activity_' . $activity->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('danger', 'Invalid CSRF token.');

            return $this->redirectToRoute('activity_index');
        }

        if ($activity->isParticipant($currentUser)) {
            $activity->removeParticipant($currentUser);
            $entityManager->flush();
            $this->addFlash('success', 'You left this activity.');
        } else {
            $this->addFlash('info', 'You are not participating in this activity.');
        }

        return $this->redirectToRoute('activity_index');
    }
}
//commited changes on this file to complete fix integration logic error 