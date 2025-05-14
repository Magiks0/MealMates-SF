<?php

namespace App\Controller;

use App\Entity\Address;
use App\Entity\File;
use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api')]
class ProductController extends AbstractController
{
    #[Route('/products', name: 'api_product', methods: ['GET'])]
    public function getAllProducts(SerializerInterface $serializer, ProductRepository $productRepository, Request $request): JsonResponse
    {
        $products = $productRepository->findAll();
        $jsonProducts = $serializer->serialize($products, 'json', ['groups' => 'product:read']);

        return new JsonResponse($jsonProducts, Response::HTTP_OK, [], true);
    }

    #[Route('/products/search', name: 'api_product_search', methods: ['GET'])]
    public function searchProducts(SerializerInterface $serializer, ProductRepository $productRepository, Request $request): JsonResponse
    {
        // Récupérer les paramètres de recherche
        $latitude = $request->query->get('latitude');
        $longitude = $request->query->get('longitude');
        $radius = $request->query->get('radius', 10); // Rayon par défaut: 10km
        
        // Vérifier que les coordonnées sont fournies
        if (!$latitude || !$longitude) {
            return new JsonResponse(['error' => 'Les coordonnées (latitude et longitude) sont requises'], Response::HTTP_BAD_REQUEST);
        }
        
        // Convertir en valeurs numériques
        $latitude = (float) $latitude;
        $longitude = (float) $longitude;
        $radius = (float) $radius;
        
        // Rechercher les produits dans le rayon spécifié
        $products = $productRepository->findByDistance($latitude, $longitude, $radius);
        
        // Sérialiser les résultats
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

    #[Route('/product/new', name: 'product', methods: ['POST'])]
    public function newProduct(Request $request, EntityManagerInterface $entityManager, Filesystem $filesystem): JsonResponse
    {
        try {
            $user = $this->getUser();
            $title = $request->request->get('title');
            $description = $request->request->get('description');
            $quantity = (int)$request->request->get('quantity');
            $peremptionDate = new \DateTime($request->request->get('peremptionDate'));
            $price = (float)$request->request->get('price');
            $donation = filter_var($request->request->get('donation'), FILTER_VALIDATE_BOOLEAN);

            $collection_date = new \DateTime();

            $product = (new Product())
                ->setTitle($title)
                ->setDescription($description)
                ->setQuantity($quantity)
                ->setPeremptionDate($peremptionDate)
                ->setCollectionDate($collection_date)
                ->setUser($user)
                ->setDonation($donation);

            if ($product->isDonation()) {
                $product->setPrice(0);
            } else {
                $product->setPrice($price);
            }

            $files = $request->files->get('files');
            if ($files) {
                foreach ($files as $file) {
                    try {
                        $originalName = $file->getClientOriginalName();
                        $size = (string) $file->getSize();
                        $fileName = md5(uniqid()).'.'.$file->guessExtension();
                        $filePath = $this->getParameter('products_images_directory');

                        if(!$filesystem->exists($filePath)) {
                            $filesystem->mkdir($filePath);
                        }

                        $file->move(
                            $filePath,
                            $fileName
                        );

                        $productImage = new File();

                        $productImage
                            ->setOriginalName($originalName)
                            ->setSize($size)
                            ->setPath($fileName)
                            ->setCreatedAt(new \DateTime());
                        $productImage->setUpdatedAt(new \DateTime());
                        $product->addFile($productImage);
                        $entityManager->persist($productImage);
                    } catch(\Exception $e) {
                        return new JsonResponse([
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString()
                        ], 500);
                    }
                }
            }

            $adress = new Address();
            $adress
                ->setName('9 rue de Janville, 60250 MOUY')
                ->setLatitude('19.132414')
                ->setLongitude('34.3454535');

            $entityManager->persist($adress);

            $product->setCreatedAt(new \DateTime());
            $product->setUpdatedAt(new \DateTime());
            $product->setAddress($adress);

            $entityManager->persist($product);
            $entityManager->flush();

            return new JsonResponse(['message' => 'Produit créé avec succès'], 201);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Une erreur est survenue: ' . $e->getMessage()]);
        }
    }
}