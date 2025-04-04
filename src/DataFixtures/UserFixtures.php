<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public const REFERENCE_IDENTIFIER = "user_";

    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $users = [
            ['email' => 'user1@example.com', 'password' => 'xxx', 'isVerified' => true, 'location' => '[49.21057328867782, 2.581954706737226]'],
            ['email' => 'user2@example.com', 'password' => 'xxx', 'isVerified' => false, 'location' => '[49.21057328867782, 2.581954706737226]'],
            ['email' => 'user3@example.com', 'password' => 'xxx', 'isVerified' => true, 'location' => '[49.21057328867782, 2.581954706737226]'],
            ['email' => 'user4@example.com', 'password' => 'xxx', 'isVerified' => false, 'location' => '[49.21057328867782, 2.581954706737226]']
        ];

        foreach ($users as $i => $userData) {
            $user = new User();
            $user->setEmail($userData['email']);
            $user->setPassword($userData['password']);
            $user->setIsVerified($userData['isVerified']);
            $user->setLocation($userData['location']);
            $user->setNote(random_int(2,5));
            $manager->persist($user);
            $this->addReference(self::REFERENCE_IDENTIFIER.$i, $user);
        }

        $manager->flush();
    }
}
