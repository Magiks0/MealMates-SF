<?php

namespace App\Controller;

use App\Repository\ChatRepository;
use App\Repository\MessageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Serializer;

#[Route('/api')]
final class ChatController extends AbstractController
{
    public function __construct(
        private ChatRepository $chatRepository,
        private Serializer $serializer,
    ){}

    #[Route('/chats', name: 'app_chat')]
    public function getChats(): JsonResponse
    {
        $chats = $this->chatRepository->findAll();
        $jsonChats = $this->serializer->serialize($chats, 'json', ['groups' => 'chat:read']);
        return new JsonResponse();
    }
}
