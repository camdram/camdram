<?php
namespace Camdram\Tests\CamdramBundle\Controller;

use Camdram\Tests\RestTestCase;
use Acts\CamdramBundle\Entity\Person;
use Acts\CamdramBundle\Entity\Role;

class PersonControllerTest extends RestTestCase
{
    private $show;

    public function setUp(): void
    {
        parent::setUp();
        $this->show = $this->createShow("Person Test");

        $this->person = new Person();
        $this->person->setName("John Smith");
        $this->entityManager->persist($this->person);
        $this->entityManager->flush();

        $this->mapped = new Person();
        $this->mapped->setName("John B. Smith");
        $this->mapped->setMappedTo($this->person);
        $this->entityManager->persist($this->mapped);

        $this->role = new Role();
        $this->role->setOrder(1);
        $this->role->setPerson($this->person);
        $this->role->setRole('Antigone');
        $this->role->setShow($this->show);
        $this->role->setType('cast');
        $this->entityManager->persist($this->role);
        $this->entityManager->flush();
    }

    public function testPersonAPI()
    {
        foreach (['john-smith', "by-id/{$this->person->getId()}", 'john-b-smith', "by-id/{$this->mapped->getId()}"] as $slug) {
            $data = $this->doJsonRequest("/people/{$slug}.json");
            $this->assertEquals("John Smith", $data['name']);

            $data = $this->doXmlRequest("/people/{$slug}.xml");
            $this->assertEquals("John Smith", $data->name);
        }
    }

    public function testViewPerson()
    {
        $crawler = $this->client->request('GET', '/people/john-smith');
        $this->assertEquals($crawler->filter('#content:contains("John Smith")')->count(), 1);
        $this->assertEquals($crawler->filter('#content:contains("Person Test")')->count(), 1);

        $crawler = $this->client->request('GET', '/people/by-id/999999');
        $this->assertHTTPStatus(404);

        $crawler = $this->client->request('GET', '/people/non-existant');
        $this->assertHTTPStatus(404);
    }

    public function testPersonRoles()
    {
        $dates = [new \DateTime('-1 week'), new \DateTime('-1 day'), new \DateTime('+1 week')];
        $paths = ['past-roles.json', 'current-roles.json', 'upcoming-roles.json'];
        $prose = ['has been involved', 'is currently involved', 'is preparing'];
        for ($i = 0; $i < 3; $i++) {
            $showId = $this->show->getId();
            $this->entityManager->clear();
            $this->show = $this->entityManager->find('Acts\CamdramBundle\Entity\Show', $showId);

            $performance = $this->show->getPerformances()->first();
            $performance->setStartAt($dates[$i]);
            $performance->setRepeatUntil((clone $dates[$i])->modify('+2 day'));
            $this->entityManager->persist($performance);
            $this->entityManager->flush();

            $crawler = $this->client->request('GET', '/people/john-smith');

            for ($j = 0; $j < 3; $j++) {
                $data = $this->doJsonRequest("/people/john-smith/{$paths[$j]}");
                $this->assertEquals('John Smith', $data['person']['name']);
                $this->assertEquals('john-smith', $data['person']['slug']);
                if ($i === $j) {
                    $this->assertEquals('Person Test', $data['shows'][0]['name']);
                } else {
                    $this->assertEmpty($data['shows']);
                }

                $this->assertEquals($i === $j, $crawler->filter('#content:contains("'.$prose[$j].'")')->count() === 1);
            }
        }

        $data = $this->doJsonRequest('/people/john-smith/roles.json');
        $this->assertCount(1, $data);
        $this->assertArraySubset([
            'person_name'   => 'John Smith',
            'person_slug'   => 'john-smith',
            'id'            => $this->role->getId(),
            'type'          => 'cast',
            'role'          => 'Antigone',
            'show'          => [
                'id'        => $this->show->getId(),
                'name'      => $this->show->getName(),
                'slug'      => $this->show->getSlug(),
                '_type'     => 'show',
            ], 'person'     => [
                'id'        => $this->person->getId(),
                'name'      => $this->person->getName(),
                'slug'      => $this->person->getSlug(),
                '_type'     => 'person',
            ], '_links'     => [
                'show'      => "/shows/{$this->show->getSlug()}",
                'person'    => "/people/{$this->person->getSlug()}",
            ], '_type'      => 'role'
        ], $data[0]);
    }
}