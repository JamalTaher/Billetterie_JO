<?php

namespace App\Tests\Unit;

use App\Entity\Offre;
use App\Entity\Commande;
use App\Entity\PrixOffreEvenement;
use PHPUnit\Framework\TestCase;

class OffreEntityTest extends TestCase
{
    public function testGetId(): void
    {
        $offre = new Offre();
        $this->assertNull($offre->getId());
    }

    public function testSetAndGetNom(): void
    {
        $offre = new Offre();
        $nom = 'Billet VIP';
        $offre->setNom($nom);
        $this->assertEquals($nom, $offre->getNom());
    }

    public function testSetAndGetDescription(): void
    {
        $offre = new Offre();
        $description = 'Accès privilégié avec salon privé.';
        $offre->setDescription($description);
        $this->assertEquals($description, $offre->getDescription());
        $offre->setDescription(null);
        $this->assertNull($offre->getDescription());
    }

    public function testSetAndGetCapacite(): void
    {
        $offre = new Offre();
        $capacite = 100;
        $offre->setCapacite($capacite);
        $this->assertEquals($capacite, $offre->getCapacite());
    }

    public function testAddRemovePrixOffreEvenement(): void
    {
        $offre = new Offre();
        $prixOffreEvenement = new PrixOffreEvenement();

        $this->assertCount(0, $offre->getPrixOffreEvenements());
        $offre->addPrixOffreEvenement($prixOffreEvenement);
        $this->assertCount(1, $offre->getPrixOffreEvenements());
        $this->assertTrue($offre->getPrixOffreEvenements()->contains($prixOffreEvenement));

        $offre->removePrixOffreEvenement($prixOffreEvenement);
        $this->assertCount(0, $offre->getPrixOffreEvenements());
        $this->assertFalse($offre->getPrixOffreEvenements()->contains($prixOffreEvenement));
    }

   
    public function testAddRemoveCommande(): void
    {
        $offre = new Offre();
        $commande = new Commande();

        $this->assertCount(0, $offre->getCommandes());
        $offre->addCommande($commande);
        $this->assertCount(1, $offre->getCommandes());
        $this->assertTrue($offre->getCommandes()->contains($commande));

        
        $offre->removeCommande($commande);
        $this->assertCount(0, $offre->getCommandes());
        $this->assertFalse($offre->getCommandes()->contains($commande));
    }
}