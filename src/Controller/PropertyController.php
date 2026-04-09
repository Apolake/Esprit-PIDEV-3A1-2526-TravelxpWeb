<?php

namespace App\Controller;

use App\Entity\Property;
use App\Form\PropertyType;
use App\Repository\PropertyRepository;
use App\Service\CurrencyConverterService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PropertyController extends AbstractController
{
    #[Route('/properties', name: 'property_index', methods: ['GET'])]
    #[Route('/admin/properties', name: 'admin_property_index', methods: ['GET'])]
    public function index(
        Request $request,
        PropertyRepository $propertyRepository,
        CurrencyConverterService $currencyConverter
    ): Response
    {
        $selectedCurrency = $currencyConverter->normalizeCurrency($request->query->get('currency', 'USD'));

        $filters = [
            'q' => (string) $request->query->get('q', ''),
            'sort' => (string) $request->query->get('sort', 'newest'),
            'propertyType' => (string) $request->query->get('propertyType', ''),
            'city' => (string) $request->query->get('city', ''),
            'country' => (string) $request->query->get('country', ''),
            'active' => (string) $request->query->get('active', ''),
            'minPrice' => (string) $request->query->get('minPrice', ''),
            'maxPrice' => (string) $request->query->get('maxPrice', ''),
            'bedrooms' => (string) $request->query->get('bedrooms', ''),
            'maxGuests' => (string) $request->query->get('maxGuests', ''),
        ];

        $view = (string) $request->query->get('view', 'grid');
        if (!in_array($view, ['grid', 'table'], true)) {
            $view = 'grid';
        }

        $page = max(1, $request->query->getInt('page', 1));
        $perPage = 9;

        $qb = $propertyRepository->createFilteredQueryBuilder($filters);
        $qb
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage);

        $paginator = new Paginator($qb, true);
        $totalItems = count($paginator);
        $totalPages = max(1, (int) ceil($totalItems / $perPage));
        $properties = iterator_to_array($paginator);

        $formattedPricesByPropertyId = [];
        foreach ($properties as $property) {
            if (!$property instanceof Property || $property->getId() === null || $property->getPricePerNight() === null) {
                continue;
            }

            $priceInUsd = (float) $property->getPricePerNight();
            $convertedPrice = $currencyConverter->convert($priceInUsd, 'USD', $selectedCurrency);
            $formattedPricesByPropertyId[$property->getId()] = $currencyConverter->formatAmount($convertedPrice, $selectedCurrency);
        }

        $isAdmin = str_starts_with((string) $request->attributes->get('_route'), 'admin_');

        return $this->render('property/index.html.twig', [
            'properties' => $properties,
            'isAdmin' => $isAdmin,
            'filters' => $filters,
            'view' => $view,
            'selectedCurrency' => $selectedCurrency,
            'currencySymbol' => $currencyConverter->getSymbol($selectedCurrency),
            'supportedCurrencies' => $currencyConverter->getSupportedCurrenciesWithLabels(),
            'formattedPricesByPropertyId' => $formattedPricesByPropertyId,
            'pagination' => [
                'page' => $page,
                'perPage' => $perPage,
                'totalItems' => $totalItems,
                'totalPages' => $totalPages,
            ],
            'types' => $propertyRepository->getDistinctPropertyTypes(),
            'cities' => $propertyRepository->getDistinctCities(),
            'countries' => $propertyRepository->getDistinctCountries(),
        ]);
    }

    #[Route('/properties/new', name: 'property_new', methods: ['GET', 'POST'])]
    #[Route('/admin/properties/new', name: 'admin_property_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $isAdmin = str_starts_with((string) $request->attributes->get('_route'), 'admin_');

        $property = new Property();
        $property->setCreatedAt(new \DateTime());

        $form = $this->createForm(PropertyType::class, $property);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->handlePropertyImageUpload($property, $form->get('imageFile')->getData());

            $entityManager->persist($property);
            $entityManager->flush();

            $this->addFlash('success', 'Property created successfully.');

            return $this->redirectToRoute($isAdmin ? 'admin_property_index' : 'property_index');
        }

        return $this->render('property/new.html.twig', [
            'isAdmin' => $isAdmin,
            'property' => $property,
            'form' => $form,
        ]);
    }

    #[Route('/properties/{id}', name: 'property_show', requirements: ['id' => '\\d+'], methods: ['GET'])]
    #[Route('/admin/properties/{id}', name: 'admin_property_show', requirements: ['id' => '\\d+'], methods: ['GET'])]
    public function show(
        Request $request,
        Property $property,
        CurrencyConverterService $currencyConverter
    ): Response
    {
        $isAdmin = str_starts_with((string) $request->attributes->get('_route'), 'admin_');
        $selectedCurrency = $currencyConverter->normalizeCurrency($request->query->get('currency', 'USD'));
        $priceInUsd = (float) ($property->getPricePerNight() ?? 0);
        $convertedPrice = $currencyConverter->convert($priceInUsd, 'USD', $selectedCurrency);

        return $this->render('property/show.html.twig', [
            'isAdmin' => $isAdmin,
            'property' => $property,
            'selectedCurrency' => $selectedCurrency,
            'currencySymbol' => $currencyConverter->getSymbol($selectedCurrency),
            'supportedCurrencies' => $currencyConverter->getSupportedCurrenciesWithLabels(),
            'convertedPrice' => $convertedPrice,
            'formattedConvertedPrice' => $currencyConverter->formatAmount($convertedPrice, $selectedCurrency),
        ]);
    }

    #[Route('/properties/{id}/edit', name: 'property_edit', requirements: ['id' => '\\d+'], methods: ['GET', 'POST'])]
    #[Route('/admin/properties/{id}/edit', name: 'admin_property_edit', requirements: ['id' => '\\d+'], methods: ['GET', 'POST'])]
    public function edit(Request $request, Property $property, EntityManagerInterface $entityManager): Response
    {
        $isAdmin = str_starts_with((string) $request->attributes->get('_route'), 'admin_');

        $form = $this->createForm(PropertyType::class, $property);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->handlePropertyImageUpload($property, $form->get('imageFile')->getData());

            $entityManager->flush();

            $this->addFlash('success', 'Property updated successfully.');

            return $this->redirectToRoute($isAdmin ? 'admin_property_index' : 'property_index');
        }

        return $this->render('property/edit.html.twig', [
            'isAdmin' => $isAdmin,
            'property' => $property,
            'form' => $form,
        ]);
    }

    #[Route('/properties/{id}', name: 'property_delete', requirements: ['id' => '\\d+'], methods: ['POST'])]
    #[Route('/admin/properties/{id}', name: 'admin_property_delete', requirements: ['id' => '\\d+'], methods: ['POST'])]
    public function delete(Request $request, Property $property, EntityManagerInterface $entityManager): Response
    {
        $isAdmin = str_starts_with((string) $request->attributes->get('_route'), 'admin_');

        if ($this->isCsrfTokenValid('delete_property_' . $property->getId(), (string) $request->request->get('_token'))) {
            $entityManager->remove($property);
            $entityManager->flush();

            $this->addFlash('success', 'Property deleted successfully.');
        } else {
            $this->addFlash('danger', 'Invalid CSRF token.');
        }

        return $this->redirectToRoute($isAdmin ? 'admin_property_index' : 'property_index');
    }

    private function handlePropertyImageUpload(Property $property, ?UploadedFile $imageFile): void
    {
        if ($imageFile === null) {
            return;
        }

        $uploadDir = $this->getParameter('kernel.project_dir') . '/public/uploads/properties';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }

        $extension = $imageFile->guessExtension() ?: 'bin';
        $filename = bin2hex(random_bytes(8)) . '.' . $extension;

        $imageFile->move($uploadDir, $filename);
        $property->setImages('/uploads/properties/' . $filename);
    }
}
