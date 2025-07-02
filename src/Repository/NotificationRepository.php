<?php

namespace App\Repository;

use App\Entity\Notification;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Notification>
 */
class NotificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notification::class);
    }

    /**
     * Trouve les notifications non lues pour un utilisateur
     */
    public function findUnreadByUser(User $user): array
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.user = :user')
            ->andWhere('n.isRead = false')
            ->setParameter('user', $user)
            ->orderBy('n.notifiedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve toutes les notifications pour un utilisateur
     */
    public function findByUser(User $user, int $limit = 50): array
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.user = :user')
            ->setParameter('user', $user)
            ->orderBy('n.notifiedAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Vérifie si une notification existe déjà pour un produit et un type donné
     */
    public function existsForProductAndType(Product $product, string $type, \DateTime $date): bool
    {
        $startOfDay = clone $date;
        $startOfDay->setTime(0, 0, 0);
        
        $endOfDay = clone $date;
        $endOfDay->setTime(23, 59, 59);

        $result = $this->createQueryBuilder('n')
            ->select('COUNT(n.id)')
            ->andWhere('n.product = :product')
            ->andWhere('n.type = :type')
            ->andWhere('n.notifiedAt BETWEEN :start AND :end')
            ->setParameter('product', $product)
            ->setParameter('type', $type)
            ->setParameter('start', $startOfDay)
            ->setParameter('end', $endOfDay)
            ->getQuery()
            ->getSingleScalarResult();

        return $result > 0;
    }

    /**
     * Marque toutes les notifications comme lues pour un utilisateur
     */
    public function markAllAsReadForUser(User $user): void
    {
        $this->createQueryBuilder('n')
            ->update()
            ->set('n.isRead', ':read')
            ->where('n.user = :user')
            ->andWhere('n.isRead = false')
            ->setParameter('read', true)
            ->setParameter('user', $user)
            ->getQuery()
            ->execute();
    }

    /**
     * Compte les notifications non lues pour un utilisateur
     */
    public function countUnreadForUser(User $user): int
    {
        return $this->createQueryBuilder('n')
            ->select('COUNT(n.id)')
            ->andWhere('n.user = :user')
            ->andWhere('n.isRead = false')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Supprime toutes les notifications lues pour un utilisateur
     */
    public function deleteReadForUser(User $user): int
    {
        return $this->createQueryBuilder('n')
            ->delete()
            ->where('n.user = :user')
            ->andWhere('n.isRead = true')
            ->setParameter('user', $user)
            ->getQuery()
            ->execute();
    }
}