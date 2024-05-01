<?php

namespace App\Test\Controller;

use App\Entity\AvisLivraison;
use App\Repository\AvisLivraisonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AvisLivraisonControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private AvisLivraisonRepository $repository;
    private string $path = '/avis/livraison/';
    private EntityManagerInterface $manager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get('doctrine')->getRepository(AvisLivraison::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('AvisLivraison index');

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
            'avis_livraison[commentaire]' => 'Testing',
            'avis_livraison[livraison]' => 'Testing',
        ]);

        self::assertResponseRedirects('/avis/livraison/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new AvisLivraison();
        $fixture->setCommentaire('My Title');
        $fixture->setLivraison('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('AvisLivraison');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new AvisLivraison();
        $fixture->setCommentaire('My Title');
        $fixture->setLivraison('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'avis_livraison[commentaire]' => 'Something New',
            'avis_livraison[livraison]' => 'Something New',
        ]);

        self::assertResponseRedirects('/avis/livraison/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getCommentaire());
        self::assertSame('Something New', $fixture[0]->getLivraison());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new AvisLivraison();
        $fixture->setCommentaire('My Title');
        $fixture->setLivraison('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/avis/livraison/');
    }
}
