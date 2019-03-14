<?php
namespace Camdram\Tests\CamdramBundle\Controller;

use Camdram\Tests\RestTestCase;
use Acts\CamdramBundle\Entity\Show;
use Acts\CamdramBundle\Entity\Performance;
use Acts\CamdramBundle\Entity\Society;


class ShowControllerTest extends RestTestCase
{

    /**
     * @var Show
     */
    private $show;

    public function setUp()
    {
        parent::setUp();

        $this->show = new Show();
        $this->show->setName("Test Show")
            ->setCategory('drama')
            ->setAuthorised(true);
        $this->entityManager->persist($this->show);
        $this->entityManager->flush();
    }

    public function testViewLoggedOut()
    {
        $crawler = $this->client->request('GET', '/shows/test-show');
        $this->assertEquals($crawler->filter('#content:contains("Test Show")')->count(), 1);
        $this->assertEquals($crawler->filter('#content:contains("Show Administration")')->count(), 0);
    }

    public function testViewAsShowOwner()
    {
        $user = $this->createUser();
        $this->aclProvider->grantAccess($this->show, $user);
        $this->login($user);

        $crawler = $this->client->request('GET', '/shows/test-show');
        $this->assertEquals($crawler->filter('#content:contains("Test Show")')->count(), 1);
        $this->assertEquals($crawler->filter('#content:contains("Show Administration")')->count(), 1);
    }

    public function testViewAsAdmin()
    {
        $user = $this->createUser();
        $this->aclProvider->grantAdmin($user);
        $this->login($user);

        $crawler = $this->client->request('GET', '/shows/test-show');
        $this->assertEquals($crawler->filter('#content:contains("Test Show")')->count(), 1);
        $this->assertEquals($crawler->filter('#content:contains("Show Administration")')->count(), 1);
    }

    public function testCreateShow()
    {
        $user = $this->createUser();
        $this->login($user);

        $crawler = $this->client->request('GET', '/shows/new');
        $form = $crawler->selectButton('Create')->form();
        $form['show[name]'] = 'Test Show';
        $form['show[performances][0][start_at][date]'] = '2001-03-02';
        $form['show[performances][0][start_at][time]'] = '19:45';
        $form['show[performances][0][repeat_until]'] = '2001-03-05';
        $crawler = $this->client->submit($form);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals($crawler->filter('#content:contains("Test Show")')->count(), 1);
        $this->assertEquals($crawler->filter('#content .approve-panel')->count(), 1);
    }

    public function testEditShow()
    {
        $user = $this->createUser();
        $this->aclProvider->grantAccess($this->show, $user);
        $this->login($user);

        $crawler = $this->client->request('GET', '/shows/test-show/edit');
        $this->assertEquals($crawler->filter('#content:contains("Edit Show")')->count(), 1);

        $input = $crawler->filter('input[name="show[name]"]');
        $this->assertEquals("Test Show", $input->attr('value'));
    }

    public function testSimpleShow()
    {
        $performance = new Performance;
        $performance->setStartAt(new \DateTime("2000-01-01 19:30"));
        $performance->setRepeatUntil(new \DateTime("2000-01-07"));

        $show = new Show;
        $show->setName("Test Show")
            ->setCategory("comedy")
            ->setAuthorised(true)
            ->addPerformance($performance);
        $this->entityManager->persist($show);
        $this->entityManager->flush();

        $data = $this->doJsonRequest('/shows/2000-test-show.json');
        $this->assertEquals("Test Show", $data['name']);

        $data = $this->doXmlRequest('/shows/2000-test-show.xml');
        $this->assertEquals("Test Show", $data->name);

        $data = $this->doJsonRequest('/shows/by-id/' . $show->getId() . '.json');
        $this->assertEquals("Test Show", $data['name']);

        // Check that by-id can generate 404s
        $crawler = $this->client->request('GET', '/shows/by-id/28934');
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }

    public function testShowWithSociety()
    {
        $society = new Society;
        $society->setName("Test Society");
        $this->entityManager->persist($society);

        $performance = new Performance;
        $performance->setStartAt(new \DateTime("2000-01-01 19:30"));
        $performance->setRepeatUntil(new \DateTime("2000-01-07"));

        $show = new Show;
        $show->setName("Test Show")
            ->setCategory("comedy")
            ->setAuthorised(true)
            ->addPerformance($performance)
            ->getSocieties()->add($society);
        $this->entityManager->persist($show);
        $this->entityManager->flush();

        $data = $this->doJsonRequest('/shows/2000-test-show.json');
        $this->assertEquals("Test Show", $data['name']);
        $this->assertEquals("Test Society", $data['society']['name']);

        $data = $this->doXmlRequest('/shows/2000-test-show.xml');
        $this->assertEquals("Test Show", $data->name);
        $this->assertEquals("Test Society", $data->society->name);
    }

    /**
     * Due to limitations of the DQL-SQLite driver, this does not test the
     * venue clash check.
     */
    public function testShowValidator()
    {
        $user = $this->createUser();
        $this->aclProvider->grantAdmin($user);
        $this->login($user);

        $show = new Show();
        $show->setName("Validator Test")
            ->setCategory("comedy")
            ->setAuthorised(true);
        $this->entityManager->persist($show);
        $this->entityManager->flush();

        $crawler = $this->client->request('GET', '/shows/validator-test');
        $this->assertEquals($crawler->filter('.error_panel:contains("no performances")')->count(), 1);
    }
}
