<?php

namespace App\DataFixtures;

use App\Entity\Address;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class AddressFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $addresses = [
            [
                'address' => '12 Rue de la République, 60300 Senlis',
                'latitude' => 49.2072,
                'longitude' => 2.5847,
            ],
            [
                'address' => '5 Place Henri IV, 60300 Senlis',
                'latitude' => 49.2079,
                'longitude' => 2.5836,
            ],
            [
                'address' => '3 Rue du Châtel, 60300 Senlis',
                'latitude' => 49.2068,
                'longitude' => 2.5830,
            ],
        ];

        foreach ($addresses as $addressData) {
            $address = new Address();
            $address->setName($addressData['address']);
            $address->setLatitude($addressData['latitude']);
            $address->setLongitude($addressData['longitude']);
            
            $manager->persist($address);
        }

        $manager->flush();
    }
}