<?php

namespace App\Command;

use App\Entity\Notification;
use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Repository\NotificationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:test-expiration-notifications',
    description: 'Test manuel du système de notifications d\'expiration',
)]
class TestExpirationNotificationCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ProductRepository $productRepository,
        private NotificationRepository $notificationRepository
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Simuler sans créer de notifications')
            ->addOption('force', null, InputOption::VALUE_NONE, 'Ignorer les notifications existantes')
            ->addOption('days', null, InputOption::VALUE_REQUIRED, 'Nombre de jours à vérifier (défaut: 1)', '1')
            ->addOption('user-id', null, InputOption::VALUE_REQUIRED, 'ID d\'un utilisateur spécifique')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $dryRun = $input->getOption('dry-run');
        $force = $input->getOption('force');
        $days = (int) $input->getOption('days');
        $userId = $input->getOption('user-id');

        $io->title('Test du système de notifications d\'expiration');

        // Dates à vérifier
        $dates = [];
        $today = new \DateTime();
        $today->setTime(0, 0, 0);

        for ($i = 0; $i <= $days; $i++) {
            $date = clone $today;
            if ($i > 0) {
                $date->modify("+{$i} day");
            }
            $dates[] = $date;
        }

        $io->section('Recherche des produits qui expirent...');
        
        $totalProducts = 0;
        $notificationsCreated = 0;

        foreach ($dates as $date) {
            $qb = $this->productRepository->createQueryBuilder('p')
                ->where('p.published = true')
                ->andWhere('p.peremptionDate = :date')
                ->setParameter('date', $date);

            if ($userId) {
                $qb->andWhere('p.user = :userId')
                   ->setParameter('userId', $userId);
            }

            $products = $qb->getQuery()->getResult();

            $count = count($products);
            $totalProducts += $count;

            if ($count > 0) {
                $io->text(sprintf(
                    '📅 %s : %d produit(s) trouvé(s)',
                    $date->format('d/m/Y'),
                    $count
                ));

                foreach ($products as $product) {
                    $notificationCreated = $this->processProduct($product, $date, $today, $io, $dryRun, $force);
                    if ($notificationCreated) {
                        $notificationsCreated++;
                    }
                }
            }
        }

        if (!$dryRun) {
            $this->entityManager->flush();
        }

        $io->newLine();
        $io->success(sprintf(
            'Terminé ! %d produit(s) analysé(s), %d notification(s) créée(s)%s',
            $totalProducts,
            $notificationsCreated,
            $dryRun ? ' (mode simulation)' : ''
        ));

        // Afficher un résumé des notifications créées
        if ($notificationsCreated > 0 && !$dryRun) {
            $io->section('Résumé des notifications créées :');
            
            $notifications = $this->notificationRepository->createQueryBuilder('n')
                ->orderBy('n.notifiedAt', 'DESC')
                ->setMaxResults($notificationsCreated)
                ->getQuery()
                ->getResult();

            foreach ($notifications as $notification) {
                $io->text(sprintf(
                    '- %s : %s',
                    $notification->getUser()->getEmail(),
                    $notification->getTitle()
                ));
            }
        }

        return Command::SUCCESS;
    }

    private function processProduct(
        Product $product, 
        \DateTime $expirationDate, 
        \DateTime $today, 
        SymfonyStyle $io, 
        bool $dryRun,
        bool $force
    ): bool {
        $daysDiff = $today->diff($expirationDate)->days;
        $isToday = $expirationDate->format('Y-m-d') === $today->format('Y-m-d');

        if ($isToday) {
            $type = Notification::TYPE_EXPIRATION_TODAY;
            $title = "⚠️ Produit expire aujourd'hui !";
            $message = sprintf(
                "Votre produit '%s' expire aujourd'hui (%s). Il est urgent de le vendre ou de le consommer !",
                $product->getTitle(),
                $expirationDate->format('d/m/Y')
            );
        } elseif ($daysDiff === 1) {
            $type = Notification::TYPE_EXPIRATION_TOMORROW;
            $title = "⏰ Produit expire demain";
            $message = sprintf(
                "Votre produit '%s' expire demain (%s). Pensez à le vendre rapidement.",
                $product->getTitle(),
                $expirationDate->format('d/m/Y')
            );
        } else {
            $type = 'expiration_future';
            $title = sprintf("📅 Produit expire dans %d jours", $daysDiff);
            $message = sprintf(
                "Votre produit '%s' expire le %s (dans %d jours).",
                $product->getTitle(),
                $expirationDate->format('d/m/Y'),
                $daysDiff
            );
        }

        // Vérifier si une notification existe déjà
        if (!$force && $this->notificationRepository->existsForProductAndType($product, $type, new \DateTime())) {
            $io->text(sprintf(
                '   ⏭️  %s - %s (notification déjà envoyée)',
                $product->getTitle(),
                $product->getUser()->getEmail()
            ));
            return false;
        }

        $io->text(sprintf(
            '   📧 %s - %s (%s)%s',
            $product->getTitle(),
            $product->getUser()->getEmail(),
            $type,
            $dryRun ? ' [SIMULATION]' : ''
        ));

        if (!$dryRun) {
            $notification = new Notification();
            $notification->setTitle($title)
                ->setMessage($message)
                ->setType($type)
                ->setUser($product->getUser())
                ->setProduct($product)
                ->setIsRead(false);

            $this->entityManager->persist($notification);
        }

        return true;
    }
}