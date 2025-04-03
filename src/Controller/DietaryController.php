<?php

namespace App\Controller;

use App\Entity\Dietary;
use App\Repository\DietaryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api')]
class DietaryController extends AbstractController
{
    #[Route('/dietaries', name: 'dietaries', methods: ['GET'])]
    public function getDietaries(DietaryRepository $dietaryRepository, SerializerInterface $serializer): JsonResponse
    {
        $dietaries = $dietaryRepository->findAll();
        $jsonDietaries = $serializer->serialize($dietaries, 'json', ['groups' => 'dietaries:read']);

        return new JsonResponse($jsonDietaries, Response::HTTP_OK, [], true);
    }
}