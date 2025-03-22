<?php
namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Preferences;

class PreferencesFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $preferences = [
            "Vegetarian",
            "Vegan",
            "Gluten-Free",
            "Halal",
            "Kosher"
        ];

        foreach ($preferences as $index => $designation) {
            $preference = new Preferences();
            $preference->setDesignation($designation);
            $manager->persist($preference);
            $this->addReference('preference_' . $index, $preference);
        }

        $manager->flush();
    }
}
