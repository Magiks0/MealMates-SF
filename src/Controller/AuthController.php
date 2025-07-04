<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

class AuthController extends AbstractController
{
    #[Route('/api/register', name: 'app_register', methods: ['POST'])]
    public function register(
        Request $request,
        UserRepository $userRepo,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $em
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $username = $data['username'];
        $email = $data['email'] ?? null;
        $plainPassword = $data['password'] ?? null;

        // Vérifications basiques
        if (!$email || !$plainPassword) {
            return new JsonResponse(['error' => 'Email and password are required'], 400);
        }

        // Vérifier si l'email existe déjà
        if ($userRepo->findOneBy(['email' => $email])) {
            return new JsonResponse(['error' => 'Email already in use'], 400);
        }

        // Création du user
        try {
            $user = (new User())
                ->setEmail($email)
                ->setUsername($username)
                ->setRoles(['ROLE_USER']);

            $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
            $user->setPassword($hashedPassword);
            $em->persist($user);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }

        $em->flush();

        return new JsonResponse(['message' => 'User created'], 201);
    }

    #[Route('/api/login', name: 'app_login', methods: ['GET','POST'])]
    public function login(
        Request $request,
        UserRepository $userRepository,
        UserPasswordHasherInterface $passwordHasher,
        JWTTokenManagerInterface $JWTManager
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $username = $data['username'] ?? null;
        $password = $data['password'] ?? null;

        $user = $userRepository->findOneBy(['username' => $username]);

        if (!$user || !$passwordHasher->isPasswordValid($user, $password)) {
            return new JsonResponse(['error' => 'Bad credentials'], 401);
        }

        try {
            $token = $JWTManager->create($user);
            return new JsonResponse(['token' => $token]);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Error generating JWT: ' . $e->getMessage()], 500);
        }
    }

    #[Route('/api/google-login', name: 'app_google_login', methods: ['POST'])]
    public function googleLogin(
        Request $request,
        UserRepository $userRepo,
        EntityManagerInterface $em,
        JWTTokenManagerInterface $JWTManager
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $googleIdToken = $data['token'] ?? null;

        if (!$googleIdToken) {
            return new JsonResponse(['error' => 'Missing Google token'], 400);
        }

        // 1. Valider le token auprès de Google
        $url = "https://oauth2.googleapis.com/tokeninfo?id_token=" . $googleIdToken;
        $googleResponse = @file_get_contents($url);
        if ($googleResponse === false) {
            return new JsonResponse(['error' => 'Failed to validate token with Google'], 400);
        }
        $googleData = json_decode($googleResponse, true);
        if (!isset($googleData['email'])) {
            return new JsonResponse(['error' => 'Invalid Google token: email not found'], 400);
        }
        $email = $googleData['email'];
        $username = $googleData['name'];

        $user = $userRepo->findOneBy(['email' => $email]);

        if (!$user) {
            $user = (new User())
                ->setEmail($email)
                ->setUsername($username)
                ->setRoles(['ROLE_USER'])
                ->setPassword(null)
                ->setIsVerified(true);
            $em->persist($user);
            $em->flush();
        }

        // 3. Générer un token JWT pour l'utilisateur
        $token = $JWTManager->create($user);

        return new JsonResponse([
            'message' => 'Logged in via Google successfully',
            'token' => $token,
        ]);
    }
}
