<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Repository\DietaryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

#[Route('/api')]
class UserController extends AbstractController
{
    #[Route('/users', name: 'users', methods: ['GET'])]
    public function getAllUsers(UserRepository $userRepository, SerializerInterface $serializer): JsonResponse
    {
        $users = $userRepository->findAll();
        $jsonUsers = $serializer->serialize($users, 'json', ['groups' => 'user:read']);
        
        return new JsonResponse($jsonUsers, Response::HTTP_OK, [], true);
    }
    
    #[Route('/user/profile', name: 'current_user', methods: ['GET'])]
    public function getCurrentUser(SerializerInterface $serializer): JsonResponse
    {
        $currentUser = $this->getUser();
        
        if (!$currentUser) {
            return new JsonResponse(['message' => 'Utilisateur non connecté'], Response::HTTP_UNAUTHORIZED);
        }
        
        $jsonUser = $serializer->serialize($currentUser, 'json', ['groups' => 'user:read']);
        
        return new JsonResponse($jsonUser, Response::HTTP_OK, [], true);
    }

    #[Route('/users/address', name: 'set_user_address', methods: ['PATCH'])]
    public function setAddress(Request $request, EntityManagerInterface $em, SerializerInterface $serializer): JsonResponse {
        $user = $this->getUser();
    
        if (!$user) {
            return new JsonResponse(['message' => 'Utilisateur non connecté'], Response::HTTP_UNAUTHORIZED);
        }
    
        $data = json_decode($request->getContent(), true);
    
        if (!isset($data['address']) || empty($data['address'])) {
            return new JsonResponse(['message' => 'Adresse manquante'], Response::HTTP_BAD_REQUEST);
        }
    
        $user->setAddress($data['address']);
        $em->persist($user);
        $em->flush();
    
        $jsonUser = $serializer->serialize($user, 'json', ['groups' => 'user:read']);
    
        return new JsonResponse($jsonUser, Response::HTTP_OK, [], true);
    }

    #[Route('/users/username', name: 'set_user_username', methods: ['PATCH'])]
    public function setUsername(Request $request, EntityManagerInterface $em, SerializerInterface $serializer): JsonResponse {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['message' => 'Utilisateur non connecté'], Response::HTTP_UNAUTHORIZED);
        }

        $data = json_decode($request->getContent(), true);

        if (!isset($data['username']) || empty($data['username'])) {
            return new JsonResponse(['message' => 'Nom d\'utilisateur manquant'], Response::HTTP_BAD_REQUEST);
        }

        $user->setUsername($data['username']);
        $em->persist($user);
        $em->flush();

        $jsonUser = $serializer->serialize($user, 'json', ['groups' => 'user:read']);

        return new JsonResponse($jsonUser, Response::HTTP_OK, [], true);
    }

    #[Route('/users/image', name: 'set_user_image', methods: ['PATCH'])]
    public function setImageUrl(Request $request, EntityManagerInterface $em, SerializerInterface $serializer): JsonResponse {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['message' => 'Utilisateur non connecté'], Response::HTTP_UNAUTHORIZED);
        }

        $data = json_decode($request->getContent(), true);

        if (!isset($data['image_url']) || empty($data['image_url'])) {
            return new JsonResponse(['message' => 'URL de l\'image manquante'], Response::HTTP_BAD_REQUEST);
        }

        $user->setImageUrl($data['image_url']);
        $em->persist($user);
        $em->flush();

        $jsonUser = $serializer->serialize($user, 'json', ['groups' => 'user:read']);

        return new JsonResponse($jsonUser, Response::HTTP_OK, [], true);
    }

    #[Route('/users/dietary-preferences', name: 'update_dietary_preferences', methods: ['PUT'])]
    public function updateDietaryPreferences(
        Request $request, 
        EntityManagerInterface $em, 
        SerializerInterface $serializer,
        DietaryRepository $dietaryRepository
    ): JsonResponse {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['message' => 'Utilisateur non connecté'], Response::HTTP_UNAUTHORIZED);
        }

        $data = json_decode($request->getContent(), true);

        if (!isset($data['dietaryIds']) || !is_array($data['dietaryIds'])) {
            return new JsonResponse(['message' => 'IDs des préférences alimentaires manquants'], Response::HTTP_BAD_REQUEST);
        }

        // Supprimer toutes les préférences actuelles
        foreach ($user->getDietaries() as $dietary) {
            $user->removeDietetic($dietary);
        }

        // Ajouter les nouvelles préférences
        foreach ($data['dietaryIds'] as $dietaryId) {
            $dietary = $dietaryRepository->find($dietaryId);
            if ($dietary) {
                $user->addDietetic($dietary);
            }
        }

        $em->persist($user);
        $em->flush();

        $jsonUser = $serializer->serialize($user, 'json', ['groups' => 'user:read']);

        return new JsonResponse($jsonUser, Response::HTTP_OK, [], true);
    }
}