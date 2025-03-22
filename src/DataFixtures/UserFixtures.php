<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;
use App\Entity\Preferences;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class UserFixtures extends Fixture implements DependentFixtureInterface
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {

        for ($i = 1; $i <= 5; $i++) {
            $user = new User();
            $user->setEmail("user{$i}@example.com");
            $user->setPassword($this->passwordHasher->hashPassword($user, "password"));
            $user->setIsVerified(true);
            $user->setUsername("Username{$i}");
            $user->setAdress("{$i} rue de la Paix Paris");
            $user->setImageUrl("image{$i}.png");


            for ($j = 0; $j < 2; $j++) {
                $preference = $this->getReference('preference_' . rand(0, 4), Preferences::class);
                $user->addPreference($preference);
            }

            $manager->persist($user);
            $this->addReference('user_' . $i, $user); 
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
