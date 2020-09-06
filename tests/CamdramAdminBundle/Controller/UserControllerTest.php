<?php
namespace Acts\CamdramAdminBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use Camdram\Tests\RestTestCase;
use Acts\CamdramBundle\Entity\Person;
use Acts\CamdramSecurityBundle\Entity\AccessControlEntry;
use Acts\CamdramSecurityBundle\Entity\User;


class UserControllerTest extends RestTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->admin = $this->createUser("Admin User", "admin@camdram.net", AccessControlEntry::LEVEL_FULL_ADMIN);
    }

    public function testNotAdmin()
    {
        $this->login($this->createUser("Normal User", "normal@example.com"));
        $this->client->request('GET', '/admin/users');
        $this->assertEquals($this->client->getResponse()->getStatusCode(), 403);
    }

    public function testGetUser()
    {
        $this->login($this->admin);
        $user = $this->createUser("John Smith", "john.smith@example.com");

        $crawler = $this->client->request('GET', '/admin/users');

        $crawler2 = $this->click('John Smith', $crawler);
        $this->assertEquals(1, $crawler2->filter('#content:contains("John Smith")')->count());
        $this->assertEquals(1, $crawler2->filter('#content:contains("john.smith@example.com")')->count());

        $crawler3 = $this->click('Edit this user', $crawler2);
        $form = $crawler3->selectButton('Save')->form();
        $form->setValues([
            'user[name]' => '', // Non-allowed value
            'user[email]' => 'john.b.smith@example.com',
        ]);

        $crawler3 = $this->client->submit($form);
        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
        $form = $crawler3->selectButton('Save')->form();
        $form->setValues([
            'user[name]' => 'John B. Smith',
            'user[email]' => 'john.b.smith@example.com',
        ]);

        $crawler4 = $this->client->submit($form);
        $this->assertEquals(1, $crawler4->filter('#content:contains("John B. Smith")')->count());
        $this->assertEquals(1, $crawler4->filter('#content:contains("john.b.smith@example.com")')->count());

        $crawler = $this->client->request('GET', '/admin/users?q=john');
        $this->assertEquals(1, $crawler->filter('#content:contains("John")')->count());
        $crawler = $this->client->request('GET', '/admin/users?q=jane');
        $this->assertEquals(0, $crawler->filter('#content:contains("John")')->count());
    }

    public function testMergeAndDeleteUsers()
    {
        $this->login($this->admin);

        $john = $this->createUser("John Smith", "john.smith@example.com");
        $jane = $this->createUser("Jane Smith", "jane.smith@example.com");
        $joan = $this->createUser("Joan Smith", "joan.smith@example.com");
        $show1 = $this->createShow("Show 1");
        $show2 = $this->createShow("Show 2");
        $show3 = $this->createShow("Show 3");
        $this->aclProvider->grantAccess($show1, $john, $this->admin);
        $this->aclProvider->grantAccess($show2, $jane, $this->admin);
        $this->aclProvider->grantAccess($show3, $joan, $this->admin);
        $userIds = [$john->getId(), $jane->getId(), $joan->getId()];

        $crawler = $this->client->request('GET', '/admin/users/'.$userIds[0]);
        $crawler = $this->click('Merge user', $crawler);
        $form = $crawler->selectButton('Merge')->form();
        $form->setValues([
            'form[email]' => 'jane.smith@example.com',
            'form[keep_user]' => 'this'
        ]);
        $crawler = $this->client->submit($form);
        $this->assertTrue($this->aclProvider->isOwner($john, $show1));
        $this->assertTrue($this->aclProvider->isOwner($john, $show2));
        $this->assertFalse($this->aclProvider->isOwner($john, $show3));

        $crawler = $this->client->request('GET', '/admin/users/'.$userIds[1]);
        $this->assertEquals($this->client->getResponse()->getStatusCode(), 404);

        $crawler = $this->client->request('GET', '/admin/users/'.$userIds[0]);
        $crawler = $this->click('Merge user', $crawler);
        $form = $crawler->selectButton('Merge')->form();
        $form->setValues([
            'form[email]' => 'joan.smith@example.com',
            'form[keep_user]' => 'other'
        ]);
        $crawler = $this->client->submit($form);
        $this->assertTrue($this->aclProvider->isOwner($joan, $show1));
        $this->assertTrue($this->aclProvider->isOwner($joan, $show2));
        $this->assertTrue($this->aclProvider->isOwner($joan, $show3));

        $crawler = $this->client->request('GET', '/admin/users/'.$userIds[0]);
        $this->assertEquals($this->client->getResponse()->getStatusCode(), 404);

        $crawler = $this->client->request('GET', '/admin/users/'.$userIds[2]);
        $form = $crawler->selectButton('Delete this user')->form();
        $crawler = $this->client->submit($form);

        $crawler = $this->client->request('GET', '/admin/users/'.$userIds[2]);
        $this->assertEquals($this->client->getResponse()->getStatusCode(), 404);
    }
}
