<?php

namespace App\Repository;

use App\Entity\Order;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Order>
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

   public function findByBuyerAndToken(User $buyer, string $token): ?Order
   {
       return $this->createQueryBuilder('o')
           ->andWhere('o.buyer = :buyer')
           ->andWhere('o.qrCodeToken = :token')
           ->setParameter('buyer', $buyer)
           ->setParameter('token', $token)
           ->getQuery()
           ->getOneOrNullResult();
   }
}
