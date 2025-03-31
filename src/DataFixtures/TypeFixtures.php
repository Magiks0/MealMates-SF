<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Type;

class TypeFixtures extends Fixture
{
    public const REFERENCE_IDENTIFIER = "type_";

    public function load(ObjectManager $manager): void
    {
        $types = [
            ['name' => 'Légumes'],
            ['name' => 'Fruits'],
            ['name' => 'Viandes'],
            ['name' => 'Poissons & Fruits de mer'],
            ['name' => 'Produits laitiers'],
            ['name' => 'Plats préparés'],
            ['name' => 'Céréales & Légumineuses'],
            ['name' => 'Boissons'],
        ];

        foreach ($types as $i => $typeData) {
            $type = new Type();
            $type->setName($typeData['name']);
            $manager->persist($type);
            $this->addReference(self::REFERENCE_IDENTIFIER.$i, $type);
        }

        $manager->flush();
    }
}
