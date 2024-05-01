<?php

namespace App\Test\Controller;

use App\Entity\Livraison;
use App\Repository\LivraisonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LivraisonControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private LivraisonRepository $repository;
    private string $path = '/livraison/';
    private EntityManagerInterface $manager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get('doctrine')->getRepository(Livraison::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Livraison index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $originalNumObjectsInRepository = count($this->repository->findAll());

        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'livraison[dateLivraison]' => 'Testing',
            'livraison[statut]' => 'Testing',
            'livraison[partenaire]' => 'Testing',
            'livraison[commande]' => 'Testing',
        ]);

        self::assertResponseRedirects('/livraison/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Livraison();
        $fixture->setDateLivraison('My Title');
        $fixture->setStatut('My Title');
        $fixture->setPartenaire('My Title');
        $fixture->setCommande('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Livraison');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Livraison();
        $fixture->setDateLivraison('My Title');
        $fixture->setStatut('My Title');
        $fixture->setPartenaire('My Title');
        $fixture->setCommande('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'livraison[dateLivraison]' => 'Something New',
            'livraison[statut]' => 'Something New',
            'livraison[partenaire]' => 'Something New',
            'livraison[commande]' => 'Something New',
        ]);

        self::assertResponseRedirects('/livraison/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getDateLivraison());
        self::assertSame('Something New', $fixture[0]->getStatut());
        self::assertSame('Something New', $fixture[0]->getPartenaire());
        self::assertSame('Something New', $fixture[0]->getCommande());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Livraison();
        $fixture->setDateLivraison('My Title');
        $fixture->setStatut('My Title');
        $fixture->setPartenaire('My Title');
        $fixture->setCommande('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/livraison/');
    }
}
