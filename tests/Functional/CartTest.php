<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\ORM\EntityManagerInterface;
use App\DataFixtures\OffreFixture;
use App\Entity\Utilisateur;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\User\UserInterface; 
use Doctrine\ORM\Tools\SchemaTool;


class CartTest extends WebTestCase
{
    private $client;
    private $entityManager;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = static::getContainer()->get('doctrine')->getManager();

        $metadatas = $this->entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool = new SchemaTool($this->entityManager);
        $schemaTool->dropSchema($metadatas);
        $schemaTool->createSchema($metadatas);

       
        $passwordHasher = static::getContainer()->get('security.password_hasher');

        // utilisateurs
        $user = new Utilisateur();
        $user->setEmail('test@example.com');
        $user->setRoles(['ROLE_USER']);
        $hashedPassword = $passwordHasher->hashPassword($user, 'password123');
        $user->setPassword($hashedPassword);
        $user->setNom('Test');
        $user->setPrenom('Utilisateur');
        $user->setCleUtilisateur(uniqid('user_'));
        $this->entityManager->persist($user);

        $adminUser = new Utilisateur();
        $adminUser->setEmail('admin@example.com');
        $adminUser->setRoles(['ROLE_ADMIN']);
        $hashedAdminPassword = $passwordHasher->hashPassword($adminUser, 'adminpassword');
        $adminUser->setPassword($hashedAdminPassword);
        $adminUser->setNom('Admin');
        $adminUser->setPrenom('User');
        $adminUser->setCleUtilisateur(uniqid('admin_'));
        $this->entityManager->persist($adminUser);

        // Chargement les offres
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

  
    private function logIn(string $email): void
    {
        $user = $this->entityManager->getRepository(Utilisateur::class)->findOneByEmail($email);
        $this->assertNotNull($user, "L'utilisateur {$email} n'a pas été trouvé pour la connexion.");

    
        $this->client->request('GET', '/'); 
    
        $this->client->enableReboot(); 

        $session = $this->client->getContainer()->get('session');

        $token = new UsernamePasswordToken($user, 'main', $user->getRoles());
        $session->set('_security_main', serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);

      
        $tokenStorage = static::getContainer()->get('security.token_storage');
        $tokenStorage->setToken($token);
    }

    
    private function addOfferToCart(string $offreNom, string $evenementNom): array
    {
        $offre = $this->entityManager->getRepository(Offre::class)->findOneBy(['nom' => $offreNom]);
        $evenement = $this->entityManager->getRepository(Evenement::class)->findOneBy(['nom' => $evenementNom]);

        $this->assertNotNull($offre, "Offre '{$offreNom}' introuvable.");
        $this->assertNotNull($evenement, "Événement '{$evenementNom}' introuvable.");

        $this->client->request('GET', '/'); 

        $this->client->xmlHttpRequest(
            'POST',
            '/panier/ajouter/' . $offre->getId() . '/' . $evenement->getId()
        );
        $this->assertResponseIsSuccessful();
        return json_decode($this->client->getResponse()->getContent(), true);
    }

   
    public function testAddToCartViaAjax(): void
    {
        $responseContent = $this->addOfferToCart('Billet Catégorie A', 'Concert Rock Stade de France');
        $this->assertTrue($responseContent['success']);
        $this->assertEquals('Offre ajoutée au panier.', $responseContent['message']);
        $this->assertEquals(1, $responseContent['panierCount']);
    }

    public function testRemoveFromCartViaAjax(): void
    {
        $this->addOfferToCart('Billet Standard', 'Compétition Athlétisme JO');

        $offre = $this->entityManager->getRepository(Offre::class)->findOneBy(['nom' => 'Billet Standard']);
        $evenement = $this->entityManager->getRepository(Evenement::class)->findOneBy(['nom' => 'Compétition Athlétisme JO']);

        $this->assertNotNull($offre);
        $this->assertNotNull($evenement);

        $this->client->request('GET', '/'); 
        $this->client->xmlHttpRequest(
            'POST',
            '/panier/supprimer/' . $offre->getId() . '/' . $evenement->getId()
        );

        $this->assertResponseIsSuccessful();
        $responseContent = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertTrue($responseContent['success']);
        $this->assertEquals('Élément supprimé du panier.', $responseContent['message']);
        $this->assertEquals(0, $responseContent['panierCount']);

        $this->client->request('GET', '/panier');
        $this->assertResponseIsSuccessful();
     
        $this->assertSelectorTextContains('p.empty-cart-message', 'Votre panier est vide.'); 
    }

    public function testCartPageLoadsEmpty(): void
    {
        $this->client->request('GET', '/panier');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Votre Panier'); 
      
        $this->assertSelectorTextContains('p.empty-cart-message', 'Votre panier est vide.'); 
    }

    
    public function testAddedItemAppearsInCart(): void
    {
        $this->addOfferToCart('Pack Famille', 'Cérémonie de Clôture JO');

        $this->client->request('GET', '/panier');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Votre Panier');

        $this->assertSelectorTextContains('body', 'Pack Famille');
        $this->assertSelectorTextContains('body', 'Cérémonie de Clôture JO');

        $this->assertCount(1, $this->client->getCrawler()->filter('.panier-item')); 
    }


    public function testCheckoutRequiresLogin(): void
    {
        $this->client->request('GET', '/panier/checkout');

      
        $this->assertResponseIsSuccessful(); 
        $this->assertSelectorTextContains('h1', 'Récapitulatif de votre commande'); 

     
    }

   
    public function testCheckoutPageLoadsSuccessfullyAfterLogin(): void
    {
        $this->logIn('test@example.com');
        $this->addOfferToCart('Billet Catégorie A', 'Concert Rock Stade de France');

        $this->client->request('GET', '/panier/checkout');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Récapitulatif de votre commande'); 
        $this->assertSelectorExists('button[type="submit"]'); 

        $this->assertSelectorTextContains('body', 'Billet Catégorie A');
        $this->assertSelectorTextContains('body', 'Concert Rock Stade de France');
    }

    public function testProcessPayment(): void
    {
        $this->logIn('test@example.com');

        $this->addOfferToCart('Billet Standard', 'Compétition Athlétisme JO');
        $this->addOfferToCart('Pack Famille', 'Cérémonie de Clôture JO'); 

        $this->client->request('POST', '/panier/process_payment');

        $this->assertResponseIsSuccessful(); 

        $this->assertSelectorTextContains('body', 'Votre billet vous a été envoyé par e-mail.'); 
        $this->assertSelectorTextContains('h1', 'Paiement Réussi !'); 

        $commandes = $this->entityManager->getRepository(\App\Entity\Commande::class)->findAll();
        $this->assertCount(2, $commandes); 
    }
}