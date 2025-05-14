<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class UserFixtures extends Fixture implements DependentFixtureInterface
{
    public const REFERENCE_IDENTIFIER = "user_";

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $users = [
            [
            'email' => 'user1@example.com',
            'password' => 'xxx',
            'isVerified' => true,
            'address' => '10 Rue de Paris, Senlis',
            'imageUrl' => 'https://example.com/images/user1.jpg',
            'note' => random_int(2, 5),
            'username' => 'user1'
            ],
            [
            'email' => 'user2@example.com',
            'password' => 'xxx',
            'isVerified' => false,
            'address' => '15 Rue de la République, Senlis',
            'imageUrl' => 'https://example.com/images/user2.jpg',
            'note' => random_int(2, 5),
            'username' => 'user2'
            ],
            [
            'email' => 'user3@example.com',
            'password' => 'xxx',
            'isVerified' => true,
            'address' => '20 Avenue des Jardins, Senlis',
            'imageUrl' => 'https://example.com/images/user3.jpg',
            'note' => random_int(2, 5),
            'username' => 'user3'
            ],
            [
            'email' => 'user4@example.com',
            'password' => 'xxx',
            'isVerified' => false,
            'address' => '5 Place de l’Église, Senlis',
            'imageUrl' => 'https://example.com/images/user4.jpg',
            'note' => random_int(2, 5),
            'username' => 'user4'
            ]
        ];

        foreach ($users as $i => $userData) {
            $user = new User();
            $user->setEmail($userData['email']);
            $user->setPassword($userData['password']);
            $user->setIsVerified($userData['isVerified']);
            $user->setAddress($userData['address']);
            $user->setImageUrl($userData['imageUrl']);
            $user->setNote($userData['note']);
            $user->setUsername($userData['username']);
            $manager->persist($user);
            $this->addReference(self::REFERENCE_IDENTIFIER.$i, $user);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            DietaryFixtures::class,
        ];
    }
}
