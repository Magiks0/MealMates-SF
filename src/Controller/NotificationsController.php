<?php

namespace App\Controller;

use App\Repository\NotificationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api')]
class NotificationController extends AbstractController
{
    #[Route('/notifications', name: 'api_notifications', methods: ['GET'])]
    public function getMyNotifications(
        NotificationRepository $notificationRepository,
        SerializerInterface $serializer
    ): JsonResponse {
        $user = $this->getUser();
        
        if (!$user) {
            return new JsonResponse(['error' => 'User not authenticated'], Response::HTTP_UNAUTHORIZED);
        }

        $notifications = $notificationRepository->findByUser($user);
        $unreadCount = $notificationRepository->countUnreadByUser($user);

        $jsonNotifications = $serializer->serialize($notifications, 'json', ['groups' => 'notification:read']);

        return new JsonResponse([
            'notifications' => json_decode($jsonNotifications, true),
            'unreadCount' => $unreadCount
        ], Response::HTTP_OK);
    }

    #[Route('/notifications/{id}/read', name: 'api_notification_mark_read', methods: ['PUT'])]
    public function markAsRead(
        int $id,
        NotificationRepository $notificationRepository,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $user = $this->getUser();
        
        if (!$user) {
            return new JsonResponse(['error' => 'User not authenticated'], Response::HTTP_UNAUTHORIZED);
        }

        $notification = $notificationRepository->find($id);

        if (!$notification) {
            return new JsonResponse(['error' => 'Notification not found'], Response::HTTP_NOT_FOUND);
        }

        if ($notification->getUser() !== $user) {
            return new JsonResponse(['error' => 'Access denied'], Response::HTTP_FORBIDDEN);
        }

        $notification->setIsRead(true);
        $entityManager->persist($notification);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Notification marked as read'], Response::HTTP_OK);
    }

    #[Route('/notifications/count', name: 'api_notifications_count', methods: ['GET'])]
    public function getUnreadCount(
        NotificationRepository $notificationRepository
    ): JsonResponse {
        $user = $this->getUser();
        
        if (!$user) {
            return new JsonResponse(['error' => 'User not authenticated'], Response::HTTP_UNAUTHORIZED);
        }

        $unreadCount = $notificationRepository->countUnreadByUser($user);

        return new JsonResponse(['unreadCount' => $unreadCount], Response::HTTP_OK);
    }

    #[Route('/notifications/mark-all-read', name: 'api_notifications_mark_all_read', methods: ['PUT'])]
    public function markAllAsRead(
        NotificationRepository $notificationRepository,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $user = $this->getUser();
        
        if (!$user) {
            return new JsonResponse(['error' => 'User not authenticated'], Response::HTTP_UNAUTHORIZED);
        }

        $notifications = $notificationRepository->findBy(['user' => $user, 'isRead' => false]);
        
        foreach ($notifications as $notification) {
            $notification->setIsRead(true);
            $entityManager->persist($notification);
        }
        
        $entityManager->flush();

        return new JsonResponse(['message' => 'All notifications marked as read'], Response::HTTP_OK);
    }
}