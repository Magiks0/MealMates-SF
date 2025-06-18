<?php

namespace App\Controller;

use App\Entity\Chat;
use App\Entity\Message;
use App\Repository\ChatRepository;
use App\Repository\MessageRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api', name: 'api_')]
class ChatController extends AbstractController
{
    private $security;
    private $entityManager;
    private $serializer;

    public function __construct(
        Security $security,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer
    ) {
        $this->security = $security;
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
    }

    #[Route('/chats', name: 'chat_list', methods: ['GET'])]
    public function getChats(ChatRepository $chatRepository): JsonResponse
    {
        $currentUser = $this->security->getUser();
        if (!$currentUser) {
            return $this->json(['message' => 'Non autorisé'], Response::HTTP_UNAUTHORIZED);
        }

        $chats = $chatRepository->findChatsForUser($currentUser->getId());

        $formattedChats = [];
        foreach ($chats as $chat) {
            $otherUser = $chat->getUser1()->getId() === $currentUser->getId()
                ? $chat->getUser2()
                : $chat->getUser1();

            $messages = $chat->getMessages();
            $lastMessage = null;
            if (count($messages) > 0) {
                $lastMessage = $messages[count($messages) - 1];
            }

            $formattedChats[] = [
                'id' => $chat->getId(),
                'otherUser' => [
                    'id' => $otherUser->getId(),
                    'username' => $otherUser->getUsername(),
                    'transactionCount' => 0 // À remplacer par une vraie donnée si disponible
                ],
                'lastMessage' => $lastMessage ? [
                    'content' => $lastMessage->getContent(),
                    'createdAt' => $lastMessage->getCreatedAt()->format('Y-m-d H:i:s'),
                    'isFromCurrentUser' => $lastMessage->getAuthor()->getId() === $currentUser->getId()
                ] : null,
                'updatedAt' => $chat->getUpdatedAt()->format('Y-m-d H:i:s')
            ];
        }

        return $this->json($formattedChats);
    }

    #[Route('/chats/{id}', name: 'chat_detail', methods: ['GET'])]
    public function getChat(int $id, ChatRepository $chatRepository): JsonResponse
    {
        $currentUser = $this->security->getUser();
        if (!$currentUser) {
            return $this->json(['message' => 'Non autorisé'], Response::HTTP_UNAUTHORIZED);
        }

        $chat = $chatRepository->find($id);
        if (!$chat) {
            return $this->json(['message' => 'Chat non trouvé'], Response::HTTP_NOT_FOUND);
        }

        if ($chat->getUser1()->getId() !== $currentUser->getId() && $chat->getUser2()->getId() !== $currentUser->getId()) {
            return $this->json(['message' => 'Accès refusé'], Response::HTTP_FORBIDDEN);
        }

        $otherUser = $chat->getUser1()->getId() === $currentUser->getId()
            ? $chat->getUser2()
            : $chat->getUser1();

        $messages = [];
        foreach ($chat->getMessages() as $message) {
            $messages[] = [
                'id' => $message->getId(),
                'content' => $message->getContent(),
                'createdAt' => $message->getCreatedAt()->format('Y-m-d H:i:s'),
                'isFromCurrentUser' => $message->getAuthor()->getId() === $currentUser->getId()
            ];
        }

        $result = [
            'id' => $chat->getId(),
            'otherUser' => [
                'id' => $otherUser->getId(),
                'username' => $otherUser->getUsername(),
                'transactionCount' => 0,
                'createdAt' => $chat->getCreatedAt()->format('d-m-Y'),
            ],
            'messages' => $messages,
            'productId' => $chat->getProduct()->getId(),
            'productName' => $chat->getProduct()->getTitle(),
            'productPrice' => $chat->getProduct()->getPrice(),
            'productFile' => '$chat->getProduct()->getFiles()[0]->getPath()'
        ];

        return $this->json($result);
    }

    #[Route('/chats/{id}/messages', name: 'chat_messages', methods: ['GET'])]
    public function getChatMessages(int $id, Request $request, ChatRepository $chatRepository): JsonResponse
    {
        $currentUser = $this->security->getUser();
        if (!$currentUser) {
            return $this->json(['message' => 'Non autorisé'], Response::HTTP_UNAUTHORIZED);
        }

        $chat = $chatRepository->find($id);
        if (!$chat) {
            return $this->json(['message' => 'Chat non trouvé'], Response::HTTP_NOT_FOUND);
        }

        if ($chat->getUser1()->getId() !== $currentUser->getId() && $chat->getUser2()->getId() !== $currentUser->getId()) {
            return $this->json(['message' => 'Accès refusé'], Response::HTTP_FORBIDDEN);
        }

        $afterId = $request->query->get('after_id');

        $messages = [];
        foreach ($chat->getMessages() as $message) {
            if ($afterId && $message->getId() <= $afterId) {
                continue;
            }

            $messages[] = [
                'id' => $message->getId(),
                'content' => $message->getContent(),
                'createdAt' => $message->getCreatedAt()->format('Y-m-d H:i:s'),
                'isFromCurrentUser' => $message->getAuthor()->getId() === $currentUser->getId()
            ];
        }

        return $this->json($messages);
    }

    #[Route('/chats/{id}/message/new', name: 'send_message', methods: ['POST'])]
    public function sendMessage(int $id, Request $request, ChatRepository $chatRepository, UserRepository $userRepository): JsonResponse
    {
        $currentUser = $this->security->getUser();
        if (!$currentUser) {
            return $this->json(['message' => 'Non autorisé'], Response::HTTP_UNAUTHORIZED);
        }

        $chat = $chatRepository->find($id);
        if (!$chat) {
            return $this->json(['message' => 'Chat non trouvé'], Response::HTTP_NOT_FOUND);
        }

        // Vérifier que l'utilisateur fait partie de cette conversation
        if ($chat->getUser1()->getId() !== $currentUser->getId() && $chat->getUser2()->getId() !== $currentUser->getId()) {
            return $this->json(['message' => 'Accès refusé'], Response::HTTP_FORBIDDEN);
        }

        $data = json_decode($request->getContent(), true);
        if (!isset($data['content']) || empty($data['content'])) {
            return $this->json(['message' => 'Le contenu du message est requis'], Response::HTTP_BAD_REQUEST);
        }

        // Déterminer le destinataire
        $recipient = $chat->getUser1()->getId() === $currentUser->getId()
            ? $chat->getUser2()
            : $chat->getUser1();

        // Créer le nouveau message
        $message = new Message();
        $message->setContent($data['content']);
        $message->setAuthor($currentUser);
        $message->setRecipient($recipient);
        $message->setChat($chat);

        // Mettre à jour le timestamp du chat
        $chat->setUpdatedAt(new \DateTime());

        $this->entityManager->persist($message);
        $this->entityManager->flush();

        return $this->json([
            'id' => $message->getId(),
            'content' => $message->getContent(),
            'createdAt' => $message->getCreatedAt()->format('Y-m-d H:i:s'),
            'isFromCurrentUser' => true
        ], Response::HTTP_CREATED);
    }

    #[Route('/chats/new/{userId}', name: 'new_chat', methods: ['POST'])]
    public function createChat(
        int $userId,
        Request $request,
        UserRepository $userRepository,
        ChatRepository $chatRepository,
        MessageRepository $messageRepository,
        ProductRepository $productRepository,
    ): JsonResponse {
        $currentUser = $this->security->getUser();
        if (!$currentUser) {
            return $this->json(['message' => 'Non autorisé'], Response::HTTP_UNAUTHORIZED);
        }

        $otherUser = $userRepository->find($userId);
        if (!$otherUser) {
            return $this->json(['message' => 'Utilisateur non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        $messageContent = trim($data['message'] ?? '');

        if (empty($messageContent)) {
            return $this->json(['message' => 'Le message ne peut pas être vide'], Response::HTTP_BAD_REQUEST);
        }

        // Vérifie si une conversation existe déjà
        $productId = $data['productId'];
        $existingChat = $chatRepository->findChatBetweenUsersAndProduct($currentUser->getId(), $userId, $productId);
        if ($existingChat) {
            // Ajouter le message à la conversation existante
            $message = new Message();
            $message->setChat($existingChat);
            $message->setAuthor($currentUser);
            $message->setContent($messageContent);

            $this->entityManager->persist($message);
            $this->entityManager->flush();

            return $this->json([
                'message' => 'Message envoyé à une conversation existante',
                'chatId' => $existingChat->getId()
            ]);
        }

        $product = $productRepository->find($productId);
        $chat = new Chat();
        $chat->setUser1($currentUser);
        $chat->setUser2($otherUser);
        $chat->setProduct($product);

        $this->entityManager->persist($chat);

        $message = new Message();
        $message->setChat($chat);
        $message->setAuthor($currentUser);
        $message->setContent($messageContent);

        $this->entityManager->persist($message);
        $this->entityManager->flush();

        return $this->json([
            'id' => $chat->getId(),
            'message' => 'Conversation et message créés avec succès'
        ], Response::HTTP_CREATED);
    }

    #[Route('/chat/check-existence', name: 'check_chat_existence', methods: ['GET'])]
    public function checkChatExistence(Request $request, ChatRepository $chatRepository, ProductRepository $productRepository): JsonResponse
    {
        $currentUser = $this->security->getUser();
        if (!$currentUser) {
            return $this->json(['message' => 'Non autorisé'], Response::HTTP_UNAUTHORIZED);
        }

        $userId = $currentUser->getId();
        $productId = $request->query->get('productId');

        if (!$productId) {
            return $this->json(['message' => 'Produit non spécifié'], Response::HTTP_BAD_REQUEST);
        }

        $product = $productRepository->find($productId);
        $chat = $chatRepository->findChatBetweenUsersAndProduct($product->getUser()->getId(),$userId, $productId);

        if ($chat) {
            return $this->json([
                'exists' => true,
                'chatId' => $chat->getId(),
            ]);
        }

        return $this->json([
            'exists' => false,
            'chatId' => null,
        ]);
    }

}