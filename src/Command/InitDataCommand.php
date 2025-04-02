<?php

namespace App\Command;

use App\Entity\Dietetic;
use App\Entity\Product;
use App\Entity\Type;
use App\Entity\User;
use App\Entity\File;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:init-data',
    description: 'Initialization of data with complete entity relationships',
)]
class InitDataCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Importation des données réalistes avec relations complètes...');

        // --- IMPORT PRODUCT TYPES ---
        $io->section('Importation des types de produits');
        $typeEntities = [];
        $types = [
            'Fruits', 'Légumes', 'Viandes', 'Poissons & Fruits de mer', 'Produits laitiers',
            'Boulangerie & Pâtisserie', 'Épicerie salée', 'Épicerie sucrée', 'Boissons',
            'Produits surgelés', 'Condiments & Assaisonnements', 'Céréales & Petit-déjeuner',
            'Snacking & Apéritifs', 'Aliments bio & santé', 'Plats préparés',
            'Produits vegan & végétariens', 'Produits sans gluten', 'Compléments alimentaires',
            'Confiseries', 'Produits exotiques'
        ];

        foreach ($types as $typeName) {
            $type = new Type();
            $type->setName($typeName);
            $this->entityManager->persist($type);
            $typeEntities[$typeName] = $type;
        }

        // --- IMPORT DIETETICS ---
        $io->section('Importation des régimes diététiques');
        $dieteticEntities = [];
        $dietetics = [
            'Végétarien', 'Vegan', 'Sans gluten', 'Sans lactose', 'Halal',
            'Casher', 'Paleo', 'Bio', 'Riche en protéines', 'Faible en sucres',
            'Sans sel ajouté', 'Sans allergènes', 'Diabétique', 'Méditerranéen',
            'Flexitarien', 'Crudivore', 'Hypocalorique', 'Alimentation sportive'
        ];

        foreach ($dietetics as $dieteticName) {
            $dietetic = new Dietetic();
            $dietetic->setName($dieteticName);
            $this->entityManager->persist($dietetic);
            $dieteticEntities[$dieteticName] = $dietetic;
        }

        // --- IMPORT USERS ---
        $io->section('Importation des utilisateurs');
        $users = [];
        $usersData = [
            [
                'email' => 'luludupas9@gmail.com',
                'password' => 'password123',
                'username' => 'Lucas Dupas',
                'note' => 4.5
            ],
            [
                'email' => 'tomzarb98@gmail.com',
                'password' => 'password123',
                'username' => 'Tom Zarb',
                'note' => 4.2
            ],
            [
                'email' => 'florian.sauvage.etudiant@gmail.com',
                'password' => 'password123',
                'username' => 'Florian Sauvage',
                'note' => 4.7
            ],
        ];

        foreach ($usersData as $userData) {
            $user = new User();
            $user->setEmail($userData['email']);
            $hashedPassword = $this->passwordHasher->hashPassword($user, $userData['password']);
            $user->setPassword($hashedPassword);
            $user->setRoles($userData['roles'] ?? ['ROLE_USER']);
            $user->setNote($userData['note']);
            $this->entityManager->persist($user);
            $users[] = $user;
        }

        // --- IMPORT PRODUCTS ---
        $io->section('Importation des produits & création des relations');
        $products = [];
        $productsData = [
            // UTILISATEUR 1: Lucas Dupas - Produits bio et donations
            [
                'title' => 'Pommes Gala Bio',
                'price' => 250, // 2.50€
                'quantity' => 6,
                'type' => 'Fruits',
                'dietetic' => 'Bio',
                'description' => 'Pommes gala bio cultivées dans le Luberon. Sucrées et croquantes, parfaites pour une collation saine.',
                'user' => 0,
                'files' => ['pomme1.jpg', 'pomme2.jpg'],
                'created_at' => '-2 days',
                'peremption' => '+8 days',
                'collection' => '+5 days',
                'donation' => true
            ],
            [
                'title' => 'Pain aux Céréales Sans Gluten',
                'price' => 380, // 3.80€
                'quantity' => 2,
                'type' => 'Boulangerie & Pâtisserie',
                'dietetic' => 'Sans gluten',
                'description' => 'Pain artisanal aux graines et céréales sans gluten, élaboré par un artisan boulanger. Savoureux et adapté aux intolérants.',
                'user' => 0,
                'files' => ['pain1.jpg'],
                'created_at' => '-1 day',
                'peremption' => '+3 days',
                'collection' => '+2 days',
                'donation' => true
            ],
            [
                'title' => 'Lait d\'Amande Bio',
                'price' => 320, // 3.20€
                'quantity' => 3,
                'type' => 'Boissons',
                'dietetic' => 'Vegan',
                'description' => 'Lait d\'amande biologique sans sucres ajoutés. Alternative végétale idéale pour cafés, céréales ou pâtisseries.',
                'user' => 0,
                'files' => ['lait1.jpg', 'lait2.jpg'],
                'created_at' => '-3 days',
                'peremption' => '+15 days',
                'collection' => '+12 days',
                'donation' => true
            ],

            // UTILISATEUR 2: Tom Zarb - Produits premium et viandes
            [
                'title' => 'Steak Haché Charolais 5%',
                'price' => 750, // 7.50€
                'quantity' => 4,
                'type' => 'Viandes',
                'dietetic' => 'Riche en protéines',
                'description' => 'Steaks hachés pur bœuf Charolais à 5% MG. Viande française d\'exception, élevage responsable en plein air.',
                'user' => 1,
                'files' => ['steak1.jpg'],
                'created_at' => '-1 day',
                'peremption' => '+4 days',
                'collection' => '+2 days',
                'donation' => false
            ],
            [
                'title' => 'Saumon Fumé d\'Écosse',
                'price' => 995, // 9.95€
                'quantity' => 2,
                'type' => 'Poissons & Fruits de mer',
                'dietetic' => 'Méditerranéen',
                'description' => 'Saumon d\'Écosse fumé au bois de hêtre. Tranches épaisses de qualité supérieure pour vos entrées festives.',
                'user' => 1,
                'files' => ['saumon1.jpg', 'saumon2.jpg'],
                'created_at' => '-2 days',
                'peremption' => '+5 days',
                'collection' => '+3 days',
                'donation' => false
            ],
            [
                'title' => 'Riz Basmati Premium',
                'price' => 420, // 4.20€
                'quantity' => 3,
                'type' => 'Épicerie salée',
                'dietetic' => 'Sans allergènes',
                'description' => 'Riz basmati premium de l\'Himalaya, aux grains longs et parfumés. Idéal pour accompagner currys et plats exotiques.',
                'user' => 1,
                'files' => ['riz1.jpg'],
                'created_at' => '-5 days',
                'peremption' => '+180 days',
                'collection' => '+170 days',
                'donation' => false
            ],

            // UTILISATEUR 3: Florian Sauvage - Produits locaux et artisanaux
            [
                'title' => 'Miel de Lavande Artisanal',
                'price' => 890, // 8.90€
                'quantity' => 2,
                'type' => 'Épicerie sucrée',
                'dietetic' => 'Bio',
                'description' => 'Miel de lavande récolté en Provence par un apiculteur local. Notes florales prononcées et texture onctueuse.',
                'user' => 2,
                'files' => ['miel1.jpg', 'miel2.jpg'],
                'created_at' => '-4 days',
                'peremption' => '+365 days',
                'collection' => '+350 days',
                'donation' => true
            ],
            [
                'title' => 'Fromage de Chèvre Fermier',
                'price' => 650, // 6.50€
                'quantity' => 3,
                'type' => 'Produits laitiers',
                'dietetic' => 'Méditerranéen',
                'description' => 'Fromage de chèvre frais élaboré dans une ferme du Périgord selon des méthodes traditionnelles. Doux et crémeux.',
                'user' => 2,
                'files' => ['fromage1.jpg'],
                'created_at' => '-2 days',
                'peremption' => '+10 days',
                'collection' => '+7 days',
                'donation' => false
            ],
            [
                'title' => 'Noix de Cajou Toastées Bio',
                'price' => 570, // 5.70€
                'quantity' => 4,
                'type' => 'Snacking & Apéritifs',
                'dietetic' => 'Riche en protéines',
                'description' => 'Noix de cajou de qualité supérieure, légèrement toastées et salées au sel de Guérande. Idéal pour l\'apéritif.',
                'user' => 2,
                'files' => ['noix1.jpg', 'noix2.jpg'],
                'created_at' => '-7 days',
                'peremption' => '+90 days',
                'collection' => '+80 days',
                'donation' => false
            ],
            [
                'title' => 'Carottes Bio de Saison',
                'price' => 190, // 1.90€
                'quantity' => 8,
                'type' => 'Légumes',
                'dietetic' => 'Bio',
                'description' => 'Carottes bio fraîchement récoltées dans une ferme locale. Sucrées et croquantes, parfaites pour vos recettes.',
                'user' => 0,
                'files' => ['carottes1.jpg'],
                'created_at' => '-1 day',
                'peremption' => '+7 days',
                'collection' => '+5 days',
                'donation' => true
            ],
            [
                'title' => 'Yaourt Grec Nature',
                'price' => 270, // 2.70€
                'quantity' => 4,
                'type' => 'Produits laitiers',
                'dietetic' => 'Riche en protéines',
                'description' => 'Yaourt grec artisanal, onctueux et riche en protéines. Parfait pour petit-déjeuner ou recettes salées.',
                'user' => 1,
                'files' => ['yaourt1.jpg'],
                'created_at' => '-3 days',
                'peremption' => '+12 days',
                'collection' => '+9 days',
                'donation' => false
            ],
            [
                'title' => 'Chocolat Noir 85% Équitable',
                'price' => 350, // 3.50€
                'quantity' => 5,
                'type' => 'Confiseries',
                'dietetic' => 'Faible en sucres',
                'description' => 'Tablette de chocolat noir 85% issu du commerce équitable. Notes fruitées et amères, faible en sucre.',
                'user' => 2,
                'files' => ['chocolat1.jpg', 'chocolat2.jpg'],
                'created_at' => '-6 days',
                'peremption' => '+180 days',
                'collection' => '+160 days',
                'donation' => true
            ],
            [
                'title' => 'Huile d\'Olive Extra Vierge Bio',
                'price' => 780, // 7.80€
                'quantity' => 2,
                'type' => 'Condiments & Assaisonnements',
                'dietetic' => 'Bio',
                'description' => 'Huile d\'olive première pression à froid, produite en Crète selon des méthodes traditionnelles. Goût fruité intense.',
                'user' => 0,
                'files' => ['huile1.jpg'],
                'created_at' => '-10 days',
                'peremption' => '+270 days',
                'collection' => '+250 days',
                'donation' => true
            ],
            [
                'title' => 'Spaghetti Complets Bio',
                'price' => 220, // 2.20€
                'quantity' => 6,
                'type' => 'Épicerie salée',
                'dietetic' => 'Végétarien',
                'description' => 'Pâtes complètes bio, riches en fibres et protéines végétales. Temps de cuisson: 9 minutes al dente.',
                'user' => 1,
                'files' => ['pates1.jpg'],
                'created_at' => '-8 days',
                'peremption' => '+365 days',
                'collection' => '+340 days',
                'donation' => false
            ],
            [
                'title' => 'Quinoa Tricolore',
                'price' => 430, // 4.30€
                'quantity' => 3,
                'type' => 'Céréales & Petit-déjeuner',
                'dietetic' => 'Sans gluten',
                'description' => 'Mélange de quinoa blanc, rouge et noir. Source complète de protéines végétales et sans gluten naturellement.',
                'user' => 2,
                'files' => ['quinoa1.jpg'],
                'created_at' => '-5 days',
                'peremption' => '+210 days',
                'collection' => '+190 days',
                'donation' => false
            ]
        ];

        // Create products with all relationships
        foreach ($productsData as $productData) {
            $product = new Product();

            // Basic product information
            $product->setTitle($productData['title']);
            $product->setPrice($productData['price']);
            $product->setQuantity($productData['quantity']);
            $product->setDescription($productData['description']);

            // Realistic dates
            $createdAt = new \DateTime($productData['created_at']);
            $product->setCreatedAt($createdAt);
            $product->setUpdatedAt($createdAt); // Même date pour création et mise à jour initiale

            // Product specific dates
            $product->setPeremptionDate(new \DateTime($productData['peremption']));
            $product->setCollectionDate(new \DateTime($productData['collection']));

            // Donation status
            $product->setDonation($productData['donation']);

            // Link to User entity (ManyToOne)
            $product->setUser($users[$productData['user']]);

            // Link to Type entity (ManyToOne)
            if (isset($typeEntities[$productData['type']])) {
                $product->setType($typeEntities[$productData['type']]);
            }

            // Link to Dietetic entity (ManyToOne)
            if (isset($dieteticEntities[$productData['dietetic']])) {
                $product->setDietetic($dieteticEntities[$productData['dietetic']]);
            }

            $this->entityManager->persist($product);
            $products[] = $product;
        }


        $this->entityManager->flush();

        $io->success('Données importées avec succès et relations établies de façon réaliste!');

        return Command::SUCCESS;
    }
}