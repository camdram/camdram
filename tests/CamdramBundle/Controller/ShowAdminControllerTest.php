<?php
namespace Camdram\Tests\CamdramBundle\Controller;

use Camdram\Tests\RestTestCase;
use Acts\CamdramBundle\Entity\Show;
use Acts\CamdramBundle\Entity\Society;


class ShowAdminControllerTest extends RestTestCase
{
    /** @var Show */
    private $show;

    public function setUp(): void
    {
        parent::setUp();
        $this->show = $this->createShow('Test Show');
    }

    public function testViewLoggedOut()
    {
        $crawler = $this->client->request('GET', '/show-admin');
        $this->assertEquals($crawler->filter('#content:contains("Log in to Camdram")')->count(), 1);
    }

    public function testViewAsShowOwner()
    {
        $user = $this->createUser();
        $this->login($user);

        $crawler = $this->client->request('GET', '/show-admin');
        $this->assertEquals($crawler->filter('#content:contains("Show Administration")')->count(), 1);
        $this->assertEquals($crawler->filter('#content:contains("You have not created any shows")')->count(), 1);
        $this->assertEquals($crawler->filter('#content:contains("Test Show")')->count(), 0);

        $this->aclProvider->grantAccess($this->show, $user);

        $crawler = $this->client->request('GET', '/show-admin');
        $this->assertEquals($crawler->filter('#content:contains("Show Administration")')->count(), 1);
        $this->assertEquals($crawler->filter('#content:contains("You have not created any shows")')->count(), 0);
        $this->assertEquals($crawler->filter('#content:contains("Test Show")')->count(), 1);
    }

    public function testViewAsAdmin()
    {
        $user = $this->createUser();
        $this->aclProvider->grantAdmin($user);
        $this->login($user);

        $crawler = $this->client->request('GET', '/show-admin');
        $this->assertEquals($crawler->filter('#content:contains("Show Administration")')->count(), 1);
        $this->assertEquals($crawler->filter('#content:contains("You have not created any shows")')->count(), 1);
        $this->assertEquals($crawler->filter('#content:contains("Unauthorised shows")')->count(), 0);
        $this->assertEquals($crawler->filter('#content:contains("Test Show")')->count(), 0);

        $this->show->setAuthorised(false);
        $this->entityManager->flush();

        $crawler = $this->client->request('GET', '/show-admin');
        $this->assertEquals($crawler->filter('#content:contains("Show Administration")')->count(), 1);
        $this->assertEquals($crawler->filter('#content:contains("You have not created any shows")')->count(), 1);
        $this->assertEquals($crawler->filter('#content:contains("Unauthorised shows")')->count(), 1);
        $this->assertEquals($crawler->filter('#content:contains("Test Show")')->count(), 1);
    }
}
