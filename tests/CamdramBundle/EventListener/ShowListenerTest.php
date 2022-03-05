<?php
namespace Camdram\Tests\CamdramBundle\Controller;

use Camdram\Tests\RestTestCase;
use Acts\CamdramBundle\Entity\Performance;
use Acts\CamdramBundle\Entity\Show;
use Acts\CamdramBundle\Entity\ShowSlug;
use Acts\CamdramBundle\Entity\Society;
use Acts\CamdramBundle\Entity\Venue;

class ShowListenerTest extends RestTestCase
{
    /** @var Show */
    private $show;

    public function setUp(): void
    {
        parent::setUp();

        $this->show = new Show();
        $this->show->setName("Test Show")
            ->setCategory('drama')
            ->setAuthorised(true);
        $this->entityManager->persist($this->show);
        $this->entityManager->flush();
    }

    private function setShowName(Show &$show, string $name): void
    {
        $showId = $show->getId();
        $crawler = $this->client->request('GET', '/shows/'.$show->getSlug().'/edit');
        $form = $crawler->selectButton('Save')->form();
        $form['show[name]'] = $name;
        $crawler = $this->client->submit($form);

        $this->entityManager->clear();
        $show = $this->entityManager->find(Show::class, $showId);
    }

    public function testManageSlugChange(): void
    {
        $slugRepo = $this->entityManager->getRepository(ShowSlug::class);

        $user = $this->createUser();
        $this->aclProvider->grantAccess($this->show, $user);
        $this->login($user);

        $this->setShowName($this->show, 'Test show');
        $this->assertEquals('test-show', $this->show->getSlug());
        $this->assertCount(0, $slugRepo->findAll());

        $this->setShowName($this->show, 'New test show');
        $this->assertEquals('new-test-show', $this->show->getSlug());
        $slugs = $slugRepo->findBy([], ['slug' => 'ASC']);

        $this->assertEquals('new-test-show', $slugs[0]->getSlug());
        $this->assertEquals($this->show, $slugs[0]->getShow());

        $this->assertEquals('test-show', $slugs[1]->getSlug());
        $this->assertEquals($this->show, $slugs[1]->getShow());
    }
}
