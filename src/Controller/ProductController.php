<?php

namespace App\Controller;

use App\Entity\Address;
use App\Entity\File;
use App\Entity\Product;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Repository\TypeRepository;
use App\Service\StripeService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api')]
class ProductController extends AbstractController
{
    public function __construct(
        private readonly Security $security,
    ){
    }

    #[Route('/products', name: 'api_product', methods: ['GET'])]
    public function getFilteredProducts(SerializerInterface $serializer, ProductRepository $productRepository, Request $request): JsonResponse
    {
        $filters = $request->query->all();
        
        // Traiter les paramètres géographiques
        if ($request->query->has('latitude') && $request->query->has('longitude')) {
            $filters['latitude'] = (float) $request->query->get('latitude');
            $filters['longitude'] = (float) $request->query->get('longitude');
            $filters['radius'] = (float) $request->query->get('radius', 10); // Rayon par défaut de 10 km
        }
        
        $products = $productRepository->findFilteredProducts($filters, $this->security->getUser());

        $jsonProducts = $serializer->serialize($products, 'json', ['groups' => 'product:read']);

        return new JsonResponse($jsonProducts, Response::HTTP_OK, [], true);
    }

    #[Route('/products/nearby', name: 'api_products_nearby', methods: ['GET'])]
    public function getNearbyProducts(SerializerInterface $serializer, ProductRepository $productRepository, Request $request): JsonResponse
    {
        if (!$request->query->has('latitude') || !$request->query->has('longitude')) {
            return new JsonResponse(['error' => 'Les paramètres latitude et longitude sont requis'], Response::HTTP_BAD_REQUEST);
        }
                
        $latitude = (float) $request->query->get('latitude');
        $longitude = (float) $request->query->get('longitude');
        $radius = (float) $request->query->get('radius', 10); // Rayon par défaut de 10 km
        
        $products = $productRepository->findProductsNearby($latitude, $longitude, $radius);
        $jsonProducts = $serializer->serialize($products, 'json', ['groups' => 'product:read']);

        return new JsonResponse($jsonProducts, Response::HTTP_OK, [], true);
    }

    #[Route('/products/{id}', name: 'api_product_show', methods: ['GET'])]
    public function getProduct(int|string $id, SerializerInterface $serializer, ProductRepository $productRepository, OrderRepository $orderRepository): JsonResponse
    {
        $product = $productRepository->find($id);
        
        if (!$product) {
            return new JsonResponse(['message' => 'Produit non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $bought = null === $orderRepository->findOneBy(['product' => $product]);

        $productArray = $serializer->normalize($product, 'json', ['groups' => 'product:read']);
        $productArray['isBought'] = $bought;

        $jsonResponse = $serializer->serialize($productArray, 'json');

        return new JsonResponse($jsonResponse, Response::HTTP_OK, [], true);
    }

    #[Route('/product/last-chance', name: 'last_chance', methods: ['GET'])]
    public function getLastChanceProducts(SerializerInterface $serializer, ProductRepository $productRepository): JsonResponse
    {
        $products = $productRepository->findLastChanceProducts($this->security->getUser());
        $jsonProducts = $serializer->serialize($products, 'json', ['groups' => 'product:read']);

        return new JsonResponse($jsonProducts, Response::HTTP_OK, [], true);
    }

    #[Route('/product/recent', name: 'recent_products', methods: ['GET'])]
    public function getRecentProducts(SerializerInterface $serializer, ProductRepository $productRepository): JsonResponse
    {
        $products = $productRepository->findRecentProducts($this->security->getUser());
        $jsonProducts = $serializer->serialize($products, 'json', ['groups' => 'product:read']);

        return new JsonResponse($jsonProducts, Response::HTTP_OK, [], true);
    }

    #[Route('/product/new', name: 'product', methods: ['POST'])]
    public function newProduct(Request $request, EntityManagerInterface $entityManager, StripeService $stripeService, Filesystem $filesystem, TypeRepository $typeRepository): JsonResponse
    {
        try {
            $user = $this->getUser();
            $type = $request->request->get('type');
            $title = $request->request->get('title');
            $description = $request->request->get('description');
            $quantity = (int)$request->request->get('quantity');
            $peremptionDate = new \DateTime($request->request->get('peremptionDate'));
            $price = (float)$request->request->get('price');
            $donation = filter_var($request->request->get('donation'), FILTER_VALIDATE_BOOLEAN);
            $locationJson = $request->request->get('location');
            $locationData = json_decode($locationJson, true);

            if (!$locationData || !isset($locationData['address']) || !isset($locationData['coordinates'])) {
                return new JsonResponse(['error' => 'Données de localisation invalides'], 400);
            }

            if ($price === 0) {
                $donation = true;
            }

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

            if (null !== $type) {
                $linkedType = $typeRepository->findOneBy(['name' => $type]);
                $product->setType($linkedType);
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
                            ->setPath($fileName);
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

            $address = new Address();
            $address
                ->setName($locationData['address'])
                ->setLatitude((float)$locationData['coordinates']['lat'])
                ->setLongitude((float)$locationData['coordinates']['lng']);

            $entityManager->persist($address);
            $product->setAddress($address);
            
            $product->setCreatedAt(new \DateTime());
            $product->setUpdatedAt(new \DateTime());

            $stripeProduct = $stripeService->createProduct($product);
            $product->setStripeProductId($stripeProduct->id);

            $stripePrice = $stripeService->createPrice($product);
            $product->setStripePriceId($stripePrice->id);

            $entityManager->persist($product);
            $entityManager->flush();

            return new JsonResponse(['message' => 'Produit créé avec succès', 'status' => 'success'], 201);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Une erreur est survenue: ' . $e->getMessage()]);
        }
    }

    #[Route('/product/my-ads', name: 'user_products', methods: ['GET'])]
    public function getUserProducts(SerializerInterface $serializer, ProductRepository $productRepository): JsonResponse
    {
        $user = $this->getUser();
        
        if (!$user) {
            return new JsonResponse(['error' => 'User not authenticated'], Response::HTTP_UNAUTHORIZED);
        }

        $products = $productRepository->findByUser($user);
        $jsonProducts = $serializer->serialize($products, 'json', ['groups' => 'product:read']);

        return new JsonResponse($jsonProducts, Response::HTTP_OK, [], true);
    }
}