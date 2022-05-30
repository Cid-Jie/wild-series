<?php

namespace App\DataFixtures;

use App\Entity\Episode;
use App\Service\Slugify;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class EpisodeFixtures extends Fixture implements DependentFixtureInterface
{
    private Slugify $slug;

    public function __construct(Slugify $slug)
    {
        $this->slug = $slug;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for($i = 1; $i <= 10; $i++) {
            for($j = 1; $j <= 10; $j++) {
                for($k = 1; $k <= 10; $k++) {  
                    $episode = new Episode();
                    $episode->setTitle($faker->sentence(3));
                    $title = $this->slug->generate($faker->sentence(3));
                    $episode->setSlug($title);
                    $episode->setNumber($k);
                    $episode->setSynopsis($faker->paragraphs(3, true));

                    $episode->setSeason($this->getReference(
                        'program_' . $i . '_season_' . $j
                    ));
                    
                    $manager->persist($episode);
                }
            }
        }
        $manager->flush();

    }

    public function getDependencies()
    {
        // Tu retournes ici toutes les classes de fixtures dont SeasonFixtures dépend
        return [
            SeasonFixtures::class,
        ];
    }
}
