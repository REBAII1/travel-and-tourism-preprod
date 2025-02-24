<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Logement;
use App\Entity\Reservation;
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
        // Create Users
        $admin = new User();
        $admin->setEmail("admin@mail.com")
            ->setPassword($this->passwordHasher->hashPassword($admin, 'adminpass'))
            ->setRoles(["ROLE_ADMIN"]);
        $manager->persist($admin);

        $host = new User();
        $host->setEmail("alice.host@mail.com")
            ->setPassword($this->passwordHasher->hashPassword($host, 'hostpass'))
            ->setRoles(["ROLE_HOST"]);
        $manager->persist($host);

        $traveler = new User();
        $traveler->setEmail("john.traveler@mail.com")
            ->setPassword($this->passwordHasher->hashPassword($traveler, 'travelerpass'))
            ->setRoles(["ROLE_TRAVELER"]);
        $manager->persist($traveler);

        // Create Logements
        $logement1 = new Logement();
        $logement1->setTitre("Cozy Apartment")
            ->setDescription("A modern apartment near the beach.")
            ->setLocalisation("Nice, France")
            ->setPrix(120)
            ->setImage("apartment.jpg")
            ->setOwner($host);
        $manager->persist($logement1);

        $logement2 = new Logement();
        $logement2->setTitre("Mountain Cabin")
            ->setDescription("A quiet retreat in the Alps.")
            ->setLocalisation("Chamonix, France")
            ->setPrix(200)
            ->setImage("cabin.jpg")
            ->setOwner($host);
        $manager->persist($logement2);

        $logement3 = new Logement();
        $logement3->setTitre("City Loft")
            ->setDescription("A stylish loft in the heart of Paris.")
            ->setLocalisation("Paris, France")
            ->setPrix(180)
            ->setImage("loft.jpg")
            ->setOwner($host);
        $manager->persist($logement3);

        // Create Reservations
        $reservation1 = new Reservation();
        $reservation1->setStatus(true)
            ->setDateDebut(new \DateTime("2025-06-01"))
            ->setDateFin(new \DateTime("2025-06-07"))
            ->setLogement($logement1)
            ->setUser($traveler);
        $manager->persist($reservation1);

        $reservation2 = new Reservation();
        $reservation2->setStatus(false)
            ->setDateDebut(new \DateTime("2025-07-10"))
            ->setDateFin(new \DateTime("2025-07-15"))
            ->setLogement($logement2)
            ->setUser($traveler);
        $manager->persist($reservation2);

        $reservation3 = new Reservation();
        $reservation3->setStatus(true)
            ->setDateDebut(new \DateTime("2025-08-05"))
            ->setDateFin(new \DateTime("2025-08-12"))
            ->setLogement($logement3)
            ->setUser($traveler);
        $manager->persist($reservation3);

        $manager->flush();
    }
}
