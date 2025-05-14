<?php

namespace App\Command;

use App\Entity\Notification;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

#[AsCommand(
    name: 'products:expiry-alert',
    description: 'Generate expiry notifications for sellers'
)]
class ProductsExpiryAlertCommand extends Command
{
    public function __construct(
        private ProductRepository     $products,
        private EntityManagerInterface $em,
        private ParameterBagInterface  $params,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $days  = (int) $this->params->get('app.alert_expiry_days', 1);
        $limit = (new \DateTimeImmutable())->modify("+$days days")->setTime(0, 0);

        $soon  = $this->products->findExpiringBefore($limit);
        $count = 0;

        foreach ($soon as $product) {
            $notif = (new Notification())
                ->setUser($product->getSeller())               // adapte au nom du champ
                ->setMessage(sprintf(
                    'Votre produit « %s » expire bientôt.',
                    $product->getTitle()
                ))
                ->setType('expiry')
                ->setTargetId($product->getId());

            $this->em->persist($notif);
            ++$count;
        }

        $this->em->flush();
        $output->writeln("<info>$count notification(s) créées</info>");

        return Command::SUCCESS;
    }
}
