<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Logement;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Create 10 users with different roles
        $roles = ['ROLE_TRAVELER', 'ROLE_ADMIN', 'ROLE_HOST'];
        $hosts = [];

        for ($i = 1; $i <= 10; $i++) {
            $user = new User();
            $user->setEmail("user$i@example.com");
            $user->setPassword($this->passwordHasher->hashPassword($user, 'password123'));
            $user->setRoles([$roles[$i % 3]]);

            $manager->persist($user);

             // Store users with ROLE_HOST to assign logements
            if ($roles[$i % 3] === 'ROLE_HOST') {
                $hosts[] = $user;
            }
        }

        // Create 10 logements and assign them to random hosts
        for ($i = 0; $i < 10; $i++) {
            $logement = new Logement();
            $logement->setTitre("Logement $i")
                ->setDescription("Description for Logement $i")
                ->setLocalisation("City $i")
                ->setPrix(mt_rand(50, 500))
                ->setImage("image$i.jpg")
                ->setOwner($hosts[array_rand($hosts)]);

            $manager->persist($logement);
        }

        $manager->flush();
    }
}
