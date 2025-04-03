<?php 
namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Availabilities;
use App\Entity\User;
use App\Enum\DayOfWeek;
use DateTime;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class AvailabilitiesFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $user = $this->getReference('user_1', User::class); 

        $availabilities = [
            ['day_of_week' => DayOfWeek::MONDAY, 'min_time' => new DateTime("08:00:00"), 'max_time' => new DateTime("18:00:00")],
            ['day_of_week' => DayOfWeek::TUESDAY, 'min_time' => new DateTime("09:00:00"), 'max_time' => new DateTime("17:00:00")],
        ];

        foreach ($availabilities as $availabilityData) {
            $availability = new Availabilities();
            $availability->setUser($user);
            $availability->setDayOfWeek($availabilityData['day_of_week']);
            $availability->setMinTime($availabilityData['min_time']);
            $availability->setMaxTime($availabilityData['max_time']);

            $manager->persist($availability);
        }

        $manager->flush();
    }
    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
    
}
