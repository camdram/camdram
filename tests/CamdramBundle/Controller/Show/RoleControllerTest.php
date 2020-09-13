<?php
namespace Camdram\Tests\CamdramBundle\Controller\Show;

use Camdram\Tests\RestTestCase;
use Acts\CamdramBundle\Entity\Show;

class RoleControllerTest extends RestTestCase
{
    /** @var Show */
    private $show;

    private $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->show = $this->createShow('Test Show');
        $this->show2 = $this->createShow('Test Show 2');
        $this->user = $this->createUser();
        $this->showUrl = "/shows/{$this->show->getSlug()}";
        $this->aclProvider->grantAccess($this->show, $this->user);
        $this->aclProvider->grantAccess($this->show2, $this->user);
    }

    public function testAddRemoveRole()
    {
        $this->login($this->user);

        //Get CSRF token
        $crawler = $this->client->request('GET', "{$this->showUrl}/edit-roles");
        $delete_token = $crawler->filter('[data-csrf-delete]')->attr('data-csrf-delete');
        $patch_token  = $crawler->filter('[data-csrf-patch]')->attr('data-csrf-patch');

        //Add role
        $crawler = $this->client->request('PATCH', "{$this->showUrl}/roles", [
            'id' => 'new', 'role' => 'Romeo', 'person' => 'Richard O\'Brien',
            'role_type' => 'cast', '_token' => $patch_token
        ]);
        $this->assertHTTPStatus(200);
        $response1 = json_decode($this->client->getResponse()->getContent());

        //Add another role for same person with curly apostrophe and extra whitespace
        $crawler = $this->client->request('PATCH', "{$this->showUrl}/roles", [
            'id' => 'new', 'role' => 'Mercutio', 'person' => ' Richard O’Brien ',
            'role_type' => 'cast', '_token' => $patch_token
        ]);
        $this->assertHTTPStatus(200);
        $response2 = json_decode($this->client->getResponse()->getContent());

        $crawler = $this->client->request('GET', $this->showUrl);
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
        $this->assertHTTPStatus(204);
        $this->client->request('DELETE', "/delete-role",
            ['role' => $role2->getId(), '_token' => $delete_token]);
        $this->assertHTTPStatus(204);

        $crawler = $this->client->request('GET', $this->showUrl);
        $this->assertEquals($crawler->filter('#sortable-cast div:contains("Richard O\'Brien")')->count(), 0);
    }

    public function testEditRole()
    {
        $repo = $this->entityManager->getRepository("ActsCamdramBundle:Role");
        $this->login($this->user);

        //Get CSRF token
        $crawler = $this->client->request('GET', "{$this->showUrl}/edit-roles");
        $delete_token = $crawler->filter('[data-csrf-delete]')->attr('data-csrf-delete');
        $patch_token  = $crawler->filter('[data-csrf-patch]')->attr('data-csrf-patch');

        //Add role
        $crawler = $this->client->request('PATCH', "{$this->showUrl}/roles", [
            'id' => 'new', 'role' => 'Romeo', 'person' => 'Richard O\'Brien',
            'role_type' => 'cast', '_token' => $patch_token
        ]);
        $this->assertHTTPStatus(200);
        $response1 = json_decode($this->client->getResponse()->getContent());
        $roleId = $response1->id;

        // Edit role
        $crawler = $this->client->request('PATCH', "{$this->showUrl}/roles", [
            'id' => $roleId, 'role' => 'Juliet', 'person' => 'Jane Smith',
            'role_type' => 'cast', '_token' => $patch_token
        ]);
        $this->assertHTTPStatus(200);

        $role = $repo->find($roleId);
        $this->assertEquals('Juliet', $role->getRole());
        $this->assertEquals('Jane Smith', $role->getPerson()->getName());
        $this->assertEquals('cast', $role->getType());
    }

    public function testBadRequests()
    {
        // Not logged in
        $crawler = $this->client->request('GET', "{$this->showUrl}/edit-roles");
        $this->assertEquals($crawler->filter('#content:contains("Show Administration")')->count(), 0);
        $this->assertEquals($crawler->filter('#content:contains("Log in to Camdram")')->count(), 1);

        // Wrong user
        $this->login($this->createUser());
        $crawler = $this->client->request('GET', "{$this->showUrl}/edit-roles");
        $this->assertHTTPStatus(403);
        $this->assertEquals($crawler->filter('#content:contains("Show Administration")')->count(), 0);

        // Right user
        $this->login($this->user);
        // Get CSRF token
        $crawler = $this->client->request('GET', "{$this->showUrl}/edit-roles");
        $delete_token = $crawler->filter('[data-csrf-delete]')->attr('data-csrf-delete');
        $patch_token  = $crawler->filter('[data-csrf-patch]')->attr('data-csrf-patch');

        // Add role
        $crawler = $this->client->request('PATCH', "{$this->showUrl}/roles", [
            'id' => 'new', 'role' => 'Romeo', 'person' => 'Richard O\'Brien',
            'role_type' => 'cast', '_token' => $patch_token
        ]);
        $this->assertHTTPStatus(200);
        $response1 = json_decode($this->client->getResponse()->getContent());
        $roleId = $response1->id;

        $crawler = $this->client->request('PATCH', "/shows/{$this->show2->getSlug()}/roles", [
            'id' => $roleId, 'role' => 'Romeo', 'person' => 'Richard O\'Brien',
            'role_type' => 'cast', '_token' => $patch_token
        ]);
        $this->assertHTTPStatus(400);
        $this->assertStringContainsString('That role is not part of that show', $crawler->html());

        // No tokens
        $crawler = $this->client->request('PATCH', "{$this->showUrl}/roles", [
            'id' => 'new', 'role' => 'Romeo', 'person' => 'Richard O\'Brien',
            'role_type' => 'cast', '_token' => 'wrong'
        ]);
        $this->assertHTTPStatus(400);
        $this->assertStringContainsString('Invalid CSRF token', $crawler->html());
        $this->client->request('DELETE', "/delete-role",
            ['role' => $roleId, '_token' => 'wrong']);
        $this->assertHTTPStatus(400);
        $this->assertStringContainsString('Invalid CSRF token', $crawler->html());
    }

    public function testMultipleRoles()
    {
        $repo = $this->entityManager->getRepository("ActsCamdramBundle:Role");
        $this->login($this->user);

        $crawler = $this->client->request('GET', "{$this->showUrl}/edit-roles");
        $form = $crawler->selectButton('Add roles')->form();
        $form['roles[ordering]'] = 'role_first';
        $form['roles[separator]'] = ':';
        $form['roles[list]'] = <<<'EOT'
Director: John Smith
  Technical Director : Jane Smith
Head of Props :John Doe
  Stage Manager:A.N. Example  
EOT;
        $form['roles[type]'] = 'prod';
        $crawler = $this->client->submit($form);

        $roles = $this->doJsonRequest("{$this->showUrl}/roles.json");
        $expectedRoles = [
            'Director' => 'John Smith',
            'Technical Director' =>  'Jane Smith',
            'Head of Props' => 'John Doe',
            'Stage Manager' => 'A.N. Example'
        ];
        foreach ($roles as $role) {
            $this->assertEquals($role['type'], 'prod');
            $this->assertArrayHasKey($role['role'], $expectedRoles);
            $this->assertEquals($expectedRoles[$role['role']], $role['person_name']);
            unset($expectedRoles[$role['role']]);
        }
        $this->assertEmpty($expectedRoles);
    }
}
