<?php

namespace App\DataFixtures;

use App\Entity\Location;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class LocationFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $locations = [
            [
                'adress' => '12 Rue de la République, 60300 Senlis',
                'latitude' => 49.2072,
                'longitude' => 2.5847,
                'product' => $this->getReference(ProductFixtures::REFERENCE_IDENTIFIER.'0', Product::class),
            ],
            [
                'adress' => '5 Place Henri IV, 60300 Senlis',
                'latitude' => 49.2079,
                'longitude' => 2.5836,
                'product' => $this->getReference(ProductFixtures::REFERENCE_IDENTIFIER.'1', Product::class),
            ],
            [
                'adress' => '3 Rue du Châtel, 60300 Senlis',
                'latitude' => 49.2068,
                'longitude' => 2.5830,
                'product' => $this->getReference(ProductFixtures::REFERENCE_IDENTIFIER.'2', Product::class),
            ],
        ];

        foreach ($locations as $locationData) {
            $location = new Location();
            $location->setAdress($locationData['adress']);
            $location->setLatitude($locationData['latitude']);
            $location->setLongitude($locationData['longitude']);
            $location->setProduct($locationData['product']);
            
            $manager->persist($location);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ProductFixtures::class, 
        ];
    }
}