<?php

namespace App\Controller;

use App\Entity\Offer;
use App\Form\OfferType;
use App\Repository\OfferRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OfferController extends AbstractController
{
    #[Route('/offers', name: 'offer_index', methods: ['GET'])]
    #[Route('/admin/offers', name: 'admin_offer_index', methods: ['GET'])]
    public function index(Request $request, OfferRepository $offerRepository): Response
    {
        $isAdmin = str_starts_with((string) $request->attributes->get('_route'), 'admin_');

        $filters = [
            'q' => (string) $request->query->get('q', ''),
            'sort' => (string) $request->query->get('sort', 'highest_discount'),
            'active' => (string) $request->query->get('active', ''),
            'propertyId' => (string) $request->query->get('propertyId', ''),
            'minDiscount' => (string) $request->query->get('minDiscount', ''),
            'maxDiscount' => (string) $request->query->get('maxDiscount', ''),
            'validNow' => (string) $request->query->get('validNow', ''),
        ];

        $page = max(1, $request->query->getInt('page', 1));
        $perPage = 10;

        $qb = $offerRepository->createFilteredQueryBuilder($filters);
        $qb
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage);

        $paginator = new Paginator($qb, true);
        $totalItems = count($paginator);
        $totalPages = max(1, (int) ceil($totalItems / $perPage));

        return $this->render('offer/index.html.twig', [
            'isAdmin' => $isAdmin,
            'offers' => iterator_to_array($paginator),
            'filters' => $filters,
            'pagination' => [
                'page' => $page,
                'perPage' => $perPage,
                'totalItems' => $totalItems,
                'totalPages' => $totalPages,
            ],
            'properties' => $offerRepository->getPropertiesForFilter(),
        ]);
    }

    #[Route('/offers/new', name: 'offer_new', methods: ['GET', 'POST'])]
    #[Route('/admin/offers/new', name: 'admin_offer_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $isAdmin = str_starts_with((string) $request->attributes->get('_route'), 'admin_');

        $offer = new Offer();
        $offer->setCreatedAt(new \DateTime());

        $form = $this->createForm(OfferType::class, $offer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($offer);
            $entityManager->flush();

            $this->addFlash('success', 'Offer created successfully.');

            return $this->redirectToRoute($isAdmin ? 'admin_offer_index' : 'offer_index');
        }

        return $this->render('offer/new.html.twig', [
            'isAdmin' => $isAdmin,
            'offer' => $offer,
            'form' => $form,
        ]);
    }

    #[Route('/offers/{id}', name: 'offer_show', requirements: ['id' => '\\d+'], methods: ['GET'])]
    #[Route('/admin/offers/{id}', name: 'admin_offer_show', requirements: ['id' => '\\d+'], methods: ['GET'])]
    public function show(Request $request, Offer $offer): Response
    {
        return $this->render('offer/show.html.twig', [
            'isAdmin' => str_starts_with((string) $request->attributes->get('_route'), 'admin_'),
            'offer' => $offer,
        ]);
    }

    #[Route('/offers/{id}/edit', name: 'offer_edit', requirements: ['id' => '\\d+'], methods: ['GET', 'POST'])]
    #[Route('/admin/offers/{id}/edit', name: 'admin_offer_edit', requirements: ['id' => '\\d+'], methods: ['GET', 'POST'])]
    public function edit(Request $request, Offer $offer, EntityManagerInterface $entityManager): Response
    {
        $isAdmin = str_starts_with((string) $request->attributes->get('_route'), 'admin_');

        $form = $this->createForm(OfferType::class, $offer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Offer updated successfully.');

            return $this->redirectToRoute($isAdmin ? 'admin_offer_index' : 'offer_index');
        }

        return $this->render('offer/edit.html.twig', [
            'isAdmin' => $isAdmin,
            'offer' => $offer,
            'form' => $form,
        ]);
    }

    #[Route('/offers/{id}', name: 'offer_delete', requirements: ['id' => '\\d+'], methods: ['POST'])]
    #[Route('/admin/offers/{id}', name: 'admin_offer_delete', requirements: ['id' => '\\d+'], methods: ['POST'])]
    public function delete(Request $request, Offer $offer, EntityManagerInterface $entityManager): Response
    {
        $isAdmin = str_starts_with((string) $request->attributes->get('_route'), 'admin_');

        if ($this->isCsrfTokenValid('delete_offer_' . $offer->getId(), (string) $request->request->get('_token'))) {
            $entityManager->remove($offer);
            $entityManager->flush();

            $this->addFlash('success', 'Offer deleted successfully.');
        } else {
            $this->addFlash('danger', 'Invalid CSRF token.');
        }

        return $this->redirectToRoute($isAdmin ? 'admin_offer_index' : 'offer_index');
    }
}
