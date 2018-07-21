<?php

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use Acts\CamdramBundle\Entity\Show;
use Acts\CamdramBundle\Entity\Performance;
use Acts\CamdramBundle\Entity\Society;
use Acts\CamdramBundle\Entity\Venue;
use Acts\CamdramBundle\Entity\Person;
use Acts\CamdramBundle\Entity\Role;
use Acts\CamdramSecurityBundle\Entity\User;

/**
 * @group search
 */
class SearchTest extends WebTestCase
{
    /**
     * @var Symfony\Bundle\FrameworkBundle\Client
     */
    private $client;

    /**
     * @var \Doctrine\ORM\EntityManager;
     */
    private $entityManager;

    /**
     * @var User
     */
    private $user;

    public function setUp()
    {
        $this->client = self::createClient(array('environment' => 'test'));

        $container = $this->client->getKernel()->getContainer();
        if (!$container->getParameter('search_enable_listeners')) {
            $this->markTestSkipped('search_enable_listeners is disabled');
        }
        $this->entityManager = $container->get('doctrine.orm.entity_manager');
        $container->get('fos_elastica.resetter')->resetAllIndexes();

        $this->user = $this->createUser();
    }

    private function createUser()
    {
        $user = new User;
        $user->setName("Test User")
            ->setEmail("admin@camdram.net");
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    private function refreshIndex()
    {
        //Elasticsearch ordinarilytakes a few secs to update its index after a change.
        //This ensures it's up to date before making assertions
        $this->client->getKernel()->getContainer()->get('fos_elastica.index.autocomplete')->refresh();
    }

    private function createShow($name, $startDate, $flush = true)
    {
        $show = new Show;
        $show->setName($name)
            ->setAuthorisedBy($this->user)
            ->setCategory('drama');

        $performance = new Performance;
        $performance->setStartDate(new \DateTime($startDate));
        $performance->setEndDate(new \DateTime($startDate));
        $performance->setTime(new \DateTime("19:30"));
        $show->addPerformance($performance);

        $this->entityManager->persist($show);
        $this->entityManager->persist($performance);

        if ($flush) {
            $this->entityManager->flush();
        }
    }

    private function createPerson($name)
    {
        $person = new Person;
        $person->setName($name);
        
        //People must have >= 1 role to be indexed
        $show = new Show;
        $show->setName('Test Show')
            ->setAuthorisedBy($this->user)
            ->setCategory('drama');

        $role = new Role;
        $role->setShow($show)
            ->setPerson($person)
            ->setRole('Director')
            ->setType('prod')
            ->setOrder(0);
        $person->addRole($role);

        $this->entityManager->persist($person);
        $this->entityManager->persist($show);
        $this->entityManager->persist($role);
        $this->entityManager->flush();
    }

    private function createSociety($name, $shortName)
    {
        $society = new Society;
        $society->setName($name)
            ->setShortName($shortName);
        $this->entityManager->persist($society);
        $this->entityManager->flush();
    }

    private function createVenue($name)
    {
        $venue = new Venue;
        $venue->setName($name)
            ->setShortName($name);
        $this->entityManager->persist($venue);
        $this->entityManager->flush();
    }

    private function doJsonRequest($url, $params)
    {
        $this->client->request('GET', $url, $params);

        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('application/json', $response->headers->get('Content-Type'));

        return json_decode($response->getContent(), true);
    }

    private function doSearch($query)
    {
        return $this->doJsonRequest('/search.json', ['q' => $query]);
    }

    private function doPaginatedSearch($query, $limit, $page)
    {
        return $this->doJsonRequest('/search.json', ['q' => $query, 'limit' => $limit, 'page' => $page]);
    }

    public function testAutocomplete()
    {
        $this->createShow('Test Show', '2000-01-01');
        $this->refreshIndex();

        $results = $this->doSearch('tes');
        $this->assertEquals(1, count($results));
        $this->assertEquals('Test Show', $results[0]['name']);

        $results = $this->doSearch('test');
        $this->assertEquals('Test Show', $results[0]['name']);

        $results = $this->doSearch('test s');
        $this->assertEquals('Test Show', $results[0]['name']);

        $results = $this->doSearch('test show');
        $this->assertEquals('Test Show', $results[0]['name']);

        $results = $this->doSearch('TeST s');
        $this->assertEquals('Test Show', $results[0]['name']);

        $results = $this->doSearch('TEST SHO');
        $this->assertEquals('Test Show', $results[0]['name']);

        $results = $this->doSearch('sh');
        $this->assertEquals('Test Show', $results[0]['name']);

        $results = $this->doSearch('show');
        $this->assertEquals('Test Show', $results[0]['name']);

        $results = $this->doSearch('      test');
        $this->assertEquals('Test Show', $results[0]['name']);

        //Does not match "Test Show"
        $this->assertEquals([], $this->doSearch(''));
        $this->assertEquals([], $this->doSearch('t')); //Minimum 2 characters
        $this->assertEquals([], $this->doSearch('s'));
        $this->assertEquals([], $this->doSearch('es'));
        $this->assertEquals([], $this->doSearch('est s'));
        $this->assertEquals([], $this->doSearch('tesf'));
        $this->assertEquals([], $this->doSearch('test x'));
        $this->assertEquals([], $this->doSearch('tests'));

        //Test shows-only search
        $results = $this->doJsonRequest('/shows.json', ['q' => 'test']);
        $this->assertEquals('Test Show', $results[0]['name']);
    }

    public function testPunctuation()
    {
        $this->createShow("Journey's End", '2000-01-01');
        $this->createShow("Panto: Snow Queen", '2001-01-01');
        $this->refreshIndex();

        $results = $this->doSearch("journeys");
        $this->assertEquals("Journey's End", $results[0]['name']);

        $results = $this->doSearch("journey's");
        $this->assertEquals("Journey's End", $results[0]['name']);

        $results = $this->doSearch("journeys end");
        $this->assertEquals("Journey's End", $results[0]['name']);

        $results = $this->doSearch("panto s");
        $this->assertEquals("Panto: Snow Queen", $results[0]['name']);

        $results = $this->doSearch("panto: s");
        $this->assertEquals("Panto: Snow Queen", $results[0]['name']);

        $results = $this->doSearch("panto snow queen");
        $this->assertEquals("Panto: Snow Queen", $results[0]['name']);
    }

    public function testAccents()
    {
        $this->createShow('Les Misérables', '2000-01-01');
        $this->createPerson('Zoë');
        $this->refreshIndex();

        $results = $this->doSearch("les mise");
        $this->assertEquals("Les Misérables", $results[0]['name']);

        $results = $this->doSearch("misé");
        $this->assertEquals("Les Misérables", $results[0]['name']);

        $results = $this->doSearch("les miserables");
        $this->assertEquals("Les Misérables", $results[0]['name']);

        $results = $this->doSearch("les misérables");
        $this->assertEquals("Les Misérables", $results[0]['name']);

        $results = $this->doSearch("zoë");
        $this->assertEquals("Zoë", $results[0]['name']);

        $results = $this->doSearch("ZOË");
        $this->assertEquals("Zoë", $results[0]['name']);

        $results = $this->doSearch("zoe");
        $this->assertEquals("Zoë", $results[0]['name']);
    }

    public function testUnicode()
    {
        $showName = '窦娥冤'; // "The Midsummer Snow" by Guan Hanqing
        $this->createShow($showName, '2000-01-01');
        $this->refreshIndex();

        $results = $this->doSearch('窦娥');
        $this->assertEquals($showName, $results[0]['name']);

        $results = $this->doSearch('窦娥冤');
        $this->assertEquals($showName, $results[0]['name']);

        $results = $this->doSearch('   窦娥冤 ');
        $this->assertEquals($showName, $results[0]['name']);

        $this->assertEquals([], $this->doSearch('窦 娥冤'));
    }

    public function testShowOrdering()
    {
        $this->createShow('Test Show 2005', '2005-10-11');
        $this->createShow('Test Show 2001', '2001-05-17');
        $this->createShow('Test Show 2012', '2012-01-02');
        $this->createShow('Test Show 1995', '1995-06-22');
        $this->refreshIndex();

        $results = $this->doSearch('test');
        $this->assertEquals(4, count($results));
        $this->assertEquals('Test Show 2012', $results[0]['name']);
        $this->assertEquals('Test Show 2005', $results[1]['name']);
        $this->assertEquals('Test Show 2001', $results[2]['name']);
        $this->assertEquals('Test Show 1995', $results[3]['name']);
    }

    public function testPagination()
    {
        for ($i = 1; $i <= 100; $i++) {
            $name = 'Test Show '.$i;
            $startAt = (2000 + $i)."-01-01";
            $this->createShow($name, $startAt, false);
        }
        $this->entityManager->flush();
        $this->refreshIndex();

        $results = $this->doPaginatedSearch('test', 1, 1);
        $this->assertEquals(1, count($results));
        $this->assertEquals('Test Show 100', $results[0]['name']);

        $results = $this->doPaginatedSearch('test', 100, 1);
        $this->assertEquals(100, count($results));
        for ($i = 0; $i < 100; $i++) {
            $this->assertEquals('Test Show '.(100 - $i), $results[$i]['name']);
        }

        for ($page = 1; $page <= 10; $page++) {
            $results = $this->doPaginatedSearch('test', 10, $page);
            $this->assertEquals(10, count($results));
            for ($i = 0; $i < 10; $i++) {
               $this->assertEquals('Test Show '.(100 - (($page-1) * 10) - $i), $results[$i]['name']);
            }
        }

        $results = $this->doPaginatedSearch('test', 101, 1);
        $this->assertEquals(100, count($results));
        for ($i = 0; $i < 100; $i++) {
            $this->assertEquals('Test Show '.(100 - $i), $results[$i]['name']);
        }

        $this->assertEquals([], $this->doPaginatedSearch('test', 0, 1));
        $this->assertEquals([], $this->doPaginatedSearch('test', 10, 11));
        $this->assertEquals([], $this->doPaginatedSearch('test', 10, 99));
    }

    public function testPerson()
    {
        $this->createPerson('John Smith');
        $this->refreshIndex();

        $results = $this->doSearch('joh');
        $this->assertEquals('John Smith', $results[0]['name']);

        $results = $this->doSearch('john s');
        $this->assertEquals('John Smith', $results[0]['name']);

        $results = $this->doSearch('john smith');
        $this->assertEquals('John Smith', $results[0]['name']);

        $results = $this->doJsonRequest('/people.json', ['q' => 'joh']);
        $this->assertEquals('John Smith', $results[0]['name']);
    }

    public function testSociety()
    {
        $name = 'Cambridge University Amateur Dramatic Club';
        $this->createSociety($name, 'cuadc');
        $this->refreshIndex();

        $results = $this->doSearch('cam');
        $this->assertEquals($name, $results[0]['name']);

        $results = $this->doSearch('dramatic c');
        $this->assertEquals($name, $results[0]['name']);

        $results = $this->doSearch($name);
        $this->assertEquals($name, $results[0]['name']);

        //"Short name" matches too
        $results = $this->doSearch('cuadc');
        $this->assertEquals($name, $results[0]['name']);

        $results = $this->doSearch('cua');
        $this->assertEquals($name, $results[0]['name']);

        $results = $this->doJsonRequest('/societies.json', ['q' => 'cua']);
        $this->assertEquals($name, $results[0]['name']);
    }

    public function testVenue()
    {
        $this->createVenue('ADC Theatre');
        $this->refreshIndex();

        $results = $this->doSearch('ad');
        $this->assertEquals('ADC Theatre', $results[0]['name']);

        $results = $this->doSearch('adc t');
        $this->assertEquals('ADC Theatre', $results[0]['name']);

        $results = $this->doSearch('adc theatre');
        $this->assertEquals('ADC Theatre', $results[0]['name']);

        $results = $this->doSearch('theat');
        $this->assertEquals('ADC Theatre', $results[0]['name']);

        $results = $this->doJsonRequest('/venues.json', ['q' => 'adc']);
        $this->assertEquals('ADC Theatre', $results[0]['name']);
    }
}