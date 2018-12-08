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
        $form['show[performances][0][start_date]'] = '2001-03-02';
        $form['show[performances][0][end_date]'] = '2001-03-05';
        $form['show[performances][0][time]'] = '19:45';
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
        $performance->setStartDate(new \DateTime("2000-01-01"));
        $performance->setEndDate(new \DateTime("2000-01-07"));
        $performance->setTime(new \DateTime("19:30"));

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
    }

    public function testShowWithSociety()
    {
        $society = new Society;
        $society->setName("Test Society");
        $this->entityManager->persist($society);

        $performance = new Performance;
        $performance->setStartDate(new \DateTime("2000-01-01"));
        $performance->setEndDate(new \DateTime("2000-01-07"));
        $performance->setTime(new \DateTime("19:30"));

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

}
