<?php

namespace App\Command;

use App\Entity\Notification;
use App\Repository\NotificationRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:check-expiration',
    description: 'Vérifie les produits qui expirent demain et crée des notifications',
)]
class CheckExpirationCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ProductRepository $productRepository,
        private readonly NotificationRepository $notificationRepository,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Vérification des produits arrivant à expiration');

        $tomorrow = new \DateTime('tomorrow');
        $io->info('Recherche des produits expirant avant le : ' . $tomorrow->format('Y-m-d'));

        $expiringProducts = $this->productRepository->createQueryBuilder('p')
            ->where('p.peremptionDate < :tomorrow')
            ->setParameter('tomorrow', $tomorrow)
            ->getQuery()
            ->getResult();

        $io->info(sprintf('Produits trouvés : %d', count($expiringProducts)));

        $notificationsCreated = 0;

        foreach ($expiringProducts as $product) {
            if ($this->notificationRepository->existsForProduct($product->getId())) {
                $io->writeln(sprintf('⏭️  Notification déjà créée pour : %s', $product->getTitle()));
                continue;
            }

            $notification = new Notification();
            $notification->setUser($product->getUser());
            $notification->setProduct($product);
            $notification->setMessage(sprintf(
                'Votre produit "%s" expire bientôt (le %s) ! Pensez à le vendre rapidement.',
                $product->getTitle(),
                $product->getPeremptionDate()->format('d/m/Y')
            ));

            $this->entityManager->persist($notification);
            $notificationsCreated++;

            $io->writeln(sprintf(
                '✅ Notification créée pour : %s (expire le %s)',
                $product->getTitle(),
                $product->getPeremptionDate()->format('d/m/Y')
            ));
        }

        // Sauvegarder toutes les notifications
        $this->entityManager->flush();

        $io->success(sprintf('Terminé ! %d notification(s) créée(s).', $notificationsCreated));

        return Command::SUCCESS;
    }
}