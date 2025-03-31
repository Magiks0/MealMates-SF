<?php

namespace App\Controller;

use App\Repository\TypeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api')]
class TypeController extends AbstractController
{

    #[Route('/types', name: 'types', methods: ['GET'])]
    public function getTypes(TypeRepository $typeRepository, SerializerInterface $serializer): JsonResponse
    {
        $types = $typeRepository->findAll();
        $jsonTypes = $serializer->serialize($types, 'json', ['groups' => 'type:read']);

        return new JsonResponse($jsonTypes, Response::HTTP_OK, [], true);
    }

}