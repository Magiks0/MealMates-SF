<?php

namespace App\Repository;

use App\Entity\Product;
use Cocur\Slugify\Slugify;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function findFilteredProducts(array $filters)
    {
        $qb = $this->createQueryBuilder('p');

        if (!empty($filters['minPrice'])) {
            $qb
                ->andWhere('p.price >= :minPrice')
                ->setParameter('minPrice', $filters['minPrice']);
        };

        if (!empty($filters['maxPrice'])) {
            $qb
                ->andWhere('p.price <= :maxPrice')
                ->setParameter('maxPrice', $filters['maxPrice']);
        };

        if (!empty($filters['dietetic'])) {
            $qb
                ->join('p.dietetic', 'd')
                ->andWhere('d.name = :dietetic')
                ->setParameter('dietetic', $filters['dietetic']);
        };

        if (!empty($filters['peremptionDate'])) {
            $qb
                ->andWhere('p.peremptionDate >= :peremptionDate')
                ->setParameter('peremptionDate', $filters['peremptionDate']);
        };

        if (!empty($filters['types'])) {
            $typeIds = explode(',', $filters['types']);
            $qb
                ->innerJoin('p.type', 't')
                ->andWhere('t.id IN (:types)')
                ->setParameter('types', $typeIds);
        }

        return $qb->getQuery()->getResult();
    }

    public function findLastChanceProducts(): array
    {
        $now = new \DateTime();
        $tomorrow = (clone $now)->modify('+1 day')->setTime(0, 0, 0);
        $dayAfterTomorrow = (clone $now)->modify('+2 day')->setTime(23, 59, 59);

        return $this->createQueryBuilder('p')
            ->where('p.peremptionDate BETWEEN :start AND :end')
            ->setParameter('start', $tomorrow)
            ->setParameter('end', $dayAfterTomorrow)
            ->getQuery()
            ->getResult();
    }

    public function findRecentProducts(): array
    {
        $yesterdayStart = (new \DateTime())->modify('-1 day')->setTime(0, 0, 0);
        $now = new \DateTime();

        return $this->createQueryBuilder('p')
            ->where('p.createdAt BETWEEN :start AND :end')
            ->setParameter('start', $yesterdayStart)
            ->setParameter('end', $now)
            ->getQuery()
            ->getResult();
    }
}
