<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Product;
use App\Entity\User;
use App\Entity\Type;
use App\Entity\Dietary;
use App\Entity\Address;
use DateTime;

class ProductFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // Création de quelques adresses
        $addresses = [
            [
                'name' => '20 rue de la Paix, 75002 Paris',
                'latitude' => 48.8697,
                'longitude' => 2.3322
            ],
            [
                'name' => '55 rue de la République, 69002 Lyon',
                'latitude' => 45.7640,
                'longitude' => 4.8357
            ],
            [
                'name' => '10 rue des Lilas, 31000 Toulouse',
                'latitude' => 43.6045,
                'longitude' => 1.4442
            ]
        ];

        $addressEntities = [];
        foreach ($addresses as $addressData) {
            $address = new Address();
            $address->setName($addressData['name']);
            $address->setLatitude($addressData['latitude']);
            $address->setLongitude($addressData['longitude']);
            $manager->persist($address);
            $addressEntities[] = $address;
        }

        $products = [
            [
                'title' => 'Lot de carottes fraîches',
                'description' => 'Carottes locales récoltées du jour. À consommer de préférence dans les prochains jours.',
                'quantity' => 10,
                'peremptionDate' => new DateTime('+5 days'),
                'price' => 2,
                'donation' => false,
                'collectionDate' => new DateTime('+2 days'),
                'user' => $this->getReference(UserFixtures::REFERENCE_IDENTIFIER.'1', User::class),
                'type' => $this->getReference(TypeFixtures::REFERENCE_IDENTIFIER.'0', Type::class), // Légumes
                'dietetic' => $this->getReference(DietaryFixtures::REFERENCE_IDENTIFIER.'0', Dietary::class), // Végétarien
                'address' => $addressEntities[0]
            ],
            [
                'title' => 'Pain aux céréales fait maison',
                'description' => 'Pain aux céréales fait maison ce matin. Parfait pour accompagner vos repas.',
                'quantity' => 2,
                'peremptionDate' => new DateTime('+2 days'),
                'price' => 3.50,
                'donation' => false,
                'collectionDate' => new DateTime('+1 days'),
                'user' => $this->getReference(UserFixtures::REFERENCE_IDENTIFIER.'0', User::class),
                'type' => $this->getReference(TypeFixtures::REFERENCE_IDENTIFIER.'5', Type::class), // Plats préparés
                'dietetic' => $this->getReference(DietaryFixtures::REFERENCE_IDENTIFIER.'2', Dietary::class), // Sans gluten
                'address' => $addressEntities[1]
            ],
            [
                'title' => 'Yaourts nature bio',
                'description' => 'Yaourts nature bio artisanaux. Lot de 4 pots en verre consignés.',
                'quantity' => 4,
                'peremptionDate' => new DateTime('+7 days'),
                'price' => 4,
                'donation' => false,
                'collectionDate' => new DateTime('+3 days'),
                'user' => $this->getReference(UserFixtures::REFERENCE_IDENTIFIER.'2', User::class),
                'type' => $this->getReference(TypeFixtures::REFERENCE_IDENTIFIER.'4', Type::class), // Produits laitiers
                'dietetic' => $this->getReference(DietaryFixtures::REFERENCE_IDENTIFIER.'7', Dietary::class), // Sans lactose
                'address' => $addressEntities[2]
            ],
            [
                'title' => 'Pommes Golden Bio',
                'description' => 'Pommes Golden bio du verger local. Parfaites pour les desserts ou en encas.',
                'quantity' => 6,
                'peremptionDate' => new DateTime('+14 days'),
                'price' => 0,
                'donation' => true,
                'collectionDate' => new DateTime('+1 days'),
                'user' => $this->getReference(UserFixtures::REFERENCE_IDENTIFIER.'1', User::class),
                'type' => $this->getReference(TypeFixtures::REFERENCE_IDENTIFIER.'1', Type::class), // Fruits
                'dietetic' => $this->getReference(DietaryFixtures::REFERENCE_IDENTIFIER.'0', Dietary::class), // Végétarien
                'address' => $addressEntities[0]
            ],
            [
                'title' => 'Fraises de saison',
                'description' => 'Fraises fraîches cultivées sans pesticides. Idéales pour vos desserts estivaux.',
                'quantity' => 500, 
                'peremptionDate' => new DateTime('+3 days'),
                'price' => 3.50,
                'donation' => false,
                'collectionDate' => new DateTime('+1 days'),
                'user' => $this->getReference(UserFixtures::REFERENCE_IDENTIFIER.'2', User::class),
                'type' => $this->getReference(TypeFixtures::REFERENCE_IDENTIFIER.'1', Type::class), // Fruits
                'dietetic' => $this->getReference(DietaryFixtures::REFERENCE_IDENTIFIER.'0', Dietary::class), // Végétarien
                'address' => $addressEntities[2]
            ]
        ];

        foreach ($products as $productData) {
            $product = new Product();
            $product->setTitle($productData['title']);
            $product->setDescription($productData['description']);
            $product->setQuantity($productData['quantity']);
            $product->setPeremptionDate($productData['peremptionDate']);
            $product->setPrice($productData['price']);
            $product->setDonation($productData['donation']);
            $product->setCollectionDate($productData['collectionDate']);
            $product->setUser($productData['user']);
            $product->setType($productData['type']);
            $product->addDietary($productData['dietetic']);
            $product->setAddress($productData['address']);
            $product->setCreatedAt(new DateTime());
            $product->setUpdatedAt(new DateTime());
            $manager->persist($product);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            TypeFixtures::class,
            DietaryFixtures::class,
        ];
    }
}