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
            ->where('c.user1 = :userId')
            ->orWhere('c.user2 = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('c.updatedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findChatBetweenUsersAndProduct(int $user1Id, int $user2Id, int $productId): ?Chat
    {
        return $this->createQueryBuilder('c')
            ->where('(c.user1 = :user1Id AND c.user2 = :user2Id)')
            ->orWhere('(c.user1 = :user2Id AND c.user2 = :user1Id)')
            ->andWhere('c.product = :productId')
            ->setParameter('user1Id', $user1Id)
            ->setParameter('user2Id', $user2Id)
            ->setParameter('productId', $productId)
            ->getQuery()
            ->getOneOrNullResult();
    }
}