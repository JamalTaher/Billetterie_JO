<?php

namespace App\Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Entity\Commande;
use App\Entity\Utilisateur; 
use App\Entity\PrixOffreEvenement; 

class CommandeTest extends TestCase
{
    public function testGetId(): void
    {
        $commande = new Commande();
        $this->assertNull($commande->getId()); 
        $commande->getId();
    }

    public function testGetSetDateCommande(): void
    {
        $commande = new Commande();
        $date = new DateTimeImmutable('2025-07-26 10:00:00'); 

        $commande->setDateCommande($date);
        $this->assertEquals($date, $commande->getDateCommande());
    }

    public function testGetSetMontantTotal(): void
    {
        $commande = new Commande();
        $montant = 250.99; 

        $commande->setMontantTotal($montant);
        $this->assertEquals($montant, $commande->getMontantTotal());
    }

    public function testGetSetUtilisateur(): void
    {
        $commande = new Commande();
        $utilisateur = new Utilisateur(); 
        $commande->setUtilisateur($utilisateur);
        $this->assertEquals($utilisateur, $commande->getUtilisateur());
    }

   
}