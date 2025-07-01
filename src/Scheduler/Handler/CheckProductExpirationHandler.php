<?php

namespace App\Scheduler\Handler;

use App\Entity\Notification;
use App\Entity\Product;
use App\Repository\NotificationRepository;
use App\Repository\ProductRepository;
use App\Scheduler\Message\CheckProductExpirationMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CheckProductExpirationHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ProductRepository $productRepository,
        private NotificationRepository $notificationRepository
    ) {}

    public function __invoke(CheckProductExpirationMessage $message): void
    {
        $today = new \DateTime();
        $today->setTime(0, 0, 0);
        
        $tomorrow = clone $today;
        $tomorrow->modify('+1 day');

        // Récupérer tous les produits publiés qui expirent aujourd'hui ou demain
        $products = $this->productRepository->createQueryBuilder('p')
            ->where('p.published = true')
            ->andWhere('p.peremptionDate IN (:today, :tomorrow)')
            ->setParameter('today', $today)
            ->setParameter('tomorrow', $tomorrow)
            ->getQuery()
            ->getResult();

        foreach ($products as $product) {
            $this->createNotificationIfNeeded($product);
        }

        $this->entityManager->flush();
    }

    private function createNotificationIfNeeded(Product $product): void
    {
        $today = new \DateTime();
        $today->setTime(0, 0, 0);
        
        $peremptionDate = clone $product->getPeremptionDate();
        $peremptionDate->setTime(0, 0, 0);
        
        $daysDiff = $today->diff($peremptionDate)->days;
        $isToday = $peremptionDate->format('Y-m-d') === $today->format('Y-m-d');
        $isTomorrow = $daysDiff === 1 && $peremptionDate > $today;

        if ($isToday) {
            $type = Notification::TYPE_EXPIRATION_TODAY;
            $title = "⚠️ Produit expire aujourd'hui !";
            $message = sprintf(
                "Votre produit '%s' expire aujourd'hui (%s). Il est urgent de le vendre ou de le consommer !",
                $product->getTitle(),
                $peremptionDate->format('d/m/Y')
            );
        } elseif ($isTomorrow) {
            $type = Notification::TYPE_EXPIRATION_TOMORROW;
            $title = "⏰ Produit expire demain";
            $message = sprintf(
                "Votre produit '%s' expire demain (%s). Pensez à le vendre rapidement.",
                $product->getTitle(),
                $peremptionDate->format('d/m/Y')
            );
        } else {
            return;
        }

        // Vérifier si une notification existe déjà pour ce produit aujourd'hui
        if ($this->notificationRepository->existsForProductAndType($product, $type, new \DateTime())) {
            return;
        }

        // Créer la notification
        $notification = new Notification();
        $notification->setTitle($title)
            ->setMessage($message)
            ->setType($type)
            ->setUser($product->getUser())
            ->setProduct($product)
            ->setIsRead(false);

        $this->entityManager->persist($notification);
    }
}