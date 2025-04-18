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
            ['email' => 'user1@example.com', 'password' => 'xxx', 'isVerified' => true],
            ['email' => 'user2@example.com', 'password' => 'xxx', 'isVerified' => false],
            ['email' => 'user3@example.com', 'password' => 'xxx', 'isVerified' => true],
            ['email' => 'user4@example.com', 'password' => 'xxx', 'isVerified' => false]
        ];

        foreach ($users as $i => $userData) {
            $user = new User();
            $user->setEmail($userData['email']);
            $user->setPassword($userData['password']);
            $user->setIsVerified($userData['isVerified']);
            $user->setNote(random_int(2,5));
            $manager->persist($user);
            $this->addReference(self::REFERENCE_IDENTIFIER.$i, $user);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            PreferencesFixtures::class,
        ];
    }
}
