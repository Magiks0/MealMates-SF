<?php

namespace App\DataFixtures;

use App\Entity\Address;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AddressFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $addressesData = [
            // Paris
            ['name' => '10 Rue de Rivoli, Paris', 'latitude' => 48.855815, 'longitude' => 2.360967],
            ['name' => '20 Avenue des Champs-Élysées, Paris', 'latitude' => 48.869867, 'longitude' => 2.307297],
            ['name' => '5 Boulevard Haussmann, Paris', 'latitude' => 48.871839, 'longitude' => 2.326561],
            ['name' => '14 Rue du Faubourg Saint-Antoine, Paris', 'latitude' => 48.852968, 'longitude' => 2.374678],
            ['name' => '27 Rue de la Paix, Paris', 'latitude' => 48.869815, 'longitude' => 2.330335],

            // Lyon
            ['name' => '5 Boulevard Jean-Jaurès, Lyon', 'latitude' => 45.758007, 'longitude' => 4.835659],
            ['name' => '12 Rue de la République, Lyon', 'latitude' => 45.757813, 'longitude' => 4.832011],
            ['name' => '8 Place Bellecour, Lyon', 'latitude' => 45.757814, 'longitude' => 4.832011],
            ['name' => '21 Quai Saint-Vincent, Lyon', 'latitude' => 45.761291, 'longitude' => 4.826753],

            // Marseille
            ['name' => '7 Rue de la République, Marseille', 'latitude' => 43.296482, 'longitude' => 5.36978],
            ['name' => '10 Cours Julien, Marseille', 'latitude' => 43.301039, 'longitude' => 5.381512],
            ['name' => '3 Boulevard Longchamp, Marseille', 'latitude' => 43.301388, 'longitude' => 5.37278],

            // Montpellier
            ['name' => '3 Place de la Comédie, Montpellier', 'latitude' => 43.611876, 'longitude' => 3.877883],
            ['name' => '15 Rue Foch, Montpellier', 'latitude' => 43.613224, 'longitude' => 3.876789],
            ['name' => '22 Avenue du Pirée, Montpellier', 'latitude' => 43.615231, 'longitude' => 3.876123],

            // Ajaccio (Corse)
            ['name' => '12 Rue Fesch, Ajaccio', 'latitude' => 41.923698, 'longitude' => 8.738525],
            ['name' => '18 Boulevard Sampiero, Ajaccio', 'latitude' => 41.922532, 'longitude' => 8.736413],

            // Nancy
            ['name' => '1 Place Stanislas, Nancy', 'latitude' => 48.692054, 'longitude' => 6.184417],
            ['name' => '8 Rue Saint-Dizier, Nancy', 'latitude' => 48.693023, 'longitude' => 6.184301],
        ];

        foreach ($addressesData as $i => $data) {
            $address = new Address();
            $address->setName($data['name']);
            $address->setLatitude($data['latitude']);
            $address->setLongitude($data['longitude']);
            $manager->persist($address);
            $this->addReference('address_'.$i, $address);
        }

        $manager->flush();
    }
}
