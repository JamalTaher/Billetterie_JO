<?php

namespace App\Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Entity\PrixOffreEvenement; 
class PrixOffreEvenementTest extends TestCase
{
    public function testGetSetPrix(): void
    {
        $prixOffreEvenement = new PrixOffreEvenement();
        $prix = 150.75; 

        
        $prixOffreEvenement->setPrix($prix);

        
        $this->assertEquals($prix, $prixOffreEvenement->getPrix());
    }
    public function testGetSetOffre(): void
    {
        $prixOffreEvenement = new PrixOffreEvenement();
        $offre = new Offre(); 

        
        $prixOffreEvenement->setOffre($offre);

        
        $this->assertEquals($offre, $prixOffreEvenement->getOffre());
    }

    public function testGetSetEvenement(): void
    {
        $prixOffreEvenement = new PrixOffreEvenement();
        $evenement = new Evenement(); 

        
        $prixOffreEvenement->setEvenement($evenement);

     
        $this->assertEquals($evenement, $prixOffreEvenement->getEvenement());
    }
}