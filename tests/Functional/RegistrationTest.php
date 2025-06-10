<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class RegistrationTest extends WebTestCase
{
    private ?EntityManagerInterface $entityManager = null;
    private ?KernelBrowser $client = null;

    protected function setUp(): void
    {
        
        parent::setUp();

        
        $this->client = static::createClient();

        
        $this->entityManager = $this->client->getContainer()->get('doctrine.orm.entity_manager');

        
        $this->entityManager->beginTransaction();
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

    public function testNewUserRegistration(): void
    {
        
        $crawler = $this->client->request('GET', '/register'); 

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Inscription'); 

        $buttonCrawlerNode = $crawler->selectButton('S\'inscrire'); 

        $form = $buttonCrawlerNode->form([
            'registration_form[nom]' => 'TestNom',
            'registration_form[prenom]' => 'TestPrenom',
            'registration_form[email]' => 'test_user_'.uniqid().'@example.com',
            'registration_form[plainPassword][first]' => 'Password123!',
            'registration_form[plainPassword][second]' => 'Password123!',
        ]);

        $this->client->submit($form);

        $this->assertResponseRedirects('/verify/email/page'); 

        $crawler = $this->client->followRedirect();

        $this->assertSelectorTextContains('h1', 'Vérifiez votre adresse e-mail'); 
    }

    public function testUserRegistrationWithExistingEmail(): void
    {
       
        $existingEmail = 'existing_user_'.uniqid().'@example.com';
        $password = 'Password123!';

        $crawler = $this->client->request('GET', '/register'); 
        $buttonCrawlerNode = $crawler->selectButton('S\'inscrire'); 
        $form = $buttonCrawlerNode->form([
            'registration_form[nom]' => 'ExistingNom',
            'registration_form[prenom]' => 'ExistingPrenom',
            'registration_form[email]' => $existingEmail,
            'registration_form[plainPassword][first]' => $password,
            'registration_form[plainPassword][second]' => $password,
        ]);
        $this->client->submit($form);

        $this->assertResponseRedirects('/verify/email/page'); 
        $this->client->followRedirect();

       
        $crawler = $this->client->request('GET', '/register'); 
        $buttonCrawlerNode = $crawler->selectButton('S\'inscrire'); 
        $form = $buttonCrawlerNode->form([
            'registration_form[nom]' => 'AnotherNom',
            'registration_form[prenom]' => 'AnotherPrenom',
            'registration_form[email]' => $existingEmail,
            'registration_form[plainPassword][first]' => 'AnotherPassword123!',
            'registration_form[plainPassword][second]' => 'AnotherPassword123!',
        ]);
        $this->client->submit($form);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('.form-error-message', 'Cette valeur est déjà utilisée.'); 
    }
}