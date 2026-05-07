<?php

namespace App\Controller;

use App\Entity\Property;
use App\Form\PropertyType;
use App\Repository\PropertyRepository;
use App\Service\CurrencyConverterService;
use App\Service\GeoapifyService;
use Dompdf\Dompdf;
use Dompdf\Options;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class PropertyController extends AbstractController
{
    public function __construct(
        private readonly string $geoapifyMapTilesApiKey,
        private readonly string $geoapifyMapTilesUrl,
    ) {
    }

    #[Route('/properties', name: 'property_index', methods: ['GET'])]
    #[Route('/admin/properties', name: 'admin_property_index', methods: ['GET'])]
    public function index(
        Request $request,
        PropertyRepository $propertyRepository,
        CurrencyConverterService $currencyConverter
    ): Response {
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
        $isAdmin = str_starts_with((string) $request->attributes->get('_route'), 'admin_');
        if ($isAdmin) {
            $this->denyAccessUnlessGranted('ROLE_ADMIN');
        } else {
            $filters['active'] = '1';
        }

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
        $displayImagesByPropertyId = [];
        foreach ($properties as $property) {
            if (!$property instanceof Property || $property->getId() === null) {
                continue;
            }

            $priceInUsd = (float) $property->getPricePerNight();
            $convertedPrice = $currencyConverter->convert($priceInUsd, 'USD', $selectedCurrency);
            $formattedPricesByPropertyId[$property->getId()] = $currencyConverter->formatAmount($convertedPrice, $selectedCurrency);
            $displayImagesByPropertyId[$property->getId()] = $this->resolvePropertyImageUrl($property->getImages());
        }

        return $this->render('property/index.html.twig', [
            'properties' => $properties,
            'isAdmin' => $isAdmin,
            'filters' => $filters,
            'view' => $view,
            'selectedCurrency' => $selectedCurrency,
            'currencySymbol' => $currencyConverter->getSymbol($selectedCurrency),
            'supportedCurrencies' => $currencyConverter->getSupportedCurrenciesWithLabels(),
            'formattedPricesByPropertyId' => $formattedPricesByPropertyId,
            'displayImagesByPropertyId' => $displayImagesByPropertyId,
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

    #[Route('/admin/properties/new', name: 'admin_property_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        GeoapifyService $geoapifyService
    ): Response
    {
        $isAdmin = str_starts_with((string) $request->attributes->get('_route'), 'admin_');

        $property = new Property();
        $property->setCreatedAt(new \DateTimeImmutable());

        $form = $this->createForm(PropertyType::class, $property);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->handlePropertyImageUpload($property, $form->get('imageFile')->getData());
            $property->setImages($this->normalizePropertyImagePath($property->getImages()));
            $geoapifyService->geocodeProperty($property);

            $entityManager->persist($property);
            $entityManager->flush();

            $this->addFlash('success', 'Property created successfully.');

            return $this->redirectToRoute($isAdmin ? 'admin_property_index' : 'property_index');
        }

        return $this->render('property/new.html.twig', [
            'isAdmin' => $isAdmin,
            'property' => $property,
            'form' => $form,
            'geoapifyMapTilesApiKey' => $this->geoapifyMapTilesApiKey,
            'geoapifyMapTilesUrl' => $this->buildGeoapifyTilesUrl(),
        ]);
    }

    #[Route('/properties/{id}', name: 'property_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    #[Route('/admin/properties/{id}', name: 'admin_property_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(
        Request $request,
        Property $property,
        CurrencyConverterService $currencyConverter
    ): Response {
        $isAdmin = str_starts_with((string) $request->attributes->get('_route'), 'admin_');
        if ($isAdmin) {
            $this->denyAccessUnlessGranted('ROLE_ADMIN');
        } elseif (!$property->isActive()) {
            throw $this->createNotFoundException('Property not found.');
        }

        $selectedCurrency = $currencyConverter->normalizeCurrency($request->query->get('currency', 'USD'));
        $priceInUsd = (float) $property->getPricePerNight();
        $convertedPrice = $currencyConverter->convert($priceInUsd, 'USD', $selectedCurrency);

        return $this->render('property/show.html.twig', [
            'isAdmin' => $isAdmin,
            'property' => $property,
            'displayImageUrl' => $this->resolvePropertyImageUrl($property->getImages()),
            'selectedCurrency' => $selectedCurrency,
            'currencySymbol' => $currencyConverter->getSymbol($selectedCurrency),
            'supportedCurrencies' => $currencyConverter->getSupportedCurrenciesWithLabels(),
            'convertedPrice' => $convertedPrice,
            'formattedConvertedPrice' => $currencyConverter->formatAmount($convertedPrice, $selectedCurrency),
            'geoapifyMapTilesApiKey' => $this->geoapifyMapTilesApiKey,
            'geoapifyMapTilesUrl' => $this->buildGeoapifyTilesUrl(),
        ]);
    }

    #[Route('/properties/{id}/pdf', name: 'property_pdf', requirements: ['id' => '\d+'], methods: ['GET'])]
    #[Route('/admin/properties/{id}/pdf', name: 'admin_property_pdf', requirements: ['id' => '\d+'], methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function generatePdf(Request $request, Property $property): Response
    {
        $isAdmin = str_starts_with((string) $request->attributes->get('_route'), 'admin_');
        if ($isAdmin) {
            $this->denyAccessUnlessGranted('ROLE_ADMIN');
        } elseif (!$property->isActive()) {
            throw $this->createNotFoundException('Property not found.');
        }

        $html = $this->renderView('property/pdf.html.twig', [
            'property' => $property,
            'offers' => $property->getOffers(),
            'imageSrc' => $this->resolvePropertyImageForPdf($property),
            'pdfImageWarning' => extension_loaded('gd') ? null : 'Property image omitted because GD extension is unavailable.',
        ]);

        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return new Response(
            $dompdf->output(),
            Response::HTTP_OK,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="property_' . $property->getId() . '.pdf"',
            ]
        );
    }

    #[Route('/admin/properties/{id}/edit', name: 'admin_property_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(
        Request $request,
        Property $property,
        EntityManagerInterface $entityManager,
        GeoapifyService $geoapifyService
    ): Response
    {
        $isAdmin = str_starts_with((string) $request->attributes->get('_route'), 'admin_');

        $form = $this->createForm(PropertyType::class, $property);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->handlePropertyImageUpload($property, $form->get('imageFile')->getData());
            $property->setImages($this->normalizePropertyImagePath($property->getImages()));
            $geoapifyService->geocodeProperty($property);

            $entityManager->flush();

            $this->addFlash('success', 'Property updated successfully.');

            return $this->redirectToRoute($isAdmin ? 'admin_property_index' : 'property_index');
        }

        return $this->render('property/edit.html.twig', [
            'isAdmin' => $isAdmin,
            'property' => $property,
            'form' => $form,
            'geoapifyMapTilesApiKey' => $this->geoapifyMapTilesApiKey,
            'geoapifyMapTilesUrl' => $this->buildGeoapifyTilesUrl(),
        ]);
    }

    #[Route('/admin/properties/{id}', name: 'admin_property_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
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

    private function resolvePropertyImageForPdf(Property $property): ?string
    {
        if (!extension_loaded('gd')) {
            return null;
        }

        $imagePath = $this->resolvePropertyImageUrl($property->getImages());
        if ($imagePath === null) {
            return null;
        }

        if (str_starts_with($imagePath, 'http://') || str_starts_with($imagePath, 'https://')) {
            return $imagePath;
        }

        $absolutePath = (string) $this->getParameter('kernel.project_dir') . '/public' . $imagePath;
        if (!is_file($absolutePath)) {
            return null;
        }

        $binary = file_get_contents($absolutePath);
        if ($binary === false) {
            return null;
        }

        $mimeType = mime_content_type($absolutePath) ?: 'application/octet-stream';

        return 'data:' . $mimeType . ';base64,' . base64_encode($binary);
    }

    private function buildGeoapifyTilesUrl(): string
    {
        $url = trim($this->geoapifyMapTilesUrl);
        $apiKey = trim($this->geoapifyMapTilesApiKey);

        if ($url === '') {
            return '';
        }

        if ($apiKey !== '') {
            $url = str_replace('{apiKey}', rawurlencode($apiKey), $url);
            if (!str_contains($url, 'apiKey=')) {
                $url .= (str_contains($url, '?') ? '&' : '?') . 'apiKey=' . rawurlencode($apiKey);
            }
        }

        return $url;
    }

    private function normalizePropertyImagePath(?string $value): ?string
    {
        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }

        $first = trim(explode(',', $value)[0] ?? '');
        if ($first === '') {
            return null;
        }

        $first = str_replace('\\', '/', $first);

        if (str_starts_with($first, 'http://') || str_starts_with($first, 'https://')) {
            return $first;
        }

        if (str_contains($first, '/public/')) {
            $first = substr($first, strpos($first, '/public/') + 7);
        }

        if (!str_starts_with($first, '/')) {
            $first = '/'.$first;
        }

        return $first;
    }

    private function resolvePropertyImageUrl(?string $value): ?string
    {
        $normalized = $this->normalizePropertyImagePath($value);
        if ($normalized === null) {
            return null;
        }

        if (str_starts_with($normalized, 'http://') || str_starts_with($normalized, 'https://')) {
            return $normalized;
        }

        $publicPath = $this->getParameter('kernel.project_dir').'/public'.$normalized;

        return is_file($publicPath) ? $normalized : null;
    }
}
