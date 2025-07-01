<?php

namespace App\Repository;

use App\Entity\Rating;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Rating>
 */
class RatingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Rating::class);
    }

    /**
     * Find all ratings received by a user
     */
    public function findRatingsReceivedByUser(User $user): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.reviewed = :user')
            ->setParameter('user', $user)
            ->orderBy('r.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Calculate average rating for a user
     */
    public function getAverageRatingForUser(User $user): ?float
    {
        $result = $this->createQueryBuilder('r')
            ->select('AVG(r.score) as average')
            ->andWhere('r.reviewed = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getOneOrNullResult();

        return $result['average'] ? round((float)$result['average'], 2) : null;
    }

    /**
     * Check if an order has already been rated
     */
    public function findByOrder(int $orderId): ?Rating
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.order = :orderId')
            ->setParameter('orderId', $orderId)
            ->getQuery()
            ->getOneOrNullResult();
    }
}