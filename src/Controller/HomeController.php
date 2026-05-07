<?php

namespace App\Controller;

use App\Entity\Activity;
use App\Entity\Trip;
use App\Entity\User;
use App\Repository\ActivityRepository;
use App\Repository\TripRepository;
use App\Repository\UserRepository;
use App\Service\AdminDashboardChartsService;
use App\Service\GamificationProgressService;
use App\Service\SchedulerRunStateService;
use Doctrine\ORM\EntityManagerInterface;
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

    #[Route('/portal', name: 'app_portal', methods: ['GET'])]
    public function portal(): Response
    {
        $authenticated = $this->getUser();
        $user = $authenticated instanceof User ? $authenticated : null;
        if ($user === null) {
            return $this->redirectToRoute('app_login');
        }

        if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            return $this->redirectToRoute('admin_dashboard');
        }

        return $this->redirectToRoute('trip_browse');
    }

    #[Route('/admin/dashboard', name: 'admin_dashboard', methods: ['GET'])]
    public function adminDashboard(
        UserRepository $userRepository,
        TripRepository $tripRepository,
        ActivityRepository $activityRepository,
        EntityManagerInterface $entityManager,
        AdminDashboardChartsService $adminDashboardChartsService,
        SchedulerRunStateService $schedulerRunStateService,
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $today = new \DateTimeImmutable('today');
        $connection = $entityManager->getConnection();

        $joinedTrips = (int) $connection->fetchOne('SELECT COUNT(*) FROM trip_participants');
        $joinedActivities = (int) $connection->fetchOne('SELECT COUNT(*) FROM trip_activity_participants');

        $upcomingTrips = (int) $tripRepository->createQueryBuilder('t')
            ->select('COUNT(t.id)')
            ->andWhere('t.startDate >= :today')
            ->setParameter('today', $today)
            ->getQuery()
            ->getSingleScalarResult();

        $completedTrips = (int) $tripRepository->createQueryBuilder('t')
            ->select('COUNT(t.id)')
            ->andWhere('t.status IN (:statuses)')
            ->setParameter('statuses', ['COMPLETED', 'DONE'])
            ->getQuery()
            ->getSingleScalarResult();

        $ongoingActivities = (int) $activityRepository->createQueryBuilder('a')
            ->select('COUNT(a.id)')
            ->andWhere('a.status = :status')
            ->setParameter('status', 'ONGOING')
            ->getQuery()
            ->getSingleScalarResult();

        $recentTrips = $tripRepository->createQueryBuilder('t')
            ->orderBy('t.createdAt', 'DESC')
            ->setMaxResults(6)
            ->getQuery()
            ->getResult();

        $recentActivities = $activityRepository->createQueryBuilder('a')
            ->leftJoin('a.trip', 't')->addSelect('t')
            ->orderBy('a.createdAt', 'DESC')
            ->setMaxResults(6)
            ->getQuery()
            ->getResult();

        return $this->render('admin/dashboard.html.twig', [
            'stats' => [
                'users' => $userRepository->count([]),
                'trips' => $tripRepository->count([]),
                'activities' => $activityRepository->count([]),
                'joinedTrips' => $joinedTrips,
                'joinedActivities' => $joinedActivities,
                'upcomingTrips' => $upcomingTrips,
                'completedTrips' => $completedTrips,
                'ongoingActivities' => $ongoingActivities,
            ],
            'charts' => $adminDashboardChartsService->buildCharts(),
            'schedulerJobs' => $schedulerRunStateService->getDashboardSnapshot(),
            'recentTrips' => array_filter($recentTrips, static fn ($trip): bool => $trip instanceof Trip),
            'recentActivities' => array_filter($recentActivities, static fn ($activity): bool => $activity instanceof Activity),
        ]);
    }

    #[Route('/admin', name: 'admin_portal', methods: ['GET'])]
    public function adminPortal(): Response
    {
        return $this->redirectToRoute('admin_dashboard');
    }

    #[Route('/user', name: 'user_portal', methods: ['GET'])]
    public function userPortal(): Response
    {
        return $this->redirectToRoute('trip_browse');
    }
}
