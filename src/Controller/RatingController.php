<?php

namespace App\Controller;

use App\Entity\Rating;
use App\Repository\OrderRepository;
use App\Repository\RatingRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api')]
class RatingController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly Security $security,
        private readonly SerializerInterface $serializer
    ) {}

    #[Route('/ratings', name: 'create_rating', methods: ['POST'])]
    public function create(
        Request $request,
        OrderRepository $orderRepository,
        RatingRepository $ratingRepository,
        UserRepository $userRepository
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        
        if (!isset($data['orderId']) || !isset($data['score'])) {
            return new JsonResponse(['error' => 'Missing required fields'], Response::HTTP_BAD_REQUEST);
        }

        $order = $orderRepository->find($data['orderId']);
        if (!$order) {
            return new JsonResponse(['error' => 'Order not found'], Response::HTTP_NOT_FOUND);
        }

        $currentUser = $this->security->getUser();
        
        // Vérifier si l'utilisateur a déjà évalué cette commande
        // $existingRating = $ratingRepository->findByOrderAndReviewer($order->getId(), $currentUser->getId());
        // if ($existingRating) {
        //     return new JsonResponse(['error' => 'You have already rated this transaction'], Response::HTTP_BAD_REQUEST);
        // }

     
        $reviewed = $userRepository->getUserById($data['reviewedId']);

        $rating = new Rating();
        $rating->setReviewer($currentUser);
        $rating->setReviewed($reviewed);
        $rating->setOrder($order);
        $rating->setScore($data['score']);
        $rating->setComment($data['comment'] ?? null);

        $this->entityManager->persist($rating);
        $this->entityManager->flush();

        $jsonRating = $this->serializer->serialize($rating, 'json', ['groups' => 'rating:read']);
        return new JsonResponse($jsonRating, Response::HTTP_CREATED, [], true);
    }

    #[Route('/ratings/check/{orderId}', name: 'check_rating', methods: ['GET'])]
    public function checkIfRated(int $orderId, RatingRepository $ratingRepository): JsonResponse {
        $currentUser = $this->security->getUser();
        $rating = $ratingRepository->findByOrderAndReviewer($orderId, $currentUser->getId());
        
        return new JsonResponse(['rated' => $rating !== null]);
    }

    #[Route('/users/{userId}/ratings', name: 'user_ratings', methods: ['GET'])]
    public function getUserRatings(
        int $userId,
        UserRepository $userRepository,
        RatingRepository $ratingRepository
    ): JsonResponse {
        $user = $userRepository->find($userId);
        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $ratings = $ratingRepository->findRatingsReceivedByUser($user);
        $jsonRatings = $this->serializer->serialize($ratings, 'json', ['groups' => 'rating:read']);

        return new JsonResponse($jsonRatings, Response::HTTP_OK, [], true);
    }

    #[Route('/my-ratings', name: 'my_ratings', methods: ['GET'])]
    public function getMyRatings(RatingRepository $ratingRepository): JsonResponse {
        $currentUser = $this->security->getUser();
        $ratings = $ratingRepository->findRatingsReceivedByUser($currentUser);
        
        $jsonRatings = $this->serializer->serialize($ratings, 'json', ['groups' => 'rating:read']);
        return new JsonResponse($jsonRatings, Response::HTTP_OK, [], true);
    }

}