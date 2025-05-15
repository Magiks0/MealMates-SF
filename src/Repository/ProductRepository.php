<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function findFilteredProducts(array $filters = [])
    {
        $qb = $this->createQueryBuilder('p');

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

        if (!empty($filters['dietetic'])) {
            $qb
                ->join('p.dietaries', 'd')
                ->andWhere('d.name = :dietetic')
                ->setParameter('dietetic', $filters['dietetic']);
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

        // Filtrage par coordonnées géographiques
        if (!empty($filters['latitude']) && !empty($filters['longitude']) && !empty($filters['radius'])) {
            return $this->findProductsNearby(
                $filters['latitude'],
                $filters['longitude'],
                $filters['radius']
            );
        }

        return $qb->orderBy('p.createdAt', 'DESC')->getQuery()->getResult();
    }

    /**
     * Trouve les produits à proximité d'un point géographique dans un rayon donné.
     * Utilise une approximation rectangulaire, adaptée pour des distances courtes (inférieures à 10km).
     *
     * @param float $latitude Latitude du point central
     * @param float $longitude Longitude du point central
     * @param float $radius Rayon de recherche en kilomètres
     * @return array Les produits trouvés dans le rayon spécifié
     */
    public function findProductsNearby(float $latitude, float $longitude, float $radius): array
    {
        // Conversion approximative des kilomètres en degrés (varie selon la latitude mais c'est une approximation)
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
            ->orderBy('p.createdAt', 'DESC')
            ->setParameter('start', $yesterdayStart)
            ->setParameter('end', $now)
            ->getQuery()
            ->getResult();
    }
}