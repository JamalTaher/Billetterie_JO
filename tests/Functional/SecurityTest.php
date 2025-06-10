<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\ORM\EntityManagerInterface;
use App\DataFixtures\OffreFixture; 
use App\Entity\Utilisateur; 
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface; 
use Doctrine\ORM\Tools\SchemaTool; 

class SecurityTest extends WebTestCase
{
    private $client;
    private $entityManager;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        
        $this->entityManager = self::getContainer()->get('doctrine')->getManager();

        
        $passwordHasher = self::getContainer()->get('security.password_hasher');

       
        $metadatas = $this->entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool = new SchemaTool($this->entityManager);

        
        $schemaTool->dropSchema($metadatas);
        
        $schemaTool->createSchema($metadatas);

        

        // utilisateur de test
        $user = new Utilisateur();
        $user->setEmail('test@example.com');
        $user->setRoles(['ROLE_USER']);
        $hashedPassword = $passwordHasher->hashPassword($user, 'password123');
        $user->setPassword($hashedPassword);
        $user->setNom('Test');
        $user->setPrenom('Utilisateur');
        $user->setCleUtilisateur(uniqid('user_')); 
        $this->entityManager->persist($user);

        // utilisateur administrateur
        $adminUser = new Utilisateur();
        $adminUser->setEmail('admin@example.com');
        $adminUser->setRoles(['ROLE_ADMIN']);
        $hashedAdminPassword = $passwordHasher->hashPassword($adminUser, 'adminpassword');
        $adminUser->setPassword($hashedAdminPassword);
        $adminUser->setNom('Admin');
        $adminUser->setPrenom('User');
        $adminUser->setCleUtilisateur(uniqid('admin_')); 
        $this->entityManager->persist($adminUser);
        
        
        $offreFixture = new OffreFixture();
        $offreFixture->load($this->entityManager);

        
        $this->entityManager->flush();
        
        
        $this->entityManager->beginTransaction();
        $this->entityManager->getConnection()->setAutoCommit(false);
    }

    protected function tearDown(): void
    {
       
        if ($this->entityManager->getConnection()->isTransactionActive()) {
            $this->entityManager->rollback();
        }
        
       
        $this->entityManager->close();
        $this->entityManager = null;
        $this->client = null; 
        
        
        parent::tearDown();
    }

   
    public function testLoginPageLoadsSuccessfully(): void
    {
        $this->client->request('GET', '/login'); 

        $this->assertResponseIsSuccessful();
        
        $this->assertSelectorTextContains('h1', 'Connexion'); 
        $this->assertSelectorExists('input#inputEmail'); 
        $this->assertSelectorExists('input#inputPassword'); 
        $this->assertSelectorExists('button[type="submit"]'); 
    }

    
    public function testLoginWithValidCredentials(): void
    {
        $crawler = $this->client->request('GET', '/login');
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Se connecter')->form([ 
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);
        $this->client->submit($form);

        $this->assertResponseRedirects('/'); 
        $this->client->followRedirect(); 

        
        $this->assertSelectorTextContains('nav', 'Déconnexion'); 
        
    }

    
    public function testLoginWithInvalidCredentials(): void
    {
        $crawler = $this->client->request('GET', '/login');
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Se connecter')->form([
            'email' => 'invalid@example.com', 
            'password' => 'wrongpassword',    
        ]);
        $this->client->submit($form);

        
        $this->assertResponseRedirects('/login'); 
        $this->client->followRedirect(); 

        
        $this->assertSelectorTextContains('.alert.alert-danger', 'Invalid credentials.'); 
    }

    
    public function testLogout(): void
    {
        
        $this->client->request('GET', '/login');
        $form = $this->client->getCrawler()->selectButton('Se connecter')->form([
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);
        $this->client->submit($form);
        $this->client->followRedirect(); 

        
        $this->client->request('GET', '/logout'); 

        
        $this->assertResponseRedirects('/'); 
        $this->client->followRedirect(); 

        
        $this->assertSelectorTextContains('nav', 'Connexion'); 
    }
}