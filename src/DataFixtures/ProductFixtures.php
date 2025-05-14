<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Product;
use App\Entity\User;
use App\Entity\Type;
use App\Entity\Dietary;
use DateTime;

class ProductFixtures extends Fixture implements DependentFixtureInterface
{
    public const REFERENCE_IDENTIFIER = "product_";

    public function load(ObjectManager $manager): void
    {
        $products = [
            [
                'title' => 'Pommes Bio',
                'description' => 'Pommes fraîches et biologiques',
                'quantity' => 10,
                'peremptionDate' => new DateTime('+10 days'),
                'price' => 5,
                'donation' => false,
                'collectionDate' => new DateTime('+2 days'),
                'user' => $this->getReference(UserFixtures::REFERENCE_IDENTIFIER.'1', User::class),
                'type' => $this->getReference(TypeFixtures::REFERENCE_IDENTIFIER.'1', Type::class),
                'dietetic' => $this->getReference(DietaryFixtures::REFERENCE_IDENTIFIER.'0', Dietary::class),
            ],
            [
                'title' => 'Riz Complet',
                'description' => 'Riz complet riche en fibres',
                'quantity' => 20,
                'peremptionDate' => new DateTime('+1 year'),
                'price' => 3,
                'donation' => false,
                'collectionDate' => new DateTime('+3 days'),
                'user' => $this->getReference(UserFixtures::REFERENCE_IDENTIFIER.'0', User::class),
                'type' => $this->getReference(TypeFixtures::REFERENCE_IDENTIFIER.'5', Type::class),
                'dietetic' => $this->getReference(DietaryFixtures::REFERENCE_IDENTIFIER.'0', Dietary::class),
            ],
            [
                'title' => 'Fromage de Chèvre',
                'description' => 'Fromage artisanal de chèvre',
                'quantity' => 5,
                'peremptionDate' => new DateTime('+15 days'),
                'price' => 8,
                'donation' => false,
                'collectionDate' => new DateTime('+4 days'),
                'user' => $this->getReference(UserFixtures::REFERENCE_IDENTIFIER.'2', User::class),
                'type' => $this->getReference(TypeFixtures::REFERENCE_IDENTIFIER.'4',Type::class),
                'dietetic' => $this->getReference(DietaryFixtures::REFERENCE_IDENTIFIER.'7', Dietary::class),
            ]
        ];

        foreach ($products as $i => $productData) {
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
            $product->setCreatedAt(new DateTime());
            $product->setUpdatedAt(new DateTime());
            $manager->persist($product);
            // Voici ma référence de produit 
            $this->addReference(self::REFERENCE_IDENTIFIER.$i, $product);
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
