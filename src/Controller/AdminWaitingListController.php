<?php

namespace App\Controller;

use App\Entity\ActivityWaitingListEntry;
use App\Entity\TripWaitingListEntry;
use App\Repository\ActivityWaitingListEntryRepository;
use App\Repository\TripWaitingListEntryRepository;
use App\Service\AdminWaitingListService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/waiting-lists')]
#[IsGranted('ROLE_ADMIN')]
class AdminWaitingListController extends AbstractController
{
    #[Route('', name: 'admin_waiting_list_index', methods: ['GET'])]
    public function index(
        Request $request,
        TripWaitingListEntryRepository $tripWaitingListEntryRepository,
        ActivityWaitingListEntryRepository $activityWaitingListEntryRepository,
        PaginatorInterface $paginator,
    ): Response {
        $scope = strtolower((string) $request->query->get('scope', 'all'));
        if (!in_array($scope, ['all', 'trip', 'activity'], true)) {
            $scope = 'all';
        }

        $status = strtoupper(trim((string) $request->query->get('status', 'WAITING')));
        $allowedStatuses = [
            'WAITING',
            'PROMOTED',
            'REJECTED',
            'EXPIRED',
            'CANCELLED',
            '',
        ];
        if (!in_array($status, $allowedStatuses, true)) {
            $status = 'WAITING';
        }

        $page = max(1, $request->query->getInt('page', 1));
        $perPage = 12;

        $tripEntries = [];
        $activityEntries = [];
        $tripPagination = null;
        $activityPagination = null;

        if ($scope === 'all' || $scope === 'trip') {
            $tripQb = $tripWaitingListEntryRepository->createAdminQueryBuilder($status);
            $tripKnpPagination = $paginator->paginate($tripQb, $page, $perPage, [
                'distinct' => true,
            ]);
            $tripItems = $tripKnpPagination->getItems();
            if ($tripItems instanceof \Traversable) {
                $tripItems = iterator_to_array($tripItems);
            }
            $tripEntries = is_array($tripItems) ? $tripItems : [];
            $tripTotalItems = (int) $tripKnpPagination->getTotalItemCount();
            $tripPagination = [
                'page' => (int) $tripKnpPagination->getCurrentPageNumber(),
                'perPage' => $perPage,
                'totalItems' => $tripTotalItems,
                'totalPages' => max(1, (int) ceil($tripTotalItems / $perPage)),
            ];
        }

        if ($scope === 'all' || $scope === 'activity') {
            $activityQb = $activityWaitingListEntryRepository->createAdminQueryBuilder($status);
            $activityKnpPagination = $paginator->paginate($activityQb, $page, $perPage, [
                'distinct' => true,
            ]);
            $activityItems = $activityKnpPagination->getItems();
            if ($activityItems instanceof \Traversable) {
                $activityItems = iterator_to_array($activityItems);
            }
            $activityEntries = is_array($activityItems) ? $activityItems : [];
            $activityTotalItems = (int) $activityKnpPagination->getTotalItemCount();
            $activityPagination = [
                'page' => (int) $activityKnpPagination->getCurrentPageNumber(),
                'perPage' => $perPage,
                'totalItems' => $activityTotalItems,
                'totalPages' => max(1, (int) ceil($activityTotalItems / $perPage)),
            ];
        }

        return $this->render('admin/waiting_list/index.html.twig', [
            'scope' => $scope,
            'status' => $status,
            'tripEntries' => $tripEntries,
            'activityEntries' => $activityEntries,
            'tripPagination' => $tripPagination,
            'activityPagination' => $activityPagination,
        ]);
    }

    #[Route('/trip/{id}/accept', name: 'admin_waiting_list_trip_accept', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function acceptTrip(
        Request $request,
        TripWaitingListEntry $entry,
        AdminWaitingListService $adminWaitingListService,
    ): Response {
        if (!$this->isCsrfTokenValid('admin_wait_trip_accept_' . $entry->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('danger', 'Invalid CSRF token.');

            return $this->redirectToRoute('admin_waiting_list_index');
        }

        $result = $adminWaitingListService->acceptTripEntry($entry);
        $this->addFlash($result->toFlashType(), $result->getMessage());

        return $this->redirectToRoute('admin_waiting_list_index', $this->keepFilters($request));
    }

    #[Route('/trip/{id}/reject', name: 'admin_waiting_list_trip_reject', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function rejectTrip(
        Request $request,
        TripWaitingListEntry $entry,
        AdminWaitingListService $adminWaitingListService,
    ): Response {
        if (!$this->isCsrfTokenValid('admin_wait_trip_reject_' . $entry->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('danger', 'Invalid CSRF token.');

            return $this->redirectToRoute('admin_waiting_list_index');
        }

        $result = $adminWaitingListService->rejectTripEntry($entry);
        $this->addFlash($result->toFlashType(), $result->getMessage());

        return $this->redirectToRoute('admin_waiting_list_index', $this->keepFilters($request));
    }

    #[Route('/activity/{id}/accept', name: 'admin_waiting_list_activity_accept', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function acceptActivity(
        Request $request,
        ActivityWaitingListEntry $entry,
        AdminWaitingListService $adminWaitingListService,
    ): Response {
        if (!$this->isCsrfTokenValid('admin_wait_activity_accept_' . $entry->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('danger', 'Invalid CSRF token.');

            return $this->redirectToRoute('admin_waiting_list_index');
        }

        $result = $adminWaitingListService->acceptActivityEntry($entry);
        $this->addFlash($result->toFlashType(), $result->getMessage());

        return $this->redirectToRoute('admin_waiting_list_index', $this->keepFilters($request));
    }

    #[Route('/activity/{id}/reject', name: 'admin_waiting_list_activity_reject', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function rejectActivity(
        Request $request,
        ActivityWaitingListEntry $entry,
        AdminWaitingListService $adminWaitingListService,
    ): Response {
        if (!$this->isCsrfTokenValid('admin_wait_activity_reject_' . $entry->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('danger', 'Invalid CSRF token.');

            return $this->redirectToRoute('admin_waiting_list_index');
        }

        $result = $adminWaitingListService->rejectActivityEntry($entry);
        $this->addFlash($result->toFlashType(), $result->getMessage());

        return $this->redirectToRoute('admin_waiting_list_index', $this->keepFilters($request));
    }

    /**
     * @return array{scope?: string, status?: string, page?: int}
     */
    private function keepFilters(Request $request): array
    {
        $scope = (string) $request->request->get('scope', '');
        $status = (string) $request->request->get('status', '');
        $page = (int) $request->request->get('page', 1);

        $params = [];
        if ($scope !== '') {
            $params['scope'] = $scope;
        }
        if ($status !== '') {
            $params['status'] = $status;
        }
        if ($page > 1) {
            $params['page'] = $page;
        }

        return $params;
    }
}
