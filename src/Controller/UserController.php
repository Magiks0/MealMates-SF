<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;

final class UserController extends AbstractController
{
    #[Route('/api/user', name: 'app_user')]
    public function index(UserRepository $userRepository, UserInterface $user): JsonResponse
    {
        $userData = $userRepository->findUserInfo($user);

        if (!$userData) {
            return new JsonResponse(['message' => 'Utilisateur non trouvé.'], 404);
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
