<?php

namespace App\Tests\Repository;

use App\Entity\Offre;
use App\Repository\OffreRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class OffreRepositoryTest extends KernelTestCase
{
    private OffreRepository $offreRepository;
    private $entityManager;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->entityManager = self::getContainer()->get('doctrine.orm.entity_manager');
        $this->offreRepository = $this->entityManager->getRepository(Offre::class);

        
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

    public function testAddAndRemoveOffre(): void
    {
        
        $uniqueName = 'Test Offre Add ' . uniqid(); 

        
        $offre = new Offre();
        $offre->setNom($uniqueName); 
        $offre->setDescription('Description de l\'offre de test.');
        $offre->setPrixInitial(100.00);

       
        $this->offreRepository->save($offre, true);

       
        $foundOffre = $this->offreRepository->findOneBy(['nom' => $uniqueName]);
        $this->assertNotNull($foundOffre, 'L\'offre devrait avoir été ajoutée à la base de données.');
        $this->assertEquals($uniqueName, $foundOffre->getNom());

        
        $this->offreRepository->remove($foundOffre, true);

        
        $deletedOffre = $this->offreRepository->findOneBy(['nom' => $uniqueName]);
        $this->assertNull($deletedOffre, 'L\'offre devrait avoir été supprimée de la base de données.');
    }

    
}