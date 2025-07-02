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

        if (!empty($filters['maxPrice'])) {
            $qb
                ->andWhere('p.price <= :maxPrice')
                ->setParameter('maxPrice', $filters['maxPrice']);
        }

        if (!empty($filters['dietaries'])) {
            $dietaryIds = explode(',', $filters['dietaries']);
            $qb
                ->join('p.dietaries', 'd')
                ->andWhere('d.id IN (:dietaries)')
                ->setParameter('dietaries', $dietaryIds);
        }

        if (!empty($filters['peremptionDate'])) {
            $qb
                ->andWhere('p.peremptionDate >= :peremptionDate')
                ->setParameter('peremptionDate', $filters['peremptionDate']);
        }

        if (!empty($filters['types'])) {
            $typeIds = explode(',', $filters['types']);
            $qb
                ->innerJoin('p.type', 't')
                ->andWhere('t.id IN (:types)')
                ->setParameter('types', $typeIds);
        }

        if (!empty($filters['latitude']) && !empty($filters['longitude']) && !empty($filters['radius'])) {
            return $this->findProductsNearby(
                $filters['latitude'],
                $filters['longitude'],
                $filters['radius']
            );
        }

        if (!empty($filters['keyword'])) {
            $kw = trim($filters['keyword']);
            $qb->andWhere('LOWER(p.title)       LIKE :kw
                      OR LOWER(p.description) LIKE :kw')
                ->setParameter('kw', '%' . mb_strtolower($kw) . '%');
        }

        if ($user) {
            $qb->leftJoin('App\Entity\FavoriteProduct', 'fav', 'WITH', 'fav.product = p AND fav.user = :currentUser')
                ->setParameter('currentUser', $user)
                ->addSelect('CASE WHEN fav.id IS NOT NULL THEN true ELSE false END as HIDDEN isFavorite');
        }

        $qb->innerJoin('p.user', 'u')
            ->andWhere('u.id NOT LIKE :userId')
            ->setParameter('userId', $user->getId());

        return $qb->orderBy('p.createdAt', 'DESC')->getQuery()->getResult();
    }

    public function findProductsNearby(float $latitude, float $longitude, float $radius): array
    {
        $kmInLat = 0.009; // Environ 1km en latitude
        $kmInLon = 0.009 / cos(deg2rad($latitude)); // Ajustement pour la longitude basé sur la latitude
        
        $latMin = $latitude - ($radius * $kmInLat);
        $latMax = $latitude + ($radius * $kmInLat);
        $lonMin = $longitude - ($radius * $kmInLon);
        $lonMax = $longitude + ($radius * $kmInLon);
        
        $qb = $this->createQueryBuilder('p')
            ->join('p.address', 'a')
            ->where('a.latitude BETWEEN :latMin AND :latMax')
            ->andWhere('a.longitude BETWEEN :lonMin AND :lonMax')
            ->setParameter('latMin', $latMin)
            ->setParameter('latMax', $latMax)
            ->setParameter('lonMin', $lonMin)
            ->setParameter('lonMax', $lonMax)
            ->orderBy('p.createdAt', 'DESC');
        
        return $qb->getQuery()->getResult();
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

    public function findByUser(User $user): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.user = :user')
            ->setParameter('user', $user)
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
