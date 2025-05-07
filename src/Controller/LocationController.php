<?php

namespace App\Controller;

use App\Entity\Location;
use App\Repository\LocationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api')]
class LocationController extends AbstractController
{
    #[Route('/locations/filtered', name: 'location', methods: ['GET'])]
    public function getFilteredLocations(SerializerInterface $serializer, LocationRepository $locationRepository, Request $request): JsonResponse
    {
        $filters = $request->query->all();
        $locations = $locationRepository->findFilteredLocations($filters);
        $jsonLocations = $serializer->serialize($locations, 'json', ['groups' => 'location:read']);

        return new JsonResponse($jsonLocations, Response::HTTP_OK, [], true);
    }

    #[Route('/locations', name: 'locations', methods: ['GET'])]
    public function getLocations(LocationRepository $locationRepository, SerializerInterface $serializer): JsonResponse
    {
        $locations = $locationRepository->findAll();
        
        $jsonLocations = $serializer->serialize($locations, 'json', ['groups' => 'location:read']);

        return new JsonResponse($jsonLocations, Response::HTTP_OK, [], true);
    }
}