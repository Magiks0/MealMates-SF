<?php

namespace App\Scheduler;

use App\Entity\Notification;
use App\Entity\Product;
use App\Repository\NotificationRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Scheduler\Attribute\AsSchedule;
use Symfony\Component\Scheduler\Schedule;
use Symfony\Component\Scheduler\RecurringMessage;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsSchedule('product_expiration')]
class ProductExpirationScheduler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ProductRepository $productRepository,
        private NotificationRepository $notificationRepository,
        private MessageBusInterface $messageBus
    ) {}

    public function __invoke(Schedule $schedule): void
    {
        // Exécuter toutes les minutes
        $schedule->add(
            RecurringMessage::every('1 minute', new CheckProductExpirationMessage())
        );
    }
}
