<?php

namespace App\Tests\Unit;

use App\Entity\Utilisateur;

use PHPUnit\Framework\TestCase;

class UserTest extends TestCase 
{
   
   

    public function testSetAndGetEmail(): void
    {
        $utilisateur = new Utilisateur(); 
        $email = 'test@example.com';

        $utilisateur->setEmail($email);
        $this->assertEquals($email, $utilisateur->getEmail());
    }

    public function testSetAndGetPassword(): void
    {
        $utilisateur = new Utilisateur(); 
        $password = 'password123';

        $utilisateur->setPassword($password);
        $this->assertEquals($password, $utilisateur->getPassword());
    }


    

    public function testUserHasDefaultRole(): void
    {
        $utilisateur = new Utilisateur();
        
        $this->assertContains('ROLE_USER', $utilisateur->getRoles());
    }

    public function testAddAndGetRole(): void
    {
        $utilisateur = new Utilisateur();
        $roleToAdd = 'ROLE_ADMIN';

        
        $currentRoles = $utilisateur->getRoles();
        if (!in_array($roleToAdd, $currentRoles)) {
            $currentRoles[] = $roleToAdd;
        }
        $utilisateur->setRoles($currentRoles); 
        $this->assertContains($roleToAdd, $utilisateur->getRoles());
        $this->assertContains('ROLE_USER', $utilisateur->getRoles()); 
    }

    public function testRolesAreUnique(): void
    {
        $utilisateur = new Utilisateur();
        
        $utilisateur->setRoles(['ROLE_USER']); 

        $roles = $utilisateur->getRoles(); 
        
        $roles[] = 'ROLE_ADMIN';
        $roles[] = 'ROLE_USER'; 

        
        $uniqueRoles = array_values(array_unique($roles)); 

        $utilisateur->setRoles($uniqueRoles); 
        $this->assertCount(2, $utilisateur->getRoles()); 
        $this->assertContains('ROLE_USER', $utilisateur->getRoles());
        $this->assertContains('ROLE_ADMIN', $utilisateur->getRoles());
    }
}