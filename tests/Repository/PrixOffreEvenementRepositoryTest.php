<?php

namespace App\Tests\Repository;

use App\Entity\PrixOffreEvenement;
use App\Entity\Offre;     
use App\Entity\Evenement; 
use App\Repository\PrixOffreEvenementRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PrixOffreEvenementRepositoryTest extends KernelTestCase
{
    private PrixOffreEvenementRepository $prixOffreEvenementRepository;
    private $entityManager;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->entityManager = self::getContainer()->get('doctrine.orm.entity_manager');
        $this->prixOffreEvenementRepository = $this->entityManager->getRepository(PrixOffreEvenement::class);

        
        $this->entityManager->beginTransaction();
        $this->entityManager->getConnection()->setAutoCommit(false);
    }

    protected function tearDown(): void
    {
       
        $this->entityManager->rollback();
        $this->entityManager->getConnection()->setAutoCommit(true);

        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null;
    }

    public function testAddAndRemovePrixOffreEvenement(): void
    {
        
        $offre = new Offre();
        $offre->setNom('Offre Test Prix ' . uniqid());
        $offre->setDescription('Description pour test PrixOffreEvenement.');
        $offre->setPrixInitial(500.00);
        $this->entityManager->persist($offre);

        $evenement = new Evenement();
        $evenement->setNom('Evenement Test Prix ' . uniqid());
        $evenement->setCategorie('Sport');
        $evenement->setDate(new \DateTime('2026-02-01 10:00:00'));
        $evenement->setLieu('Lieu Test Prix');
        $evenement->setDescription('Description Evenement Test Prix.');
        $this->entityManager->persist($evenement);

        $this->entityManager->flush(); 

        
        $prixOffreEvenement = new PrixOffreEvenement();
        $prixOffreEvenement->setPrix(150.50);
        $prixOffreEvenement->setOffre($offre);
        $prixOffreEvenement->setEvenement($evenement);

       
        $this->entityManager->persist($prixOffreEvenement);
        $this->entityManager->flush();

        
        $foundPoe = $this->prixOffreEvenementRepository->findOneBy(['prix' => 150.50]);
        $this->assertNotNull($foundPoe, 'Le PrixOffreEvenement devrait avoir été ajouté à la base de données.');
        $this->assertEquals($offre->getNom(), $foundPoe->getOffre()->getNom());
        $this->assertEquals($evenement->getNom(), $foundPoe->getEvenement()->getNom());

        
        $this->entityManager->remove($foundPoe);
        $this->entityManager->flush();

        
        $deletedPoe = $this->prixOffreEvenementRepository->findOneBy(['prix' => 150.50]);
        $this->assertNull($deletedPoe, 'Le PrixOffreEvenement devrait avoir été supprimé de la base de données.');
    }

    
}