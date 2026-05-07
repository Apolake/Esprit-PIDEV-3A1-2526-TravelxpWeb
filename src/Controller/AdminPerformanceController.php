<?php

namespace App\Controller;

use App\Dev\DoctrinePerformanceProfiler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/performance')]
#[IsGranted('ROLE_ADMIN')]
class AdminPerformanceController extends AbstractController
{
    #[Route('/', name: 'app_admin_performance_index', methods: ['GET'])]
    public function index(Request $request, DoctrinePerformanceProfiler $profiler): Response
    {
        $limit = max(10, $request->query->getInt('limit', 100));

        return $this->render('admin_performance/index.html.twig', [
            'records' => $profiler->getPersistedRecords($limit),
            'summary' => $profiler->getSummary(),
            'limit' => $limit,
        ]);
    }

    #[Route('/clear', name: 'app_admin_performance_clear', methods: ['POST'])]
    public function clear(DoctrinePerformanceProfiler $profiler, Request $request): Response
    {
        if (!$this->isCsrfTokenValid('clear_performance_profiles', (string) $request->request->get('_token'))) {
            $this->addFlash('danger', 'Invalid CSRF token.');

            return $this->redirectToRoute('app_admin_performance_index');
        }

        $profiler->clear();
        $this->addFlash('success', 'Performance records cleared.');

        return $this->redirectToRoute('app_admin_performance_index');
    }
}