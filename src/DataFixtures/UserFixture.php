<?php

namespace App\DataFixtures;

use App\Entity\Utilisateur; 
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixture extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        
        $user = new Utilisateur(); 
        $user->setEmail('test@example.com');
        $user->setRoles(['ROLE_USER']);
        // Générer le mot de passe haché
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            'password123' 
        );
        $user->setPassword($hashedPassword);
        
        $user->setNom('Test');
        $user->setPrenom('Utilisateur');
        $user->setCleUtilisateur(uniqid()); 

        $manager->persist($user);

        
        $adminUser = new Utilisateur();
        $adminUser->setEmail('admin@example.com');
        $adminUser->setRoles(['ROLE_ADMIN']);
        $hashedAdminPassword = $this->passwordHasher->hashPassword(
            $adminUser,
            'adminpassword'
        );
        $adminUser->setPassword($hashedAdminPassword);
        $adminUser->setNom('Admin');
        $adminUser->setPrenom('User');
        $adminUser->setCleUtilisateur(uniqid());

        $manager->persist($adminUser);

        $manager->flush();
    }
}
