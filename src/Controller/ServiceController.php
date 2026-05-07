<?php

namespace App\Controller;

use App\Entity\Service;
use App\Form\ServiceType;
use App\Repository\ServiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ServiceController extends AbstractController
{
    #[Route('/admin/services', name: 'admin_service_index', methods: ['GET'])]
    #[Route('/services', name: 'service_index', methods: ['GET'])]
    public function index(Request $request, ServiceRepository $serviceRepository): Response
    {
        $isAdmin = str_starts_with((string) $request->attributes->get('_route'), 'admin_');
        if ($isAdmin) {
            $this->denyAccessUnlessGranted('ROLE_ADMIN');
        }

        $filters = $this->extractFilters($request);
        if (!$isAdmin && $filters['availableOnly'] === '') {
            $filters['availableOnly'] = '1';
        }

        $page = max(1, $request->query->getInt('page', 1));
        $perPage = $isAdmin ? 10 : 9;

        $qb = $serviceRepository->createFilteredQueryBuilder($filters);
        $qb
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage);

        $paginator = new Paginator($qb, true);
        $totalItems = count($paginator);
        $totalPages = max(1, (int) ceil($totalItems / $perPage));

        return $this->render('service/index.html.twig', [
            'isAdmin' => $isAdmin,
            'services' => iterator_to_array($paginator),
            'filters' => $filters,
            'pagination' => [
                'page' => $page,
                'perPage' => $perPage,
                'totalItems' => $totalItems,
                'totalPages' => $totalPages,
            ],
            'serviceTypes' => $serviceRepository->getDistinctServiceTypes(),
        ]);
    }

    #[Route('/admin/services/new', name: 'admin_service_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $isAdmin = str_starts_with((string) $request->attributes->get('_route'), 'admin_');

        $service = new Service();
        $service->setCreatedAt(new \DateTimeImmutable());

        $form = $this->createForm(ServiceType::class, $service);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($service);
            $entityManager->flush();
            $this->addFlash('success', 'Service created successfully.');

            return $this->redirectToRoute($isAdmin ? 'admin_service_index' : 'service_index');
        }

        return $this->render('service/new.html.twig', [
            'isAdmin' => $isAdmin,
            'form' => $form,
            'service' => $service,
        ]);
    }

    #[Route('/admin/services/{id}', name: 'admin_service_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    #[Route('/services/{id}', name: 'service_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(Request $request, Service $service): Response
    {
        $isAdmin = str_starts_with((string) $request->attributes->get('_route'), 'admin_');
        if ($isAdmin) {
            $this->denyAccessUnlessGranted('ROLE_ADMIN');
        } elseif (!$service->isAvailable()) {
            throw $this->createNotFoundException('Service not found.');
        }

        return $this->render('service/show.html.twig', [
            'isAdmin' => $isAdmin,
            'service' => $service,
        ]);
    }

    #[Route('/admin/services/{id}/edit', name: 'admin_service_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, Service $service, EntityManagerInterface $entityManager): Response
    {
        $isAdmin = true;

        $form = $this->createForm(ServiceType::class, $service);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Service updated successfully.');

            return $this->redirectToRoute('admin_service_index');
        }

        return $this->render('service/edit.html.twig', [
            'isAdmin' => $isAdmin,
            'form' => $form,
            'service' => $service,
        ]);
    }

    #[Route('/admin/services/{id}/delete', name: 'admin_service_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Service $service, EntityManagerInterface $entityManager): Response
    {
        $isAdmin = true;

        if (!$this->isCsrfTokenValid('delete_service_' . $service->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('danger', 'Invalid CSRF token.');

            return $this->redirectToRoute('admin_service_index');
        }

        $entityManager->remove($service);
        $entityManager->flush();
        $this->addFlash('success', 'Service deleted successfully.');

        return $this->redirectToRoute('admin_service_index');
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

}
