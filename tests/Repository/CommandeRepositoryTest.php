<?php

namespace App\Tests\Repository;

use App\Entity\Commande;
use App\Entity\Utilisateur; 
use App\Repository\CommandeRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CommandeRepositoryTest extends KernelTestCase
{
    private CommandeRepository $commandeRepository;
    private $entityManager;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->entityManager = self::getContainer()->get('doctrine.orm.entity_manager');
        $this->commandeRepository = $this->entityManager->getRepository(Commande::class);

       
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

    public function testAddAndRemoveCommande(): void
    {
        
        $utilisateur = new Utilisateur();
        $utilisateur->setEmail('user_for_commande_' . uniqid() . '@example.com');
        $utilisateur->setPassword('password123'); 
        $utilisateur->setNom('NomCommande');
        $utilisateur->setPrenom('PrenomCommande');
        $utilisateur->setRoles(['ROLE_USER']);
        $utilisateur->setCleUtilisateur(uniqid()); 
        $utilisateur->setEmailVerificationCode('code_commande');
        $utilisateur->setEmailVerificationRequestedAt(new \DateTimeImmutable());
        $utilisateur->setIsVerified(true); 

        
        $this->entityManager->persist($utilisateur);
        $this->entityManager->flush(); 

       
        $commande = new Commande();
        $commande->setDateCommande(new \DateTimeImmutable());
        $commande->setMontantTotal(99.99);
        $commande->setUtilisateur($utilisateur); 

        
        $this->entityManager->persist($commande); 
        $this->entityManager->flush();           

        
        $foundCommande = $this->commandeRepository->findOneBy(['montantTotal' => 99.99]);
        $this->assertNotNull($foundCommande, 'La commande devrait avoir été ajoutée.');
        $this->assertEquals($utilisateur->getEmail(), $foundCommande->getUtilisateur()->getEmail());

       
        $this->entityManager->remove($foundCommande); 
        $this->entityManager->flush();              

        
        $deletedCommande = $this->commandeRepository->findOneBy(['montantTotal' => 99.99]);
        $this->assertNull($deletedCommande, 'La commande devrait avoir été supprimée.');
    }

    
    public function testGetVentesParEvenementAvecTotal(): void
    {
        
        $utilisateur = new Utilisateur();
        $utilisateur->setEmail('user_for_report_' . uniqid() . '@example.com');
        $utilisateur->setPassword('passreport');
        $utilisateur->setNom('ReportNom');
        $utilisateur->setPrenom('ReportPrenom');
        $utilisateur->setRoles(['ROLE_USER']);
        $utilisateur->setCleUtilisateur(uniqid());
        $utilisateur->setEmailVerificationCode('code_report');
        $utilisateur->setEmailVerificationRequestedAt(new \DateTimeImmutable());
        $utilisateur->setIsVerified(true);
        $this->entityManager->persist($utilisateur);

        
        $evenement1 = new \App\Entity\Evenement(); 
        $evenement1->setNom('Concert Rock ' . uniqid());
        $evenement1->setCategorie('Musique');
        $evenement1->setDate(new \DateTime('2025-07-15'));
        $evenement1->setLieu('Salle Pleyel');
        $evenement1->setDescription('Un super concert.');
        $this->entityManager->persist($evenement1);

        $evenement2 = new \App\Entity\Evenement(); 
        $evenement2->setNom('Match Foot ' . uniqid());
        $evenement2->setCategorie('Sport');
        $evenement2->setDate(new \DateTime('2025-07-20'));
        $evenement2->setLieu('Stade');
        $evenement2->setDescription('Match amical.');
        $this->entityManager->persist($evenement2);

        
        $offre1 = new \App\Entity\Offre(); 
        $offre1->setNom('Offre VIP ' . uniqid());
        $offre1->setDescription('Description VIP');
        $offre1->setPrixInitial(200.00);
        $this->entityManager->persist($offre1);

        $offre2 = new \App\Entity\Offre(); 
        $offre2->setNom('Offre Standard ' . uniqid());
        $offre2->setDescription('Description Standard');
        $offre2->setPrixInitial(50.00);
        $this->entityManager->persist($offre2);

        $this->entityManager->flush(); 

        
        $poe1_ev1_off1 = new \App\Entity\PrixOffreEvenement(); 
        $poe1_ev1_off1->setEvenement($evenement1);
        $poe1_ev1_off1->setOffre($offre1);
        $poe1_ev1_off1->setPrix(180.00);
        $this->entityManager->persist($poe1_ev1_off1);

        $poe2_ev1_off2 = new \App\Entity\PrixOffreEvenement();
        $poe2_ev1_off2->setEvenement($evenement1);
        $poe2_ev1_off2->setOffre($offre2);
        $poe2_ev1_off2->setPrix(45.00);
        $this->entityManager->persist($poe2_ev1_off2);

        $poe3_ev2_off2 = new \App\Entity\PrixOffreEvenement();
        $poe3_ev2_off2->setEvenement($evenement2);
        $poe3_ev2_off2->setOffre($offre2);
        $poe3_ev2_off2->setPrix(40.00);
        $this->entityManager->persist($poe3_ev2_off2);

        $this->entityManager->flush(); 

        
        $commande1 = new Commande();
        $commande1->setDateCommande(new \DateTimeImmutable());
       
        $this->entityManager->persist($commande1);


        
        $commande2 = new Commande();
        $commande2->setDateCommande(new \DateTimeImmutable());
        $commande2->setMontantTotal(45.00); 
        $commande2->setUtilisateur($utilisateur);
        $this->entityManager->persist($commande2);

        
        $commande3 = new Commande();
        $commande3->setDateCommande(new \DateTimeImmutable());
        $commande3->setMontantTotal(40.00); 
        $commande3->setUtilisateur($utilisateur);
        $this->entityManager->persist($commande3);

        $this->entityManager->flush(); 

       
        $ventesParEvenement = $this->commandeRepository->getVentesParEvenementAvecTotal();

       
        $this->assertIsArray($ventesParEvenement);
        $this->assertCount(2, $ventesParEvenement, 'Il devrait y avoir 2 entrées de rapport pour les deux événements.');

       
        $reportEvenement1 = array_filter($ventesParEvenement, fn($r) => $r['evenement_nom'] === $evenement1->getNom());
        $reportEvenement1 = reset($reportEvenement1); 
        $this->assertNotNull($reportEvenement1, 'Le rapport pour l\'événement Concert Rock devrait exister.');
        $this->assertEquals(2, $reportEvenement1['nombre_ventes'], 'Le nombre de ventes pour Concert Rock ne correspond pas.');
       
        $reportEvenement2 = array_filter($ventesParEvenement, fn($r) => $r['evenement_nom'] === $evenement2->getNom());
        $reportEvenement2 = reset($reportEvenement2);

        $this->assertNotNull($reportEvenement2, 'Le rapport pour l\'événement Match Foot devrait exister.');
        $this->assertEquals(1, $reportEvenement2['nombre_ventes'], 'Le nombre de ventes pour Match Foot ne correspond pas.');
        
}