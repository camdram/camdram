<?php
namespace Camdram\Tests\CamdramBundle\Controller;

use Camdram\Tests\RestTestCase;
use Acts\CamdramBundle\Entity\Person;
use Acts\CamdramBundle\Entity\Role;
use Acts\CamdramBundle\Service\Time;

class PersonControllerTest extends RestTestCase
{
    /** @var string */
    private static $old_dev_warning;
    private $show;

    public static function setUpBeforeClass(): void
    {
        self::$old_dev_warning = getenv("DEVELOPMENT_WARNING");
        putenv("DEVELOPMENT_WARNING=false");
    }

    public static function tearDownAfterClass(): void
    {
        if (self::$old_dev_warning === false) {
            putenv("DEVELOPMENT_WARNING");
        } else {
            putenv("DEVELOPMENT_WARNING=".self::$old_dev_warning);
        }
    }

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
        $this->assertEquals($crawler->filter('meta[name="robots"][content="noindex"]')->count(), 0);
        $this->assertEquals($crawler->filter('#content:contains("John Smith")')->count(), 1);
        $this->assertEquals($crawler->filter('#content:contains("Person Test")')->count(), 1);

        $crawler = $this->client->request('GET', '/people/by-id/999999');
        $this->assertHTTPStatus(404);

        $crawler = $this->client->request('GET', '/people/non-existant');
        $this->assertHTTPStatus(404);
    }

    public function personRolesData()
    {
        // Cover both GMT and BST.
        return [[new \DateTime()], [new \DateTime('+5 month')], [new \DateTime('+7 month')]];
    }

    /**
     * @dataProvider personRolesData
     */
    public function testPersonRoles(\DateTime $faketime)
    {
        Time::mockDateTime($faketime);
        $dates = [
            ['-7 day', '-5 day'], // Past
            ['-1 day', '-1 day'], // Past
            ['+0 day', '+0 day'], // Present
            ['-1 day', '+1 day'], // Present
            ['+1 day', '+1 day'], // Future
            ['+7 day', '+9 day'], // Future
        ];
        $paths = ['past-roles.json', 'current-roles.json', 'upcoming-roles.json'];
        $prose = ['has been involved', 'is currently involved', 'is preparing'];
        for ($i = 0; $i < 6; $i++) {
            $showId = $this->show->getId();
            $this->entityManager->clear();
            $this->show = $this->entityManager->find('Acts\CamdramBundle\Entity\Show', $showId);

            $performance = $this->show->getPerformances()->first();
            $performance->setStartAt((clone $faketime)->modify($dates[$i][0]));
            $performance->setRepeatUntil((clone $faketime)->modify($dates[$i][1]));
            $this->entityManager->persist($performance);
            $this->entityManager->flush();

            $crawler = $this->client->request('GET', '/people/john-smith');

            for ($j = 0; $j < 3; $j++) {
                $data = $this->doJsonRequest("/people/john-smith/{$paths[$j]}");
                $this->assertEquals('John Smith', $data['person']['name']);
                $this->assertEquals('john-smith', $data['person']['slug']);
                $expected = ($i >> 1) === $j ? 1 : 0;
                if ($expected) {
                    $this->assertEquals('Person Test', $data['shows'][0]['name']);
                } else {
                    $this->assertEmpty($data['shows']);
                }

                $this->assertCount($expected, $crawler->filter('#content:contains("'.$prose[$j].'")'));
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

	public function testNewPerson() {
        $crawler = $this->client->request('GET', '/people/new');
        $this->assertHTTPStatus(404);

        $crawler = $this->client->request('POST', '/people');
        $this->assertHTTPStatus(405);
	}

	public function testEditPerson() {
        $crawler = $this->client->request('GET', '/people/john-smith/edit');
        $this->assertEquals($crawler->filter('#content:contains("Log in to Camdram")')->count(), 1);

        $user = $this->createUser();
        $this->aclProvider->grantAdmin($user);
        $this->login($user);

        $crawler = $this->client->request('GET', '/people/john-smith');
		$crawler = $this->click("Edit profile", $crawler);
		$this->assertHTTPStatus(200);

        $form = $crawler->selectButton('Save')->form();
        $form['person[name]'] = 'Jane Doe';
        $form['person[description]'] = 'Text text text';
        $form['person[no_robots]']->tick();
        $crawler = $this->client->submit($form);

        $this->assertEquals($crawler->filter('meta[name="robots"][content="noindex"]')->count(), 1);
        $this->assertEquals($crawler->filter('#content h2:contains("Jane Doe")')->count(), 1);
        $this->assertEquals($crawler->filter('#content p:contains("Text text text")')->count(), 1);
	}
}
