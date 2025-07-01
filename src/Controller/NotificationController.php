<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Repository\NotificationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/notifications')]
#[IsGranted('ROLE_USER')]
class NotificationController extends AbstractController
{
    public function __construct(
        private NotificationRepository $notificationRepository,
        private EntityManagerInterface $entityManager,
        private SerializerInterface $serializer
    ) {}

    /**
     * Récupère toutes les notifications de l'utilisateur connecté
     */
    #[Route('', name: 'api_notifications_list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $user = $this->getUser();
        $limit = $request->query->getInt('limit', 50);
        
        $notifications = $this->notificationRepository->findByUser($user, $limit);
        
        $json = $this->serializer->serialize($notifications, 'json', [
            'groups' => ['notification:read']
        ]);

        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

    /**
     * Récupère uniquement les notifications non lues
     */
    #[Route('/unread', name: 'api_notifications_unread', methods: ['GET'])]
    public function unread(): JsonResponse
    {
        $user = $this->getUser();
        $notifications = $this->notificationRepository->findUnreadByUser($user);
        
        $json = $this->serializer->serialize($notifications, 'json', [
            'groups' => ['notification:read']
        ]);

        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

    /**
     * Compte le nombre de notifications non lues
     */
    #[Route('/count', name: 'api_notifications_count', methods: ['GET'])]
    public function count(): JsonResponse
    {
        $user = $this->getUser();
        $count = $this->notificationRepository->countUnreadForUser($user);
        
        return $this->json(['count' => $count]);
    }

    /**
     * Marque une notification comme lue
     */
    #[Route('/{id}/read', name: 'api_notification_mark_read', methods: ['PUT'])]
    public function markAsRead(Notification $notification): JsonResponse
    {
        // Vérifier que la notification appartient à l'utilisateur
        if ($notification->getUser() !== $this->getUser()) {
            return $this->json(['error' => 'Access denied'], Response::HTTP_FORBIDDEN);
        }

        $notification->setIsRead(true);
        $this->entityManager->flush();

        return $this->json(['success' => true]);
    }

    /**
     * Marque toutes les notifications comme lues
     */
    #[Route('/read-all', name: 'api_notifications_mark_all_read', methods: ['PUT'])]
    public function markAllAsRead(): JsonResponse
    {
        $user = $this->getUser();
        $this->notificationRepository->markAllAsReadForUser($user);

        return $this->json(['success' => true]);
    }

    /**
     * Supprime une notification
     */
    #[Route('/{id}', name: 'api_notification_delete', methods: ['DELETE'])]
    public function delete(Notification $notification): JsonResponse
    {
        // Vérifier que la notification appartient à l'utilisateur
        if ($notification->getUser() !== $this->getUser()) {
            return $this->json(['error' => 'Access denied'], Response::HTTP_FORBIDDEN);
        }

        $this->entityManager->remove($notification);
        $this->entityManager->flush();

        return $this->json(['success' => true]);
    }

    /**
     * Supprime toutes les notifications lues
     */
    #[Route('/clear-read', name: 'api_notifications_clear_read', methods: ['DELETE'])]
    public function clearRead(): JsonResponse
    {
        $user = $this->getUser();
        $deletedCount = $this->notificationRepository->deleteReadForUser($user);

        return $this->json([
            'success' => true, 
            'deleted' => $deletedCount
        ]);
    }
}