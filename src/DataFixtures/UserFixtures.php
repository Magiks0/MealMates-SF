<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $usersData = [
            [
                'email' => 'luludupas9@gmail.com',
                'username' => 'Magiks',
                'roles' => ['ROLE_USER'],
                'password' => 'password123',
                'isVerified' => true,
                'note' => 4.5,
                'address' => '9 Rue de Janville, Mouy',
                'image_url' => 'https://randomuser.me/api/portraits/women/1.jpg',
            ],
            [
                'email' => 'tomzarb@gmail.com',
                'username' => 'Tom Zarb',
                'roles' => ['ROLE_USER'],
                'password' => 'password123',
                'isVerified' => false,
                'note' => 3.7,
                'address' => '5 Boulevard Jean-Jaurès, Lyon',
                'image_url' => 'https://randomuser.me/api/portraits/men/2.jpg',
            ],
            [
                'email' => 'floriansauvage@gmail.com',
                'username' => 'Flo',
                'roles' => ['ROLE_USER'],
                'password' => 'password123',
                'isVerified' => true,
                'note' => 4.9,
                'address' => '7 Rue de la République, Marseille',
                'image_url' => 'https://randomuser.me/api/portraits/women/3.jpg',
            ],
            [
                'email' => 'david@example.com',
                'username' => 'david',
                'roles' => ['ROLE_USER'],
                'password' => 'password123',
                'isVerified' => true,
                'note' => null,
                'address' => '3 Place de la Comédie, Montpellier',
                'image_url' => 'https://randomuser.me/api/portraits/men/4.jpg',
            ],
            [
                'email' => 'eve@example.com',
                'username' => 'eve',
                'roles' => ['ROLE_USER'],
                'password' => 'password123',
                'isVerified' => false,
                'note' => 4.1,
                'address' => '12 Rue Fesch, Ajaccio',
                'image_url' => 'https://randomuser.me/api/portraits/women/5.jpg',
            ],
        ];

        foreach ($usersData as $i => $data) {
            $user = new User();
            $user->setEmail($data['email']);
            $user->setUsername($data['username']);
            $user->setRoles($data['roles']);
            // Hash le mot de passe
            $hashedPassword = $this->passwordHasher->hashPassword($user, $data['password']);
            $user->setPassword($hashedPassword);
            $user->setIsVerified($data['isVerified']);
            $user->setNote($data['note']);
            $user->setAddress($data['address']);
            $user->setImageUrl($data['image_url']);
            $manager->persist($user);
            $this->addReference('user_'.$i, $user);
        }

        $manager->flush();
    }
}
