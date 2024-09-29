<?php

namespace App\DataFixtures;

use App\Entity\User; // Assurez-vous que cela correspond à votre classe User
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Création de plusieurs utilisateurs fictifs
        for ($i = 1; $i <= 10; $i++) {
            $user = new User();
            $user->setEmail("user$i@example.com"); // Remplacez par vos méthodes
            $user->setPassword(password_hash('password', PASSWORD_BCRYPT)); // Utilisez une méthode pour le hachage des mots de passe
            $user->setCreatedAt(new \DateTimeImmutable());

            $manager->persist($user);
        }

        // Flush pour enregistrer les données
        $manager->flush();
    }
}
