<?php

namespace App\DataFixtures;

use App\Entity\Address;
use App\Entity\Product;
use App\Entity\Type;
use App\Entity\User;
use App\Service\StripeService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ProductFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(private StripeService $stripeService)
    {}

    public function load(ObjectManager $manager): void
    {
        $productsData = [
            [
                'title' => 'Tomates cerises',
                'description' => 'Tomates cerises fraîches du jardin',
                'quantity' => 30,
                'peremptionDate' => new \DateTime('+5 days'),
                'price' => 4.0,
                'donation' => false,
                'collection_date' => new \DateTime('+1 day'),
                'user_reference' => 'user_1',
                'address_reference' => 'address_2',
                'type_reference' => 'type_0', // Légumes
                'published' => true,
            ],
            [
                'title' => 'Pommes Bio',
                'description' => 'Pommes bio croquantes et sucrées',
                'quantity' => 50,
                'peremptionDate' => new \DateTime('+10 days'),
                'price' => 3.5,
                'donation' => false,
                'collection_date' => new \DateTime('+2 days'),
                'user_reference' => 'user_0',
                'address_reference' => 'address_3',
                'type_reference' => 'type_1', // Fruits
                'published' => true,
            ],
            [
                'title' => 'Pain Complet',
                'description' => 'Pain complet frais du boulanger',
                'quantity' => 20,
                'peremptionDate' => new \DateTime('+2 days'),
                'price' => 2.2,
                'donation' => false,
                'collection_date' => new \DateTime('+1 day'),
                'user_reference' => 'user_2',
                'address_reference' => 'address_1',
                'type_reference' => 'type_5', // Plats préparés
                'published' => true,
            ],
            [
                'title' => 'Fromage de Chèvre',
                'description' => 'Fromage de chèvre local et crémeux',
                'quantity' => 15,
                'peremptionDate' => new \DateTime('+8 days'),
                'price' => 6.0,
                'donation' => false,
                'collection_date' => new \DateTime('+3 days'),
                'user_reference' => 'user_3',
                'address_reference' => 'address_2',
                'type_reference' => 'type_4', // Produits laitiers
                'published' => true,
            ],
            [
                'title' => 'Bananes',
                'description' => 'Bananes fraîches et mûres à point',
                'quantity' => 40,
                'peremptionDate' => new \DateTime('+7 days'),
                'price' => 2.8,
                'donation' => false,
                'collection_date' => new \DateTime('+1 day'),
                'user_reference' => 'user_1',
                'address_reference' => 'address_3',
                'type_reference' => 'type_1', // Fruits
                'published' => true,
            ],
            [
                'title' => 'Carottes',
                'description' => 'Carottes bio croquantes',
                'quantity' => 25,
                'peremptionDate' => new \DateTime('+9 days'),
                'price' => 3.0,
                'donation' => false,
                'collection_date' => new \DateTime('+2 days'),
                'user_reference' => 'user_0',
                'address_reference' => 'address_2',
                'type_reference' => 'type_0', // Légumes
                'published' => true,
            ],
            [
                'title' => 'Chocolat Noir',
                'description' => 'Tablette de chocolat noir 70%',
                'quantity' => 10,
                'peremptionDate' => new \DateTime('+30 days'),
                'price' => 5.5,
                'donation' => false,
                'collection_date' => new \DateTime('+1 day'),
                'user_reference' => 'user_3',
                'address_reference' => 'address_3',
                'type_reference' => 'type_7', // Boissons (un peu approximatif mais dans ta liste)
                'published' => true,
            ],
            [
                'title' => 'Yaourt Nature',
                'description' => 'Yaourt nature fermier',
                'quantity' => 18,
                'peremptionDate' => new \DateTime('+4 days'),
                'price' => 1.2,
                'donation' => true,
                'collection_date' => new \DateTime('+1 day'),
                'user_reference' => 'user_4',
                'address_reference' => 'address_1',
                'type_reference' => 'type_4', // Produits laitiers
                'published' => false,
            ],
            [
                'title' => 'Poulet Fermier',
                'description' => 'Poulet fermier élevé en plein air',
                'quantity' => 8,
                'peremptionDate' => new \DateTime('+3 days'),
                'price' => 12.0,
                'donation' => false,
                'collection_date' => new \DateTime('+2 days'),
                'user_reference' => 'user_2',
                'address_reference' => 'address_2',
                'type_reference' => 'type_2', // Viandes
                'published' => true,
            ],
            [
                'title' => 'Salade Verte',
                'description' => 'Salade fraîche et croquante',
                'quantity' => 35,
                'peremptionDate' => new \DateTime('+5 days'),
                'price' => 1.8,
                'donation' => true,
                'collection_date' => new \DateTime('+1 day'),
                'user_reference' => 'user_1',
                'address_reference' => 'address_3',
                'type_reference' => 'type_0', // Légumes
                'published' => true,
            ],
        ];

        foreach ($productsData as $data) {
            $product = new Product();
            $product->setTitle($data['title']);
            $product->setDescription($data['description']);
            $product->setQuantity($data['quantity']);
            $product->setPeremptionDate($data['peremptionDate']);
            $product->setPrice($data['price']);
            $product->setDonation($data['donation']);
            $product->setCollectionDate($data['collection_date']);
            $product->setPublished($data['published']);

            $user = $this->getReference($data['user_reference'], User::class);
            $product->setUser($user);

            $address = $this->getReference($data['address_reference'], Address::class);
            $product->setAddress($address);

            $type = $this->getReference($data['type_reference'], Type::class);
            $product->setType($type);

            // Création du produit Stripe
            $stripeProduct = $this->stripeService->createProduct($product);
            $product->setStripeProductId($stripeProduct->id);

            // Création du prix Stripe lié au produit
            $stripePrice = $this->stripeService->createPrice($product);
            $product->setStripePriceId($stripePrice->id);

            $manager->persist($product);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            AddressFixtures::class,
            TypeFixtures::class,
        ];
    }
}
