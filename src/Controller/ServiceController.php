<?php

namespace App\Controller;

use App\Entity\Service;
use App\Form\ServiceType;
use App\Repository\ServiceRepository;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ServiceController extends AbstractController
{
    #[Route('/admin/services', name: 'admin_service_index', methods: ['GET'])]
    public function adminIndex(Request $request, ServiceRepository $serviceRepository, PaginatorInterface $paginator): Response
    {
        $filters = $this->extractFilters($request);
        $qb = $serviceRepository->createFilteredQueryBuilder($filters);
        $pagination = $paginator->paginate($qb, $request->query->getInt('page', 1), 10);

        return $this->render('service/admin/index.html.twig', [
            'pagination' => $pagination,
            'filters' => $filters,
            'serviceTypes' => $serviceRepository->getDistinctServiceTypes(),
        ]);
    }

    #[Route('/admin/services/new', name: 'admin_service_new', methods: ['GET', 'POST'])]
    public function adminNew(Request $request, EntityManagerInterface $entityManager): Response
    {
        $service = new Service();
        $service->setCreatedAt(new \DateTime());

        $form = $this->createForm(ServiceType::class, $service);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($service);
            $entityManager->flush();
            $this->addFlash('success', 'Service created successfully.');

            return $this->redirectToRoute('admin_service_index');
        }

        return $this->render('service/admin/new.html.twig', [
            'form' => $form,
            'service' => $service,
        ]);
    }

    #[Route('/admin/services/{id}', name: 'admin_service_show', requirements: ['id' => '\\d+'], methods: ['GET'])]
    public function adminShow(Service $service): Response
    {
        return $this->render('service/admin/show.html.twig', [
            'service' => $service,
        ]);
    }

    #[Route('/admin/services/{id}/edit', name: 'admin_service_edit', requirements: ['id' => '\\d+'], methods: ['GET', 'POST'])]
    public function adminEdit(Request $request, Service $service, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ServiceType::class, $service);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Service updated successfully.');

            return $this->redirectToRoute('admin_service_index');
        }

        return $this->render('service/admin/edit.html.twig', [
            'form' => $form,
            'service' => $service,
        ]);
    }

    #[Route('/admin/services/{id}/delete', name: 'admin_service_delete', requirements: ['id' => '\\d+'], methods: ['POST'])]
    public function adminDelete(Request $request, Service $service, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isCsrfTokenValid('delete_service_' . $service->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('danger', 'Invalid CSRF token.');

            return $this->redirectToRoute('admin_service_index');
        }

        $entityManager->remove($service);
        $entityManager->flush();
        $this->addFlash('success', 'Service deleted successfully.');

        return $this->redirectToRoute('admin_service_index');
    }

    #[Route('/services', name: 'service_index', methods: ['GET'])]
    public function index(Request $request, ServiceRepository $serviceRepository, PaginatorInterface $paginator): Response
    {
        $filters = $this->extractFilters($request);
        if ($filters['availableOnly'] === '') {
            $filters['availableOnly'] = '1';
        }

        $qb = $serviceRepository->createFilteredQueryBuilder($filters);
        $pagination = $paginator->paginate($qb, $request->query->getInt('page', 1), 9);

        return $this->render('service/frontend/index.html.twig', [
            'pagination' => $pagination,
            'filters' => $filters,
            'serviceTypes' => $serviceRepository->getDistinctServiceTypes(),
        ]);
    }

    #[Route('/services/{id}', name: 'service_show', requirements: ['id' => '\\d+'], methods: ['GET'])]
    public function show(Service $service): Response
    {
        return $this->render('service/frontend/show.html.twig', [
            'service' => $service,
            'canEdit' => $this->canEdit($service),
            'canDelete' => $this->canDelete($service),
        ]);
    }

    #[Route('/services/new', name: 'service_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $service = new Service();
        $service->setCreatedAt(new \DateTime());

        $form = $this->createForm(ServiceType::class, $service);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($service);
            $entityManager->flush();
            $this->addFlash('success', 'Service created successfully.');

            return $this->redirectToRoute('service_index');
        }

        return $this->render('service/frontend/new.html.twig', [
            'form' => $form,
            'service' => $service,
        ]);
    }

    #[Route('/services/{id}/edit', name: 'service_edit', requirements: ['id' => '\\d+'], methods: ['GET', 'POST'])]
    public function edit(Request $request, Service $service, EntityManagerInterface $entityManager): Response
    {
        if (!$this->canEdit($service)) {
            $this->addFlash('warning', 'This service cannot be edited.');

            return $this->redirectToRoute('service_show', ['id' => $service->getId()]);
        }

        $form = $this->createForm(ServiceType::class, $service);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Service updated successfully.');

            return $this->redirectToRoute('service_show', ['id' => $service->getId()]);
        }

        return $this->render('service/frontend/edit.html.twig', [
            'form' => $form,
            'service' => $service,
        ]);
    }

    #[Route('/services/{id}/delete', name: 'service_delete', requirements: ['id' => '\\d+'], methods: ['POST'])]
    public function delete(Request $request, Service $service, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isCsrfTokenValid('delete_service_' . $service->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('danger', 'Invalid CSRF token.');

            return $this->redirectToRoute('service_index');
        }

        if (!$this->canDelete($service)) {
            $this->addFlash('warning', 'This service cannot be deleted because it is used in bookings.');

            return $this->redirectToRoute('service_show', ['id' => $service->getId()]);
        }

        $entityManager->remove($service);
        $entityManager->flush();
        $this->addFlash('success', 'Service deleted successfully.');

        return $this->redirectToRoute('service_index');
    }

    /**
     * @return array<string, string>
     */
    private function extractFilters(Request $request): array
    {
        return [
            'q' => (string) $request->query->get('q', ''),
            'serviceType' => (string) $request->query->get('serviceType', ''),
            'availableOnly' => (string) $request->query->get('availableOnly', ''),
            'ecoOnly' => (string) $request->query->get('ecoOnly', ''),
            'minPrice' => (string) $request->query->get('minPrice', ''),
            'maxPrice' => (string) $request->query->get('maxPrice', ''),
            'sort' => (string) $request->query->get('sort', 'newest'),
        ];
    }

    private function canEdit(Service $service): bool
    {
        return $service->isAvailable();
    }

    private function canDelete(Service $service): bool
    {
        return $service->isAvailable() && $service->getBookings()->isEmpty();
    }
}
