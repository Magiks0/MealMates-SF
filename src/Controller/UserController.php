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

    #[Route('/users/update/{id}', name: 'update_user', methods: ['PUT'])]
    public function updateUser(
        int $id,
        Request $request,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer
    ): JsonResponse {
        $user = $userRepository->find($id);

        if (!$user) {
            return new JsonResponse(['message' => 'Utilisateur non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['username'])) {
            $user->setUsername($data['username']);
        }
        if (isset($data['adress'])) {
            $user->setAdress($data['adress']);
        }
        if (isset($data['image_url'])) {
            $user->setImageUrl($data['image_url']);
        }

        $entityManager->persist($user);
        $entityManager->flush();

        $jsonUser = $serializer->serialize($user, 'json', [AbstractNormalizer::IGNORED_ATTRIBUTES => ['password']]);

        return new JsonResponse($jsonUser, Response::HTTP_OK, [], true);
    }

    #[Route('/users/preferences', name: 'update_user_preferences', methods: ['PUT'])]
    public function updatePreferences(
        Request $request,
        EntityManagerInterface $entityManager,
        PreferencesRepository $preferencesRepository
    ): JsonResponse {
        $user = $this->getUser();
        
        if (!$user) {
            return new JsonResponse(['message' => 'User not authenticated'], Response::HTTP_UNAUTHORIZED);
        }

        $data = json_decode($request->getContent(), true);
        
        if (!isset($data['preferences']) || !is_array($data['preferences'])) {
            return new JsonResponse(['message' => 'Invalid data format'], Response::HTTP_BAD_REQUEST);
        }

        // Vider les préférences existantes
        foreach ($user->getPreferences() as $preference) {
            $user->removePreference($preference);
        }

        // Ajouter les nouvelles préférences
        foreach ($data['preferences'] as $preferenceId) {
            $preference = $preferencesRepository->find($preferenceId);
            
            if ($preference) {
                $user->addPreference($preference);
            }
        }

        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Preferences updated successfully'], Response::HTTP_OK);
    }
}
