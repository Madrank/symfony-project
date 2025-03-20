<?php

namespace App\DataFixtures;

use App\Entity\Admin;
use App\Entity\Category;
use App\Entity\Responsible;
use App\Entity\Status;
use App\Entity\Ticket;
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
        // Création des catégories
        $categories = [];
        $categoryNames = ['Incident', 'Panne', 'Évolution', 'Anomalie', 'Information'];
        
        foreach ($categoryNames as $name) {
            $category = new Category();
            $category->setName($name);
            $categories[] = $category;
            $manager->persist($category);
        }

        // Création des statuts
        $statuses = [];
        $statusNames = ['Nouveau', 'Ouvert', 'Résolu', 'Fermé'];
        
        foreach ($statusNames as $name) {
            $status = new Status();
            $status->setName($name);
            $statuses[] = $status;
            $manager->persist($status);
        }

        // Création des responsables
        $responsibles = [];
        $responsibleData = [
            ['John Doe', 'john.doe@example.com'],
            ['Jane Smith', 'jane.smith@example.com'],
            ['Bob Wilson', 'bob.wilson@example.com']
        ];

        foreach ($responsibleData as [$name, $email]) {
            $responsible = new Responsible();
            $responsible->setName($name);
            $responsible->setEmail($email);
            $responsibles[] = $responsible;
            $manager->persist($responsible);
        }

        // Création d'un administrateur
        $admin = new Admin();
        $admin->setEmail('admin@example.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin123'));
        $admin->setFirstName('Admin');
        $admin->setLastName('User');
        $manager->persist($admin);

        // Création d'un utilisateur standard
        $user = new Admin();
        $user->setEmail('user@example.com');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword($this->passwordHasher->hashPassword($user, 'user123'));
        $user->setFirstName('Regular');
        $user->setLastName('User');
        $manager->persist($user);

        // Création de quelques tickets
        $ticketDescriptions = [
            'Problème de connexion à l\'application',
            'Besoin d\'une nouvelle fonctionnalité',
            'Bug dans le module de facturation',
            'Question sur l\'utilisation du système'
        ];

        foreach ($ticketDescriptions as $key => $description) {
            $ticket = new Ticket();
            $ticket->setAuthorEmail('client' . ($key + 1) . '@example.com');
            $ticket->setDescription($description);
            $ticket->setCategory($categories[array_rand($categories)]);
            $ticket->setStatus($statuses[0]); // Nouveau statut
            $ticket->setResponsible($responsibles[array_rand($responsibles)]);
            $manager->persist($ticket);
        }

        $manager->flush();
    }
}
