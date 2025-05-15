<?php

namespace App\Controller;

use App\Entity\Address;
use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api')]
class ProductController extends AbstractController
{
    #[Route('/products', name: 'api_product', methods: ['GET'])]
    public function getFilteredProducts(SerializerInterface $serializer, ProductRepository $productRepository, Request $request): JsonResponse
    {
        $filters = $request->query->all();
        $products = $productRepository->findFilteredProducts($filters);
        $jsonProducts = $serializer->serialize($products, 'json', ['groups' => 'product:read']);

        return new JsonResponse($jsonProducts, Response::HTTP_OK, [], true);
    }

    #[Route('/product/{id}', name: 'api_product_show', methods: ['GET'])]
    public function getProduct(int $id, SerializerInterface $serializer, ProductRepository $productRepository): JsonResponse
    {
        $product = $productRepository->findDetailedProduct($id);
        
        if (!$product) {
            return new JsonResponse(['message' => 'Produit non trouvé'], Response::HTTP_NOT_FOUND);
        }
        
        $jsonProduct = $serializer->serialize($product, 'json', ['groups' => 'product:read']);
        
        return new JsonResponse($jsonProduct, Response::HTTP_OK, [], true);
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
    public function newProduct(Request $request, EntityManagerInterface $entityManager): JsonResponse
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
                   if ($file->isValid()) {
                       $fileName = md5(uniqid()).'.'.$file->guessExtension();

                       $file->move(
                           $this->getParameter('products_images_directory'),
                           $fileName
                       );

                       $productImage = new File();
                       $productImage->setPath($fileName);
                       $product->addFile($productImage);
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