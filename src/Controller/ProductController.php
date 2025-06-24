<?php

namespace App\Controller;

use App\Entity\Address;
use App\Entity\File;
use App\Entity\Product;
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
        $products = $productRepository->findFilteredProducts($filters, $this->security->getUser());
        $jsonProducts = $serializer->serialize($products, 'json', ['groups' => 'product:read']);

        return new JsonResponse($jsonProducts, Response::HTTP_OK, [], true);
    }

    #[Route('/products/{id}', name: 'api_product_show', methods: ['GET'])]
    public function getProduct(int|string $id, SerializerInterface $serializer, ProductRepository $productRepository): JsonResponse
    {
        $product = $productRepository->find($id);

        if (!$product) {
            return new JsonResponse(['message' => 'Produit non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $jsonProduct = $serializer->serialize($product, 'json', ['groups' => 'product:read']);

        return new JsonResponse($jsonProduct, Response::HTTP_OK, [], true);
    }

    #[Route('/product/last-chance', name: 'last_chance', methods: ['GET'])]
    public function getLastChanceProducts(SerializerInterface $serializer, ProductRepository $productRepository): JsonResponse
    {
        $products = $productRepository->findLastChanceProducts($this->security->getUser());
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

            $adress = new Address();
            $adress
                ->setName('9 rue de Janville, 60250 MOUY')
                ->setLatitude('19.132414')
                ->setLongitude('34.3454535');

            $entityManager->persist($adress);
            $product->setAddress($adress);

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
}