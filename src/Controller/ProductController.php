<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api')]
class ProductController extends AbstractController
{
    #[Route('/products', name: 'product', methods: ['GET'])]
    public function getFilteredProducts(SerializerInterface $serializer, ProductRepository $productRepository, Request $request): JsonResponse
    {
        $filters = $request->query->all();
        $products = $productRepository->findFilteredProducts($filters);
        $jsonProducts = $serializer->serialize($products, 'json', ['groups' => 'product:read']);

        return new JsonResponse($jsonProducts, Response::HTTP_OK, [], true);
    }

    #[Route('/product/last-chance', name: 'last_chance', methods: ['GET'])]
    public function getLastChanceProducts(SerializerInterface $serializer, ProductRepository $productRepository): JsonResponse
    {
        $products = $productRepository->findLastChanceProducts();
        $jsonProducts = $serializer->serialize($products, 'json', ['groups' => 'product:read']);

        return new JsonResponse($jsonProducts, Response::HTTP_OK, [], true);
    }

//    #[Route('/product/recommendations', name: 'last_chance', methods: ['GET'])]
//    public function getRecommendedProducts(SerializerInterface $serializer, ProductRepository $productRepository, Request $request): JsonResponse
//    {
//        $products = $productRepository->find();
//        $jsonProducts = $serializer->serialize($products, 'json', ['groups' => 'product:read']);
//
//        return new JsonResponse($jsonProducts, Response::HTTP_OK, [], true);
//    }

    #[Route('/product/recent', name: 'recent_products', methods: ['GET'])]
    public function getRecentProducts(SerializerInterface $serializer, ProductRepository $productRepository): JsonResponse
    {
        $products = $productRepository->findRecentProducts();
        $jsonProducts = $serializer->serialize($products, 'json', ['groups' => 'product:read']);

        return new JsonResponse($jsonProducts, Response::HTTP_OK, [], true);
    }
}