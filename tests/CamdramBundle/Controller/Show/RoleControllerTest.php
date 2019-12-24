<?php
namespace Camdram\Tests\CamdramBundle\Controller\Show;

use Camdram\Tests\RestTestCase;
use Acts\CamdramBundle\Entity\Show;

class RoleControllerTest extends RestTestCase
{

    public function testAddRemoveRole()
    {
        $show = new Show;
        $show->setName('Test Show')->setCategory('drama')->setAuthorised(true);
        $this->entityManager->persist($show);
        $this->entityManager->flush();

        $user = $this->createUser();
        $this->aclProvider->grantAccess($show, $user);
        $this->login($user);

        //Get CSRF token
        $crawler = $this->client->request('GET', '/shows/test-show/edit-roles');
        $delete_token = $crawler->filter('[data-csrf-delete]')->attr('data-csrf-delete');

        //Add role
        $crawler = $this->client->request('PATCH', '/shows/test-show/roles', [
            'id' => 'new', 'role' => 'Romeo', 'person' => 'Richard O\'Brien',
            'role_type' => 'cast'
        ]);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $response1 = json_decode($this->client->getResponse()->getContent());

        //Add another role for same person with curly apostrophe and extra whitespace
        $crawler = $this->client->request('PATCH', '/shows/test-show/roles', [
            'id' => 'new', 'role' => 'Mercutio', 'person' => ' Richard O’Brien ',
            'role_type' => 'cast'
        ]);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $response2 = json_decode($this->client->getResponse()->getContent());

        $crawler = $this->client->request('GET', '/shows/test-show');
        $this->assertEquals($crawler->filter('#sortable-cast div:contains("Romeo")')->count(), 1);
        $this->assertEquals($crawler->filter('#sortable-cast div:contains("Mercutio")')->count(), 1);
        $this->assertEquals($crawler->filter('#sortable-cast div:contains("Richard O\'Brien")')->count(), 2);
        $repo = $this->entityManager->getRepository("ActsCamdramBundle:Role");
        $role1 = $repo->findOneById($response1->id);
        $role2 = $repo->findOneById($response2->id);
        $this->assertEquals($role1->getRole(), "Romeo");
        $this->assertEquals($role2->getRole(), "Mercutio");
        $this->assertEquals($role1->getPerson()->getId(), $role2->getPerson()->getId());
        $this->assertEquals($role1->getPerson()->getName(), 'Richard O\'Brien');

        //Remove both roles
        $this->client->request('DELETE', "/delete-role",
            ['role' => $role1->getId(), '_token' => $delete_token]);
        $this->assertEquals(204, $this->client->getResponse()->getStatusCode());
        $this->client->request('DELETE', "/delete-role",
            ['role' => $role2->getId(), '_token' => $delete_token]);
        $this->assertEquals(204, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->request('GET', '/shows/test-show');
        $this->assertEquals($crawler->filter('#sortable-cast div:contains("Richard O\'Brien")')->count(), 0);
    }

}
?>
