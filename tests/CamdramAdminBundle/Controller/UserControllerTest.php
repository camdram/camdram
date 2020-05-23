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
        $admin = $this->createUser("Admin User", "admin@camdram.net", AccessControlEntry::LEVEL_FULL_ADMIN);
        $this->login($admin);
    }

    public function testGetUser()
    {
        $user = new User();
        $user->setName("John Smith");
        $user->setEmail("john.smith@example.com");
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $crawler = $this->client->request('GET', '/admin/users');
        $link = $crawler->selectLink('John Smith')->link();

        $crawler2 = $this->client->request('GET', $link->getUri());
        $this->assertEquals(1, $crawler2->filter('#content:contains("John Smith")')->count());
        $this->assertEquals(1, $crawler2->filter('#content:contains("john.smith@example.com")')->count());
        $link = $crawler2->selectLink('Edit this user')->link();

        $crawler3 = $this->client->request('GET', $link->getUri());
        $form = $crawler3->selectButton('Save')->form();
        $form->setValues([
            'user[name]' => 'John B. Smith',
            'user[email]' => 'john.b.smith@example.com',
        ]);

        $crawler4 = $this->client->submit($form);
        $this->assertEquals(1, $crawler4->filter('#content:contains("John B. Smith")')->count());
        $this->assertEquals(1, $crawler4->filter('#content:contains("john.b.smith@example.com")')->count());
    }
}
