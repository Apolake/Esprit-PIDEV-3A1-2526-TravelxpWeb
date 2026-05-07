<?php

namespace App\Controller;

use App\Service\GeoapifyService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/geoapify', name: 'geoapify_')]
class GeoapifyController extends AbstractController
{
    #[Route('/autocomplete', name: 'autocomplete', methods: ['GET'])]
    public function autocomplete(Request $request, GeoapifyService $geoapifyService): JsonResponse
    {
        $query = trim((string) $request->query->get('q', ''));
        if (mb_strlen($query) < 2) {
            return $this->json(['items' => []]);
        }

        return $this->json([
            'items' => $geoapifyService->autocomplete($query),
        ]);
    }

    #[Route('/reverse', name: 'reverse', methods: ['GET'])]
    public function reverse(Request $request, GeoapifyService $geoapifyService): JsonResponse
    {
        $latitude = $request->query->get('lat');
        $longitude = $request->query->get('lon');

        if (!is_numeric($latitude) || !is_numeric($longitude)) {
            return $this->json(['item' => null], 400);
        }

        return $this->json([
            'item' => $geoapifyService->reverse((float) $latitude, (float) $longitude),
        ]);
    }

    #[Route('/places', name: 'places', methods: ['GET'])]
    public function places(Request $request, GeoapifyService $geoapifyService): JsonResponse
    {
        $latitude = $request->query->get('lat');
        $longitude = $request->query->get('lon');

        if (!is_numeric($latitude) || !is_numeric($longitude)) {
            return $this->json(['items' => []], 400);
        }

        return $this->json([
            'items' => $geoapifyService->nearbyPlaces((float) $latitude, (float) $longitude),
        ]);
    }

    #[Route('/route', name: 'route', methods: ['GET'])]
    public function route(Request $request, GeoapifyService $geoapifyService): JsonResponse
    {
        $fromLat = $request->query->get('fromLat');
        $fromLon = $request->query->get('fromLon');
        $toLat = $request->query->get('toLat');
        $toLon = $request->query->get('toLon');

        if (!is_numeric($fromLat) || !is_numeric($fromLon) || !is_numeric($toLat) || !is_numeric($toLon)) {
            return $this->json(['route' => null], 400);
        }

        return $this->json([
            'route' => $geoapifyService->route((float) $fromLat, (float) $fromLon, (float) $toLat, (float) $toLon),
        ]);
    }
}
