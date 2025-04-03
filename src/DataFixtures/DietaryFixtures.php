<?php

namespace App\DataFixtures;

use App\Entity\Dietary;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class DietaryFixtures extends Fixture
{
    public const REFERENCE_IDENTIFIER = "dietetic_";

    public function load(ObjectManager $manager): void
    {
        $diets = [
            ['name' => 'Végétarien'],
            ['name' => 'Vegan'],
            ['name' => 'Sans gluten'],
            ['name' => 'Cétogène'],
            ['name' => 'Paleo'],
            ['name' => 'Méditerranéen'],
            ['name' => 'Hypocalorique'],
            ['name' => 'Sans lactose']
        ];

        foreach ($diets as $i => $dietData) {
            $diet = new Dietary();
            $diet->setName($dietData['name']);
            $manager->persist($diet);
            $this->addReference(self::REFERENCE_IDENTIFIER.$i, $diet);
        }

        $manager->flush();
    }
}