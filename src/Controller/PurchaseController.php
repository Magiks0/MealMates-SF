<?php

namespace App\Controller;

use App\Repository\PurchaseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api')]
class PurchaseController extends AbstractController
{
    #[Route('/purchases/{id}', name: 'purchase', methods: ['GET'])]
    public function getById(int $id, PurchaseRepository $purchaseRepository, SerializerInterface $serializer): JsonResponse
    {
        $purchase = $purchaseRepository->find($id);
        $jsonPurchase = $serializer->serialize($purchase, 'json', ['groups' => 'purchase:read']);

        return new JsonResponse($jsonPurchase, Response::HTTP_OK, [], true);
    }
}