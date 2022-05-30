<?php

namespace App\DataFixtures;

use App\Entity\Program;
use App\Service\Slugify;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ProgramFixtures extends Fixture implements DependentFixtureInterface
{
    const PROGRAMS = [
        ['title' => 'Vikings',
        'synopsis' => "Les exploits d'un groupe de vikings de la fin du 8ème siècle jusqu'au milieu du 11ème, mené par Ragnar Lodbrok, l'un des plus populaires héros viking de tous les temps, qui a régné quelques temps sur le Danemark et la Suède.",
        'poster' => 'https://pictures.betaseries.com/fonds/poster/56222c69d5e94f21652fed6402f66d24.jpg',
        'category_id' => 'Action'
        ],
        ['title' => 'Squid Game',
        'synopsis' => "Tentés par un prix alléchant en cas de victoire, des centaines de joueurs désargentés acceptent de s'affronter lors de jeux pour enfants aux enjeux mortels.",
        'poster' => 'https://pictures.betaseries.com/fonds/poster/79bdd9ecbc3b0b0888c7d0fb1b20a9f9.jpg',
        'category_id' => 'Action'
        ],
        ['title' => 'The Walking dead',
        'synopsis' => "Le policier Rick Grimes se réveille à l'hôpital après un long coma. Il découvre avec effarement que le monde, ravagé par une épidémie, est envahi par les morts-vivants.",
        'poster' => 'https://pictures.betaseries.com/fonds/poster/d338e649f57a342598ec430862798ad3.jpg',
        'category_id' => 'Horreur'
        ],
        ['title' => 'American Horror Story',
        'synopsis' => "A chaque saison, son histoire. American Horror Story nous embarque dans des récits à la fois poignants et cauchemardesques, mêlant la peur, le gore et le politiquement correct. De quoi vous confronter à vos plus grandes frayeurs !",
        'poster' => 'https://pictures.betaseries.com/fonds/poster/a5d17a5df3af1289dcaf993a26326835.jpg',
        'category_id' => 'Horreur'
        ],
        ['title' => 'Malcolm',
        'synopsis' => "Petit génie malgré lui, Malcolm vit dans une famille hors du commun. Le jeune surdoué n'hésite pas à se servir de son intelligence pour faire les 400 coups avec ses frères : Francis, l'aîné, envoyé dans une école militaire après une bêtise de trop, Reese, une brute pas très maligne, et Dewey, le petit dernier, souffre douleur général.",
        'poster' => 'https://pictures.betaseries.com/fonds/poster/5c3547cbae868ca895e5c5ed17cad1ed.jpg',
        'category_id' => 'Comédie'
        ],
        ['title' => 'The Big Bang Theory',
        'synopsis' => "Que se passe-t-il quand les très intelligents colocataires Sheldon et Leonard rencontrent Penny, une beauté libre d'esprit qui emménage la porte d'à côté, et réalisent qu'ils ne connaissent presque rien de la vie hors de leur laboratoire. Leur bande d'amis est complétée par le mielleux Wolowitz, qui pense être aussi sexy que futé, et Koothrappali, qui est incapable de parler en présence de femmes.",
        'poster' => 'https://pictures.betaseries.com/fonds/poster/2ab01b51d3b7cc1ce4b50a1009f9a989.jpg',
        'category_id' => 'Comédie'
        ],
        ['title' => 'The South Park',
        'synopsis' => "La petite ville de South Park dans le Colorado est le théâtre des aventures de Cartman, Stan, Kyle et Kenny, quatre enfants qui ont un langage un peu... décalé",
        'poster' => 'https://pictures.betaseries.com/fonds/poster/de642048b11c0c32e20061322fc749a5.jpg',
        'category_id' => 'Animation'
        ],
        ['title' => 'The Simpsons',
        'synopsis' => "Située à Springfield, ville américaine moyenne, la série se concentre sur les singeries et les aventures quotidiennes de la famille Simpson : Homer, Marge, Bart, Lisa et Maggie, ainsi que des milliers d'autres personnages.",
        'poster' => 'https://pictures.betaseries.com/fonds/poster/f205cecd578b4d95b246f0b33ebbaa5c.jpg',
        'category_id' => 'Animation'
        ],
        ['title' => 'The handmaid\'s tale',
        'synopsis' => "Dans une société dystopique et totalitaire au très bas taux de natalité, les femmes sont divisées en trois catégories : les Epouses, qui dominent la maison, les Marthas, qui l'entretiennent, et les Servantes, dont le rôle est la reproduction.",
        'poster' => 'https://pictures.betaseries.com/fonds/poster/60165477869340a61467d8870d723e1c.jpg',
        'category_id' => 'Science-fiction'
        ],
        ['title' => 'Snowpiercer',
        'synopsis' => "Sept ans après que le monde est devenu une vaste étendue glacée, les survivants ont trouvé refuge dans un train en perpétuel mouvement. Composé de 1001 wagons, l'engin fait le tour du globe à toute vitesse. À bord, la guerre des classes, l’injustice sociale et la politique interne sèment le trouble.",
        'poster' => 'https://pictures.betaseries.com/fonds/poster/6b044928d54a9bc55af06d8776babe00.jpg',
        'category_id' => 'Science-fiction'
        ],
    ];

    private Slugify $slug;

    public function __construct(Slugify $slug) 
    {
        $this->slug = $slug;
    }

    public function load(ObjectManager $manager)
    {
        $programNumber = 1;
        foreach (self::PROGRAMS as $programLoading) {
            $program = new Program();
            $program->setTitle($programLoading['title']);
            $title = $this->slug->generate($programLoading['title']);
            $program->setSlug($title);
            $program->setSynopsis($programLoading['synopsis']);
            $program->setPoster($programLoading['poster']);
            $program->setCategory($this->getReference('category_' . $programLoading['category_id']));
            $this->addReference('program_' . $programNumber, $program);
            $programNumber++;
            $manager->persist($program);
        }  
        $manager->flush();      
    }

    public function getDependencies()
    {
        // Tu retournes ici toutes les classes de fixtures dont ProgramFixtures dépend
        return [
          CategoryFixtures::class,
        ];
    }
}
