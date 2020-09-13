<?php
namespace Camdram\Tests\CamdramBundle\Controller\Show;

use Camdram\Tests\RestTestCase;
use Acts\CamdramBundle\Entity\Show;

class AdminControllerTest extends RestTestCase
{
    public function testAddRemoveAdmin()
    {
        $show = new Show;
        $show->setName('Test Show')->setCategory('drama')->setAuthorised(true);
        $this->entityManager->persist($show);
        $this->entityManager->flush();

        $user = $this->createUser();
        $this->login($user);
        // Test access denied
        $crawler = $this->client->request('GET', '/shows/test-show/admin/edit');
        $this->assertHTTPStatus(403);

        // Add role for existing user
        $this->aclProvider->grantAccess($show, $user);
        $user2 = $this->createUser('User Two', 'user2@camdram.net');
        $crawler = $this->client->request('GET', '/shows/test-show/admin/edit');
        $form = $crawler->selectButton('Send')->form();
        $form->setValues(['pending_access[email]' => 'user2@camdram.net']);
        $crawler = $this->client->submit($form);

        // Verify admin role was created and then delete it
        $this->assertEquals(1, $crawler->filter("#admin_{$user2->getId()}:contains(\"User Two\")")->count());
        $form = $crawler->filter("#revoke_admin_form_{$user2->getId()}")->form();
        $crawler = $this->client->submit($form);

        // Verify admin role was deleted then create pending admin
        $this->assertEquals(0, $crawler->filter("#admin_{$user2->getId()}:contains(\"User Two\")")->count());
        $form = $crawler->selectButton('Send')->form();
        $form->setValues(['pending_access[email]' => 'user3@camdram.net']);
        $crawler = $this->client->submit($form);

        // Verify pending admin role created and delete
        $this->assertEquals(1, $crawler->filter('[id^="pending_"]:contains("user3@camdram.net")')->count());
        $form = $crawler->filter('[id^="revoke_pending_admin_"]')->form();
        $crawler = $this->client->submit($form);

        // Verify pending admin role was deleted then create pending admin
        $this->assertEquals(0, $crawler->filter('[id^="pending_"]:contains("user3@camdram.net")')->count());
        $form = $crawler->selectButton('Send')->form();
        $form->setValues(['pending_access[email]' => 'user3@camdram.net']);
        $crawler = $this->client->submit($form);

        // Create user to receive pending adminship
        $user3 = $this->createUser('User Three', 'user3@camdram.net');

        // Verify adminship received by $user3.
        $crawler = $this->client->request('GET', '/shows/test-show/admin/edit');
        $this->assertEquals(1, $crawler->filter("#admin_{$user3->getId()}:contains(\"User Three\")")->count());
    }

    public function testAdminRequests() {
        $show = new Show;
        $show->setName('Test Show')->setCategory('drama')->setAuthorised(true);
        $this->entityManager->persist($show);
        $this->entityManager->flush();

        $user1 = $this->createUser('Test User 1', 'user1@camdram.net');
        $user2 = $this->createUser('Test User 2', 'user2@camdram.net');
        $this->aclProvider->grantAccess($show, $user2);

        // Make admin request
        $this->login($user1);
        $crawler = $this->client->request('GET', "/shows/{$show->getSlug()}");
        $form = $crawler->selectButton("Request to be an admin")->form();
        $this->client->submit($form);

        // Deny request
        $this->login($user2);
        $crawler = $this->client->request('GET', "/shows/{$show->getSlug()}/admin/edit");
        $this->assertEquals(1, $crawler->filter("#admin_{$user1->getId()}:contains(\"Test User 1\")")->count());
        $form = $crawler->filter('button[title*="Deny request"]')->form();
        $crawler = $this->client->submit($form);
        $this->assertEquals(0, $crawler->filter("#admin_{$user1->getId()}:contains(\"Test User 1\")")->count());

        // Make another request
        $this->login($user1);
        $crawler = $this->client->request('GET', "/shows/{$show->getSlug()}");
        $form = $crawler->selectButton("Request to be an admin")->form();
        $this->client->submit($form);

        // Accept request
        $this->login($user2);
        $crawler = $this->client->request('GET', "/shows/{$show->getSlug()}/admin/edit");
        $this->assertEquals(1, $crawler->filter("#admin_{$user1->getId()}:contains(\"Test User 1\")")->count());
        $form = $crawler->filter('button[title*="Approve request"]')->form();
        $crawler = $this->client->submit($form);
        $this->assertEquals(1, $crawler->filter("#revoke_admin_form_{$user1->getId()}")->count());
        $this->assertEquals(0, $crawler->filter('button[title*="Deny request"]')->count());
    }
}
