<?php

namespace App\Test\Controller;

use App\Entity\ServiceApresVente;
use App\Repository\ServiceApresVenteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ServiceApresVenteControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private ServiceApresVenteRepository $repository;
    private string $path = '/service/apres/vente/';
    private EntityManagerInterface $manager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get('doctrine')->getRepository(ServiceApresVente::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('ServiceApresVente index');

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
            'service_apres_vente[description]' => 'Testing',
            'service_apres_vente[type]' => 'Testing',
            'service_apres_vente[date]' => 'Testing',
            'service_apres_vente[status]' => 'Testing',
            'service_apres_vente[idAchats]' => 'Testing',
            'service_apres_vente[idPartenaire]' => 'Testing',
        ]);

        self::assertResponseRedirects('/service/apres/vente/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new ServiceApresVente();
        $fixture->setDescription('My Title');
        $fixture->setType('My Title');
        $fixture->setDate('My Title');
        $fixture->setStatus('My Title');
        $fixture->setIdAchats('My Title');
        $fixture->setIdPartenaire('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('ServiceApresVente');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new ServiceApresVente();
        $fixture->setDescription('My Title');
        $fixture->setType('My Title');
        $fixture->setDate('My Title');
        $fixture->setStatus('My Title');
        $fixture->setIdAchats('My Title');
        $fixture->setIdPartenaire('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'service_apres_vente[description]' => 'Something New',
            'service_apres_vente[type]' => 'Something New',
            'service_apres_vente[date]' => 'Something New',
            'service_apres_vente[status]' => 'Something New',
            'service_apres_vente[idAchats]' => 'Something New',
            'service_apres_vente[idPartenaire]' => 'Something New',
        ]);

        self::assertResponseRedirects('/service/apres/vente/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getDescription());
        self::assertSame('Something New', $fixture[0]->getType());
        self::assertSame('Something New', $fixture[0]->getDate());
        self::assertSame('Something New', $fixture[0]->getStatus());
        self::assertSame('Something New', $fixture[0]->getIdAchats());
        self::assertSame('Something New', $fixture[0]->getIdPartenaire());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new ServiceApresVente();
        $fixture->setDescription('My Title');
        $fixture->setType('My Title');
        $fixture->setDate('My Title');
        $fixture->setStatus('My Title');
        $fixture->setIdAchats('My Title');
        $fixture->setIdPartenaire('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/service/apres/vente/');
    }
}
