<?php

namespace App\Tests\Repository;

use App\Entity\Evenement;
use App\Repository\EvenementRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class EvenementRepositoryTest extends KernelTestCase
{
    private EvenementRepository $evenementRepository;
    private $entityManager;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->entityManager = self::getContainer()->get('doctrine.orm.entity_manager');
        $this->evenementRepository = $this->entityManager->getRepository(Evenement::class);

     
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

    public function testAddAndRemoveEvenement(): void
    {
      
        $evenement = new Evenement();
        $evenement->setNom('Test Evenement Add');
        $evenement->setCategorie('Sport');
        $evenement->setDate(new \DateTime('2026-01-01 10:00:00')); 
        $evenement->setLieu('Lieu Test');
        $evenement->setDescription('Description Test');

        // Persiste l'événement
        $this->evenementRepository->save($evenement, true); 

        $foundEvenement = $this->evenementRepository->findOneBy(['nom' => 'Test Evenement Add']);
        $this->assertNotNull($foundEvenement, 'L\'événement devrait avoir été ajouté à la base de données.');
        $this->assertEquals('Sport', $foundEvenement->getCategorie());

       
        $this->evenementRepository->remove($foundEvenement, true); 

       
        $deletedEvenement = $this->evenementRepository->findOneBy(['nom' => 'Test Evenement Add']);
        $this->assertNull($deletedEvenement, 'L\'événement devrait avoir été supprimé de la base de données.');
    }

    
}