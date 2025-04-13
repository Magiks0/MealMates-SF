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
                'product_reference' => 'product_1'
            ],
            [
                'adress' => '5 Place Henri IV, 60300 Senlis',
                'latitude' => 49.2079,
                'longitude' => 2.5836,
                'product_reference' => 'product_2'
            ],
            [
                'adress' => '3 Rue du Châtel, 60300 Senlis',
                'latitude' => 49.2068,
                'longitude' => 2.5830,
                'product_reference' => 'product_3'
            ],
            [
                'adress' => '18 Avenue de Creil, 60300 Senlis',
                'latitude' => 49.2096,
                'longitude' => 2.5750,
                'product_reference' => 'product_4'
            ],
            [
                'adress' => '2 Avenue des Chevreuils, 60300 Senlis',
                'latitude' => 49.1980,
                'longitude' => 2.5820,
                'product_reference' => 'product_5'
            ],
            [
                'adress' => '7 Rue Saint-Pierre, 60300 Senlis',
                'latitude' => 49.2070,
                'longitude' => 2.5865,
                'product_reference' => 'product_6'
            ],
            [
                'adress' => '22 Avenue de Chantilly, 60300 Senlis',
                'latitude' => 49.2036,
                'longitude' => 2.5960,
                'product_reference' => 'product_7'
            ],
            [
                'adress' => '15 Rue Bellon, 60300 Senlis',
                'latitude' => 49.2060,
                'longitude' => 2.5840,
                'product_reference' => 'product_8'
            ],
            [
                'adress' => '6 Rue du Puits Tiphaine, 60300 Senlis',
                'latitude' => 49.2075,
                'longitude' => 2.5823,
                'product_reference' => 'product_9'
            ],
            [
                'adress' => '10 Rue des Jardiniers, 60300 Senlis',
                'latitude' => 49.2045,
                'longitude' => 2.5900,
                'product_reference' => 'product_10'
            ],
        ];

        foreach ($locations as $locationData) {
            $location = new Location();
            $location->setAdress($locationData['adress']);
            $location->setLatitude($locationData['latitude']);
            $location->setLongitude($locationData['longitude']);
            
            $product = $this->getReference($locationData['product_reference']);
            $location->setProduct($product);
            
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