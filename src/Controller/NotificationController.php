<?php
namespace App\Controller;

// Ne pas oublier d'ajouter le cron 0 3 * * * /usr/bin/php /var/www/mealmates/bin/console products:expiry-alert --env=prod >> /var/log/mealmates-expiry.log 2>&1 dans le serveur de production.

use App\Entity\Notification;
use App\Repository\NotificationRepository;
use App\Security\Voter\NotificationVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/notifications', name: 'api_notif_')]   // methods removed here to avoid double filtering
class NotificationController extends AbstractController
{
    // GET /api/notifications
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(NotificationRepository $repo): JsonResponse
    {
        $data = $repo->findBy(
            ['user' => $this->getUser()],
            ['createdAt' => 'DESC']
        );

        return $this->json($data, 200, [], ['groups' => ['notif:read']]);
    }

    // PATCH /api/notifications/{id}/read
    #[Route('/{id}/read', name: 'mark_read', methods: ['PATCH'])]
    public function mark(Notification $n, EntityManagerInterface $em): JsonResponse
    {
        $this->denyAccessUnlessGranted(NotificationVoter::MARK_READ, $n);

        $n->markRead();
        $em->flush();

        return $this->json(null, 204);
    }
}
