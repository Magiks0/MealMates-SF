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
        $user = new User();
        $user->setEmail($email);
        // Hachage du mot de passe
        $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
        $user->setPassword($hashedPassword);

        // ex: isVerified = false par défaut
        // $user->setIsVerified(false);

        // Sauvegarde
        $em->persist($user);
        $em->flush();

        return new JsonResponse(['message' => 'User created'], 201);
    }

    #[Route('/api/login', name: 'app_login', methods: ['POST'])]
    public function login(
        Request $request,
        UserRepository $userRepo,
        UserPasswordHasherInterface $passwordHasher,
        JWTTokenManagerInterface $JWTManager
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $email = $data['email'] ?? null;
        $plainPassword = $data['password'] ?? null;

        $user = $userRepo->findOneBy(['email' => $email]);
        if (!$user || !$passwordHasher->isPasswordValid($user, $plainPassword)) {
            return new JsonResponse(['error' => 'Bad credentials'], 401);
        }

        // Génération du token JWT pour l'utilisateur
        $token = $JWTManager->create($user);

        return new JsonResponse([
            'message' => 'Logged in successfully',
            'token' => $token,
        ]);
    }

    #[Route('/api/google-login', name: 'app_google_login', methods: ['POST'])]
    public function googleLogin(
        Request $request,
        UserRepository $userRepo,
        EntityManagerInterface $em
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $googleIdToken = $data['token'] ?? null;

        if (!$googleIdToken) {
            return new JsonResponse(['error' => 'Missing Google token'], 400);
        }

        // Vérifier le token auprès de Google (ex: "https://oauth2.googleapis.com/tokeninfo?id_token=xxx")
        // Extraire l'email
        // Créer ou retrouver le user
        // Retourner un token / message

        // (Exemple de code simplifié)
        // $response = file_get_contents("https://oauth2.googleapis.com/tokeninfo?id_token=$googleIdToken");
        // $googleData = json_decode($response, true);
        // $email = $googleData['email'] ?? null;

        return new JsonResponse(['message' => 'TODO: implement google login']);
    }

}
