<?php

namespace App\Controller;

use App\Entity\Dietetic;
use App\Repository\DieteticRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api')]
class DieteticController extends AbstractController
{
    #[Route('/dietetics', name: 'dietetics', methods: ['GET'])]
    public function getDietetics(DieteticRepository $dieteticRepository, SerializerInterface $serializer): JsonResponse
    {
        $dietetics = $dieteticRepository->findAll();
        $jsonDietetics = $serializer->serialize($dietetics, 'json', ['groups' => 'dietetic:read']);

        return new JsonResponse($jsonDietetics, Response::HTTP_OK, [], true);
    }
}