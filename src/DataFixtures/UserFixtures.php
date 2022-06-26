<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker\Factory;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        // Création d’un utilisateur de type “contributeur” (= auteur)
        $contributor = new User();
        $contributor->setName($faker->name());
        $contributor->setEmail('contributor@monsite.com');
        $contributor->setRoles(['ROLE_CONTRIBUTOR']);
        $hashedPassword = $this->passwordHasher->hashPassword(
            $contributor,
            'contributorpassword'
        );
        $contributor->setPassword($hashedPassword);
        $manager->persist($contributor);
 
        // Création d’un utilisateur de type “administrateur”
        $admin = new User();
        $admin->setName($faker->name());
        $admin->setEmail('admin@monsite.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $hashedPassword = $this->passwordHasher->hashPassword(
            $admin,
            'adminpassword'
        );
        $admin->setPassword($hashedPassword);
        $manager->persist($admin);
        $this->addReference('owner_' . $admin->getName(), $admin);
        
        // Sauvegarde des 2 nouveaux utilisateurs :
        $manager->flush();
    }
}
