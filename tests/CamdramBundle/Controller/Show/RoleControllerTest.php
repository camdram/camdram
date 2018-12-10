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

        //Add role
        $crawler = $this->client->request('GET', '/shows/test-show');
        $form = $crawler->selectButton('role_send')->form();
        $form['role[role]'] = 'Romeo';
        $form['role[name]'] = 'John Smith';
        $crawler = $this->client->submit($form);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        //Add another role for same person
        $form = $crawler->selectButton('role_send')->form();
        $form['role[role]'] = 'Mercutio';
        $form['role[name]'] = 'John Smith';
        $crawler = $this->client->submit($form);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $this->assertEquals($crawler->filter('#sortable-cast div:contains("Romeo")')->count(), 1);
        $this->assertEquals($crawler->filter('#sortable-cast div:contains("Mercutio")')->count(), 1);
        $this->assertEquals($crawler->filter('#sortable-cast div:contains("John Smith")')->count(), 2);
        
        //Remove both roles
        $form = $crawler->filter('#sortable-cast .delete-form')->form();
        $crawler = $this->client->submit($form);
        $form = $crawler->filter('#sortable-cast .delete-form')->form();
        $crawler = $this->client->submit($form);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals($crawler->filter('#sortable-cast div:contains("John Smith")')->count(), 0);
    }

}
?>