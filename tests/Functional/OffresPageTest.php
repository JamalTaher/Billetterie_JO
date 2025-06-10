<?php


namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use App\DataFixtures\OffreFixture;

class OffresPageTest extends WebTestCase
{
    private $client;
    private $entityManager;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $purger = new ORMPurger($this->entityManager);
        $executor = new ORMExecutor($this->entityManager, $purger);
        $executor->execute([new OffreFixture()], false);

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

    
    public function testOffresPageLoadsSuccessfully(): void
    {
        $this->client->request('GET', '/offres');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Toutes nos offres disponibles');
    }

   
    public function testOffresPageDisplaysAllOffers(): void
    {
        $this->client->request('GET', '/offres');
        $this->assertResponseIsSuccessful();
        $this->assertCount(3, $this->client->getCrawler()->filter('.card'));
        $this->assertSelectorExists('button.btn-success');
    }

   
    public function testOffresPageHasCategoryFilterForm(): void
    {
        $this->client->request('GET', '/offres');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('select#category_filter_type_categorie'); 
        $this->assertSelectorExists('button[type="submit"]'); 
    }
}