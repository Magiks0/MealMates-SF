<?php

namespace App\Repository;

use App\Entity\Product;
use App\Entity\User;
use Cocur\Slugify\Slugify;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;
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

    public function findFilteredProducts(array $filters, User $user)
    {
        $qb = $this
            ->createQueryBuilder('p')
            ->where('p.published = true')
        ;

        if (!empty($filters['minPrice'])) {
            $qb
                ->andWhere('p.price >= :minPrice')
                ->setParameter('minPrice', $filters['minPrice']);
        }
        ;

        if (!empty($filters['maxPrice'])) {
            $qb
                ->andWhere('p.price <= :maxPrice')
                ->setParameter('maxPrice', $filters['maxPrice']);
        }
        ;

        if (!empty($filters['dietary'])) {
            $qb
                ->join('p.dietaries', 'd')
                ->andWhere('d.name = :dietetic')
                ->setParameter('dietetic', $filters['dietetic']);
        }
        ;

        if (!empty($filters['peremptionDate'])) {
            $qb
                ->andWhere('p.peremptionDate >= :peremptionDate')
                ->setParameter('peremptionDate', $filters['peremptionDate']);
        }
        ;

        if (!empty($filters['types'])) {
            $typeIds = explode(',', $filters['types']);
            $qb
                ->innerJoin('p.type', 't')
                ->andWhere('t.id IN (:types)')
                ->setParameter('types', $typeIds);
        }

        if (!empty($filters['keyword'])) {
            $kw = trim($filters['keyword']);
            $qb->andWhere('LOWER(p.title)       LIKE :kw
                      OR LOWER(p.description) LIKE :kw')
                ->setParameter('kw', '%' . mb_strtolower($kw) . '%');
        }

        $qb->innerJoin('p.user', 'u')
            ->andWhere('u.id NOT LIKE :userId')
            ->setParameter('userId', $user->getId());

        return $qb->orderBy('p.createdAt', 'DESC')->getQuery()->getResult();
    }

    public function findLastChanceProducts(User $user): array
    {
        $now = new \DateTime();
        $tomorrow = (clone $now)->modify('+1 day')->setTime(0, 0, 0);
        $dayAfterTomorrow = (clone $now)->modify('+2 day')->setTime(23, 59, 59);

        return $this->createQueryBuilder('p')
            ->where('p.peremptionDate BETWEEN :start AND :end')
            ->innerJoin('p.user', 'u')
            ->andWhere('u.id NOT LIKE :userId')
            ->setParameter('userId', $user->getId())
            ->setParameter('start', $tomorrow)
            ->setParameter('end', $dayAfterTomorrow)
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    public function findRecentProducts(User $user): array
    {
        $yesterdayStart = (new \DateTime())->modify('-1 day')->setTime(0, 0, 0);
        $now = new \DateTime();

        return $this->createQueryBuilder('p')
            ->where('p.createdAt BETWEEN :start AND :end')
            ->innerJoin('p.user', 'u')
            ->andWhere('u.id NOT LIKE :userId')
            ->orderBy('p.createdAt', 'DESC')
            ->setMaxResults(10)
            ->setParameter('userId', $user->getId())
            ->setParameter('start', $yesterdayStart)
            ->setParameter('end', $now)
            ->getQuery()
            ->getResult();
    }

    public function findDetailedProduct(string $id): ?array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.id = :id')
            ->setParameter('id', $id)
            ->leftJoin('p.user', 'u')
            ->leftJoin('p.type', 't')
            ->leftJoin('p.dietaries', 'd')
            ->leftJoin('p.address', 'a')
            ->leftJoin('p.files', 'f')
            ->addSelect('u', 't', 'd', 'a', 'f')
            ->getQuery()
            ->getResult();
    }
}
