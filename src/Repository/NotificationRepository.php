<?php

namespace App\Repository;

use App\Entity\Notification;
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
    public function findUnreadExpiryByUser(int $userId): array
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.user = :u')
            ->andWhere('n.isRead = false')
            ->andWhere('n.type = :t')->setParameter('t', 'expiry')
            ->orderBy('n.createdAt', 'DESC')
            ->setParameter('u', $userId)
            ->getQuery()->getResult();
    }

    public function markAllRead(array $ids): int
    {
        return $this->createQueryBuilder('n')
            ->update()
            ->set('n.isRead', ':r')->setParameter('r', true)
            ->andWhere('n.id IN (:ids)')->setParameter('ids', $ids)
            ->getQuery()->execute();
    }
    //    /**
    //     * @return Notification[] Returns an array of Notification objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('n')
    //            ->andWhere('n.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('n.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Notification
    //    {
    //        return $this->createQueryBuilder('n')
    //            ->andWhere('n.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
