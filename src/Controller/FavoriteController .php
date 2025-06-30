<?php

namespace App\Controller;

use App\Entity\FavoriteProduct;
use App\Entity\Product;
use App\Repository\FavoriteProductRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api')]
class FavoriteController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly FavoriteProductRepository $favoriteRepository,
        private readonly SerializerInterface $serializer
    ) {}

    #[Route('/favorites', name: 'api_favorites_list', methods: ['GET'])]
    public function getFavorites(): JsonResponse
    {
        $user = $this->getUser();
        
        if (!$user) {
            return new JsonResponse(['error' => 'User not authenticated'], Response::HTTP_UNAUTHORIZED);
        }

        $favorites = $this->favoriteRepository->findByUser($user);
        
        // Extract products from favorites
        $products = array_map(function($favorite) {
            return $favorite->getProduct();
        }, $favorites);

        $jsonProducts = $this->serializer->serialize($products, 'json', ['groups' => 'product:read']);

        return new JsonResponse($jsonProducts, Response::HTTP_OK, [], true);
    }

    #[Route('/favorites/{id}', name: 'api_favorites_toggle', methods: ['GET'])]
    public function toggleFavorite(Product $product): JsonResponse
    {
        $user = $this->getUser();
        
        if (!$user) {
            return new JsonResponse(['error' => 'User not authenticated'], Response::HTTP_UNAUTHORIZED);
        }

        $existingFavorite = $this->favoriteRepository->findByUserAndProduct($user, $product);

        if ($existingFavorite) {
            // Remove from favorites
            $this->entityManager->remove($existingFavorite);
            $this->entityManager->flush();
            
            return new JsonResponse(['isFavorite' => false, 'message' => 'Removed from favorites'], Response::HTTP_OK);
        } else {
            // Add to favorites
            $favorite = new FavoriteProduct();
            $favorite->setUser($user);
            $favorite->setProduct($product);
            
            $this->entityManager->persist($favorite);
            $this->entityManager->flush();
            
            return new JsonResponse(['isFavorite' => true, 'message' => 'Added to favorites'], Response::HTTP_OK);
        }
    }

    #[Route('/favorites/check/{id}', name: 'api_favorite_check', methods: ['GET'])]
    public function checkFavorite(Product $product): JsonResponse
    {
        $user = $this->getUser();
        
        if (!$user) {
            return new JsonResponse(['error' => 'User not authenticated'], Response::HTTP_UNAUTHORIZED);
        }

        $isFavorite = $this->favoriteRepository->isFavorite($user, $product);

        return new JsonResponse(['isFavorite' => $isFavorite], Response::HTTP_OK);
    }
}