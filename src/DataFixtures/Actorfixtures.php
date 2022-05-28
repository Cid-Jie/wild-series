<?php

namespace App\DataFixtures;

use App\Entity\Actor;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class Actorfixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for($i = 1; $i <= 10; $i++) {
            for($l = 1; $l <= 10; $l++) {
                $actor = new Actor();
                $actor->setFirstname($faker->firstname());
                $actor->setLastname($faker->lastname());
                $actor->setBirthDate($faker->dateTime());
                $actor->addProgram($this->getReference(
                    'program_' . rand(1, 3)
                ));
                    
                $manager->persist($actor);
        }
    }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            ProgramFixtures::class,
        ];
    }
}
