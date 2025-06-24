<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Product;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Uid\Uuid;

#[Route('/api')]
class OrderController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ){}

    #[Route('/orders/{id}', name: 'purchase', methods: ['GET'])]
    public function getById(int $id, OrderRepository $orderRepository, SerializerInterface $serializer): JsonResponse
    {
        $order = $orderRepository->find($id);
        $jsonOrder = $serializer->serialize($order, 'json', ['groups' => 'order:read']);

        return new JsonResponse($jsonOrder, Response::HTTP_OK, [], true);
    }

    #[Route('/orders/{userId}/{token}', name: 'purchase', methods: ['GET'])]
    public function getByTokenAndUser(int $userId, string $token, OrderRepository $orderRepository, UserRepository $userRepository, SerializerInterface $serializer): JsonResponse
    {
        $buyer = $userRepository->find($userId);
        $order = $orderRepository->findByBuyerAndToken($buyer, $token);

        //If order is not found, that means wrong token or not the buyer we refuse the access
        if (null === $order) {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        } else {
            $jsonOrder = $serializer->serialize($order, 'json', ['groups' => 'order:read']);
        }


        return new JsonResponse($jsonOrder, Response::HTTP_OK, [], true);
    }

    #[Route('/orders/validate-pickup/{qrCodeToken}', name: 'orders', methods: ['GET'])]
    public function validatePickup(string $qrCodeToken): JsonResponse
    {

        return new JsonResponse('success');
    }
}