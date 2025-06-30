<?php

namespace App\Repository;

use App\Entity\Chat;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Chat>
 */
class ChatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Chat::class);
    }

    public function findChatsForUser(int $userId): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.buyer = :userId')
            ->orWhere('c.seller = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('c.updatedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findChatBetweenUsersAndProduct(int $buyerId, int $sellerId, int $productId): ?Chat
    {
        return $this->createQueryBuilder('c')
            ->where('(c.buyer = :buyerId AND c.seller = :sellerId)')
            ->orWhere('(c.buyer = :buyerId AND c.seller = :sellerId)')
            ->andWhere('c.product = :productId')
            ->setParameter('buyerId', $buyerId)
            ->setParameter('sellerId', $sellerId)
            ->setParameter('productId', $productId)
            ->getQuery()
            ->getOneOrNullResult();
    }
}