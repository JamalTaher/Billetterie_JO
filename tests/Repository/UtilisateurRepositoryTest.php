<?php

namespace App\Tests\Repository;

use App\Entity\Utilisateur;
use App\Repository\UtilisateurRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Security\Core\Exception\UserNotFoundException; 

class UtilisateurRepositoryTest extends KernelTestCase
{
    private UtilisateurRepository $utilisateurRepository;
    private $entityManager;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->entityManager = self::getContainer()->get('doctrine.orm.entity_manager');
        $this->utilisateurRepository = $this->entityManager->getRepository(Utilisateur::class);

        
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

    public function testAddAndRemoveUtilisateur(): void
    {
        $utilisateur = new Utilisateur();
        $utilisateur->setEmail('test_add_remove@example.com');
        $utilisateur->setPassword('hashedpassword'); 
        $utilisateur->setNom('TestNom');
        $utilisateur->setPrenom('TestPrenom');
        $utilisateur->setRoles(['ROLE_USER']);
        $utilisateur->setCleUtilisateur('unique_key_1'); 
        $utilisateur->setEmailVerificationCode('code123');
        $utilisateur->setEmailVerificationRequestedAt(new \DateTimeImmutable());
        $utilisateur->setIsVerified(false); 

        $this->utilisateurRepository->save($utilisateur, true);

        $foundUtilisateur = $this->utilisateurRepository->findOneBy(['email' => 'test_add_remove@example.com']);
        $this->assertNotNull($foundUtilisateur, 'L\'utilisateur devrait avoir été ajouté à la base de données.');
        $this->assertEquals('TestNom', $foundUtilisateur->getNom());

        $this->utilisateurRepository->remove($foundUtilisateur, true);

        $deletedUtilisateur = $this->utilisateurRepository->findOneBy(['email' => 'test_add_remove@example.com']);
        $this->assertNull($deletedUtilisateur, 'L\'utilisateur devrait avoir été supprimé de la base de données.');
    }

    public function testFindOneByEmail(): void
    {
        $email = 'test_find_one_by_email@example.com';
        $utilisateur = new Utilisateur();
        $utilisateur->setEmail($email);
        $utilisateur->setPassword('anotherhashedpassword'); 
        $utilisateur->setPrenom('FindPrenom');
        $utilisateur->setRoles(['ROLE_USER']);
        $utilisateur->setCleUtilisateur('unique_key_2');
        $utilisateur->setEmailVerificationCode('code456');
        $utilisateur->setEmailVerificationRequestedAt(new \DateTimeImmutable());
        $utilisateur->setIsVerified(true);

        $this->utilisateurRepository->save($utilisateur, true);

        $found = $this->utilisateurRepository->findOneByEmail($email);
        $this->assertNotNull($found, 'L\'utilisateur devrait être trouvé par son email.');
        $this->assertEquals($email, $found->getEmail());
    }

    public function testLoadUserByIdentifier(): void
    {
        $email = 'test_load_by_identifier@example.com';
        $utilisateur = new Utilisateur();
        $utilisateur->setEmail($email);
        $utilisateur->setPassword('identifierhashedpassword'); 
        $utilisateur->setNom('IdentifierNom');
        $utilisateur->setPrenom('IdentifierPrenom');
        $utilisateur->setRoles(['ROLE_USER']);
        $utilisateur->setCleUtilisateur('unique_key_3');
        $utilisateur->setEmailVerificationCode('code789');
        $utilisateur->setEmailVerificationRequestedAt(new \DateTimeImmutable());
        $utilisateur->setIsVerified(true);

        $this->utilisateurRepository->save($utilisateur, true);

        $found = $this->utilisateurRepository->loadUserByIdentifier($email);
        $this->assertNotNull($found, 'L\'utilisateur devrait être trouvé par loadUserByIdentifier.');
        $this->assertEquals($email, $found->getUserIdentifier());

        
        try {
            $this->utilisateurRepository->loadUserByIdentifier('nonexistent@example.com');
            $this->fail('UserNotFoundException devrait être lancée pour un email inexistant.');
        } catch (UserNotFoundException $e) {
            $this->assertEquals('Email non trouvé ou compte non vérifié.', $e->getMessage());
        }

        
        $unverifiedUser = new Utilisateur();
        $unverifiedUser->setEmail('unverified@example.com');
        $unverifiedUser->setPassword('unverifiedpassword'); 
        $unverifiedUser->setNom('Unverified');
        $unverifiedUser->setPrenom('User');
        $unverifiedUser->setRoles(['ROLE_USER']);
        $unverifiedUser->setCleUtilisateur('unique_key_4');
        $unverifiedUser->setEmailVerificationCode('unverifiedcode');
        $unverifiedUser->setEmailVerificationRequestedAt(new \DateTimeImmutable());
        $unverifiedUser->setIsVerified(false); 
        $this->utilisateurRepository->save($unverifiedUser, true);

        try {
            $this->utilisateurRepository->loadUserByIdentifier('unverified@example.com');
            $this->fail('UserNotFoundException devrait être lancée pour un email non vérifié.');
        } catch (UserNotFoundException $e) {
            $this->assertEquals('Email non trouvé ou compte non vérifié.', $e->getMessage());
        }
    }

    
    public function testUpgradePassword(): void
    {
        $utilisateur = new Utilisateur();
        $utilisateur->setEmail('test_upgrade_password@example.com');
        $utilisateur->setPassword('old_hashed_password'); 
        $utilisateur->setNom('UpgradeNom');
        $utilisateur->setPrenom('UpgradePrenom');
        $utilisateur->setRoles(['ROLE_USER']);
        $utilisateur->setCleUtilisateur('unique_key_5');
        $utilisateur->setEmailVerificationCode('upgrade_code');
        $utilisateur->setEmailVerificationRequestedAt(new \DateTimeImmutable());
        $utilisateur->setIsVerified(true);

        $this->utilisateurRepository->save($utilisateur, true); 

        $newHashedPassword = 'new_hashed_password';
        $this->utilisateurRepository->upgradePassword($utilisateur, $newHashedPassword);

        
        $reloadedUser = $this->utilisateurRepository->findOneBy(['email' => 'test_upgrade_password@example.com']);
        $this->assertEquals($newHashedPassword, $reloadedUser->getPassword(), 'Le mot de passe devrait avoir été mis à jour.');
    }
}