<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\PreferencesRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

#[Route('/api')]
class UserController extends AbstractController
{
    #[Route('/users', name: 'users', methods: ['GET'])]
    public function getUsers(UserRepository $userRepository, SerializerInterface $serializer): JsonResponse
    {
        $users = $userRepository->findAll();
        $jsonUsers = $serializer->serialize($users, 'json');

        return new JsonResponse($jsonUsers, Response::HTTP_OK, [], true);
        }

        // Récupération des informations de l'utilisateur
        $data = [
            'id' => $userData->getId(),
            'email' => $userData->getEmail(),
            'username' => $userData->getUsername(),
            'adress' => $userData->getAdress(),
            'image_url' => $userData->getImageUrl(),
            'is_verified' => $userData->isVerified(),
            'availabilities' => $userData->getAvailabilities()->toArray(),
            'preferences' => $userData->getPreferences()->toArray(),
        ];

        return $this->json($data);
    }
}
