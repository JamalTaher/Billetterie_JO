<?php

namespace App\Tests\Unit;

use App\Entity\Evenement;
use App\Entity\PrixOffreEvenement;
use PHPUnit\Framework\TestCase;

class EvenementTest extends TestCase
{
    public function testSetAndGetNom(): void
    {
        $evenement = new Evenement();
        $nom = 'Cérémonie d\'ouverture des JO';
        $evenement->setNom($nom);
        $this->assertEquals($nom, $evenement->getNom());
    }

    public function testSetAndGetDescription(): void
    {
        $evenement = new Evenement();
        $description = 'Description détaillée de la cérémonie.';
        $evenement->setDescription($description);
        $this->assertEquals($description, $evenement->getDescription());
       
        $evenement->setDescription(null);
        $this->assertNull($evenement->getDescription());
    }

    public function testSetAndGetDate(): void
    {
        $evenement = new Evenement();
        $date = new \DateTime('2024-07-26 19:00:00'); 
        $evenement->setDate($date);
        $this->assertEquals($date, $evenement->getDate());
        $evenement->setDate(null); 
        $this->assertNull($evenement->getDate());
    }

    public function testSetAndGetCategorie(): void
    {
        $evenement = new Evenement();
        $categorie = 'Cérémonie';
        $evenement->setCategorie($categorie);
        $this->assertEquals($categorie, $evenement->getCategorie());
    }

    public function testSetAndGetLieu(): void
    {
        $evenement = new Evenement();
        $lieu = 'Stade de France';
        $evenement->setLieu($lieu);
        $this->assertEquals($lieu, $evenement->getLieu());
       
        $evenement->setLieu(null);
        $this->assertNull($evenement->getLieu());
    }

    public function testAddRemovePrixOffreEvenement(): void
    {
        $evenement = new Evenement();
        $prixOffreEvenement = new PrixOffreEvenement();

        $this->assertCount(0, $evenement->getPrixOffreEvenements());

        $evenement->addPrixOffreEvenement($prixOffreEvenement);
        $this->assertCount(1, $evenement->getPrixOffreEvenements());
        $this->assertTrue($evenement->getPrixOffreEvenements()->contains($prixOffreEvenement));

        $evenement->removePrixOffreEvenement($prixOffreEvenement);
        $this->assertCount(0, $evenement->getPrixOffreEvenements());
        $this->assertFalse($evenement->getPrixOffreEvenements()->contains($prixOffreEvenement));
    }

    public function testCreatePrixOffreEvenement(): void
    {
        $evenement = new Evenement();
        $initialCount = $evenement->getPrixOffreEvenements()->count();

        $prixOffreEvenement = $evenement->createPrixOffreEvenement();

        $this->assertInstanceOf(PrixOffreEvenement::class, $prixOffreEvenement);
        $this->assertEquals($initialCount + 1, $evenement->getPrixOffreEvenements()->count());
        $this->assertTrue($evenement->getPrixOffreEvenements()->contains($prixOffreEvenement));
    }
}