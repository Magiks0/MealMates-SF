<?php

namespace App\DataFixtures;

use App\Entity\Dietetic;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class DieteticFixtures extends Fixture
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
        ];

        foreach ($diets as $dietData) {
            $diet = new Dietetic();
            $diet->setName($dietData['name']);
            $manager->persist($diet);
        }

        $manager->flush();
    }
}