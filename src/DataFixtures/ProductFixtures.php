<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Product;
use App\Entity\User;
use App\Entity\Type;
use App\Entity\Dietetic;
use DateTime;

class ProductFixtures extends Fixture
{
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
                'user' => $manager->getRepository(User::class)->find(1),
                'type' => $manager->getRepository(Type::class)->find(2),
                'dietetic' => $manager->getRepository(Dietetic::class)->find(1),
            ],
            [
                'title' => 'Riz Complet',
                'description' => 'Riz complet riche en fibres',
                'quantity' => 20,
                'peremptionDate' => new DateTime('+1 year'),
                'price' => 3,
                'donation' => false,
                'collectionDate' => new DateTime('+3 days'),
                'user' => $manager->getRepository(User::class)->find(2),
                'type' => $manager->getRepository(Type::class)->find(7),
                'dietetic' => $manager->getRepository(Dietetic::class)->find(3),
            ],
            [
                'title' => 'Fromage de Chèvre',
                'description' => 'Fromage artisanal de chèvre',
                'quantity' => 5,
                'peremptionDate' => new DateTime('+15 days'),
                'price' => 8,
                'donation' => false,
                'collectionDate' => new DateTime('+4 days'),
                'user' => $manager->getRepository(User::class)->find(3),
                'type' => $manager->getRepository(Type::class)->find(5),
                'dietetic' => $manager->getRepository(Dietetic::class)->find(2),
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
            $product->setDietetic($productData['dietetic']);
            $product->setCreatedAt(new DateTime());
            $product->setUpdatedAt(new DateTime());
            $manager->persist($product);
        }

        $manager->flush();
    }
}
