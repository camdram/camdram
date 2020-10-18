<?php
namespace Camdram\Tests\CamdramBundle\Controller;

use Camdram\Tests\RestTestCase;
use Acts\CamdramBundle\Entity\Show;
use Acts\CamdramBundle\Entity\Performance;
use Acts\CamdramBundle\Entity\Society;
use Acts\CamdramBundle\Entity\Venue;

class ShowControllerTest extends RestTestCase
{

    /**
     * @var Show
     */
    private $show;

    public function setUp(): void
    {
        parent::setUp();

        $this->show = new Show();
        $this->show->setName("Test Show")
            ->setCategory('drama')
            ->setAuthorised(true);
        $this->entityManager->persist($this->show);
        $this->entityManager->flush();
    }

    public function testViewLoggedOut()
    {
        $crawler = $this->client->request('GET', '/shows/test-show');
        $this->assertEquals($crawler->filter('#content:contains("Test Show")')->count(), 1);
        $this->assertEquals($crawler->filter('#content:contains("Show Administration")')->count(), 0);
    }

    public function testViewAsShowOwner()
    {
        $user = $this->createUser();
        $this->aclProvider->grantAccess($this->show, $user);
        $this->login($user);

        $crawler = $this->client->request('GET', '/shows/test-show');
        $this->assertEquals($crawler->filter('#content:contains("Test Show")')->count(), 1);
        $this->assertEquals($crawler->filter('#content:contains("Show Administration")')->count(), 1);
    }

    public function testViewAsAdmin()
    {
        $user = $this->createUser();
        $this->aclProvider->grantAdmin($user);
        $this->login($user);

        $crawler = $this->client->request('GET', '/shows/test-show');
        $this->assertEquals($crawler->filter('#content:contains("Test Show")')->count(), 1);
        $this->assertEquals($crawler->filter('#content:contains("Show Administration")')->count(), 1);
    }

    private function doCreateShow(string $name, \DateTime $startDate, \DateTime $endDate, bool $shouldPass = true)
    {
        $user = $this->createUser();
        $this->login($user);

        $crawler = $this->client->request('GET', '/shows/new');
        $form = $crawler->selectButton('Create')->form();
        $form['show[name]'] = $name;
        $form['show[performances][0][start_at][date]'] = $startDate->format('Y-m-d');
        $form['show[performances][0][start_at][time]'] = '19:45';
        $form['show[performances][0][repeat_until]'] = $endDate->format('Y-m-d');
        $crawler = $this->client->submit($form);

        if ($shouldPass) {
            $this->assertHTTPStatus(200);
            $this->assertEquals($crawler->filter("#content:contains(\"$name\")")->count(), 1);
            $this->assertEquals($crawler->filter('#content:contains("This show is not yet visible to the public")')->count(), 1);
        } else {
            $this->assertHTTPStatus(400);
            $this->assertTrue($crawler->filter('small.error')->count() > 0);
        }
    }

    public function testCreateShowsAllowed()
    {
        // Create shows at different dates to test the validator.
        $this->doCreateShow("Historic show", new \DateTime("2001-05-06"), new \DateTime("2001-05-11"));
        $this->doCreateShow("Current show", new \DateTime(), new \DateTime("+3 days"));
        $this->doCreateShow("One-night stand", new \DateTime("+3 days"), new \DateTime("+3 days"));
    }

    public function testCreateShowsDisallowed()
    {
        // Policy at time of writing: shows may be no more than 18 months ahead
        $this->doCreateShow("Future show", new \DateTime("+19 months"), new \DateTime("+19 months"), false);
        $this->doCreateShow("Backwards show", new \DateTime("+4 days"), new \DateTime("+2 days"), false);
    }

    public function testAuthorizeShow()
    {
        $this->show->setAuthorised(false);
        $this->entityManager->flush();

        $user = $this->createUser();
        $this->aclProvider->grantAdmin($user);
        $this->login($user);

        $crawler = $this->client->request('GET', '/shows/test-show');
        $form = $crawler->selectButton('Approve this show')->form();
        $crawler = $this->client->submit($form);

        $this->assertHTTPStatus(200);
        $this->assertEquals($crawler->filter('#content:contains("This show is not yet visible to the public")')->count(), 0);

        $this->logout();
        $crawler = $this->client->request('GET', '/shows/test-show');
        $this->assertEquals($crawler->filter('#content:contains("Test Show")')->count(), 1);
        $this->assertEquals($crawler->filter('#content .admin-panel')->count(), 0);
    }

    public function testEditShow()
    {
        $user = $this->createUser();
        $this->aclProvider->grantAccess($this->show, $user);
        $this->login($user);

        $crawler = $this->client->request('GET', '/shows/test-show/edit');
        $this->assertEquals($crawler->filter('#content:contains("Edit Show")')->count(), 1);

        $input = $crawler->filter('input[name="show[name]"]');
        $this->assertEquals("Test Show", $input->attr('value'));
    }

    public function testSimpleShow()
    {
        $performance = new Performance;
        $performance->setStartAt(new \DateTime("2000-01-01 19:30"));
        $performance->setRepeatUntil(new \DateTime("2000-01-07"));

        $show = new Show;
        $show->setName("Test Show")
            ->setCategory("comedy")
            ->setAuthorised(true)
            ->addPerformance($performance);
        $this->entityManager->persist($show);
        $this->entityManager->flush();

        $data = $this->doJsonRequest('/shows/2000-test-show.json');
        $this->assertEquals("Test Show", $data['name']);

        $data = $this->doXmlRequest('/shows/2000-test-show.xml');
        $this->assertEquals("Test Show", $data->name);

        $data = $this->doJsonRequest('/shows/by-id/' . $show->getId() . '.json');
        $this->assertEquals("Test Show", $data['name']);

        // Check that by-id can generate 404s
        $crawler = $this->client->request('GET', '/shows/by-id/28934');
        $this->assertHTTPStatus(404);
    }

    public function testShowWithSociety()
    {
        $society = new Society;
        $society->setName("Test Society");
        $this->entityManager->persist($society);

        $performance = new Performance;
        $performance->setStartAt(new \DateTime("2000-01-01 19:30"));
        $performance->setRepeatUntil(new \DateTime("2000-01-07"));

        $show = new Show;
        $show->setName("Test Show")
            ->setCategory("comedy")
            ->setAuthorised(true)
            ->addPerformance($performance)
            ->getSocieties()->add($society);
        $this->entityManager->persist($show);
        $this->entityManager->flush();

        $data = $this->doJsonRequest('/shows/2000-test-show.json');
        $this->assertEquals("Test Show", $data['name']);
        $this->assertEquals("Test Society", $data['societies'][0]['name']);

        $data = $this->doXmlRequest('/shows/2000-test-show.xml');
        $this->assertEquals("Test Show", $data->name);
        $this->assertEquals("Test Society", $data->societies->entry->name);
    }

    /**
     * Due to limitations of the DQL-SQLite driver, this does not test the
     * venue clash check.
     */
    public function testShowValidator()
    {
        $user = $this->createUser();
        $this->aclProvider->grantAdmin($user);
        $this->login($user);

        $show = new Show();
        $show->setName("Validator Test")
            ->setCategory("comedy")
            ->setAuthorised(true);
        $this->entityManager->persist($show);
        $this->entityManager->flush();

        $crawler = $this->client->request('GET', '/shows/validator-test');
        $this->assertHTTPStatus(200);
        $this->assertEquals(1, $crawler->filter('.error_panel:contains("no performances")')->count());
    }

    /**
     * @group mysql
     */
    public function testShowValidatorMySQL()
    {
        // Run the SQLite compatible tests
        $this->testShowValidator();

        // Create entities
        $performance1 = new Performance();
        $performance1->setStartAt(new \DateTime("2000-01-01 19:30"));
        $performance1->setRepeatUntil(new \DateTime("2000-01-07"));
        $performance2 = new Performance();
        $performance2->setStartAt(new \DateTime("2000-01-05 19:30"));
        $performance2->setRepeatUntil(new \DateTime("2000-01-05"));

        $show1 = new Show();
        $show1->setName("Validator Test 1")
            ->setCategory("comedy")
            ->addPerformance($performance1)
            ->setAuthorised(true);
        $this->entityManager->persist($show1);
        $show2 = new Show();
        $show2->setName("Validator Test 2")
            ->setCategory("comedy")
            ->addPerformance($performance2)
            ->setAuthorised(true);
        $this->entityManager->persist($show2);
        $this->entityManager->flush();

        // No venue set => no clash
        $crawler = $this->client->request('GET', "/shows/{$show1->getSlug()}");
        $this->assertHTTPStatus(200);
        $this->assertEquals(0, $crawler->filter('.error_panel:contains("no performances")')->count());
        $this->assertEquals(0, $crawler->filter('.error_panel:contains("Validator Test 2")')->count());

        // Reset entity manager and add a venue to both shows => clash
        $this->entityManager->clear();

        $venue = new Venue();
        $venue->setName('ADC Theatre')->setShortName('ADC Theatre');
        $this->entityManager->persist($venue);

        $show1 = $this->entityManager->find(Show::class, $show1->getId());
        $show2 = $this->entityManager->find(Show::class, $show2->getId());
        $show1->getPerformances()->first()->setVenue($venue);
        $show2->getPerformances()->first()->setVenue($venue);
        $this->entityManager->persist($show1);
        $this->entityManager->persist($show2);
        $this->entityManager->flush();

        $crawler = $this->client->request('GET', "/shows/{$show1->getSlug()}");
        $this->assertHTTPStatus(200);
        $this->assertEquals(0, $crawler->filter('.error_panel:contains("no performances")')->count());
        $this->assertEquals(1, $crawler->filter('.error_panel:contains("Validator Test 2")')->count());

        // Reset entity manager and change one date => no clash
        $this->entityManager->clear();
        $performance1 = $this->entityManager->find(Performance::class, $performance1->getId());
        $performance1->setStartAt(new \DateTime("2000-02-01 19:30"));
        $performance1->setRepeatUntil(new \DateTime("2000-02-07"));
        $this->entityManager->persist($performance1);
        $this->entityManager->flush();

        $crawler = $this->client->request('GET', "/shows/{$show1->getSlug()}");
        $this->assertHTTPStatus(200);
        $this->assertEquals(0, $crawler->filter('.error_panel:contains("no performances")')->count());
        $this->assertEquals(0, $crawler->filter('.error_panel:contains("Validator Test 2")')->count());

        // Reset entity manager and change one time => no clash
        $this->entityManager->clear();
        $performance1 = $this->entityManager->find(Performance::class, $performance1->getId());
        $performance1->setStartAt(new \DateTime("2000-01-01 21:30"));
        $performance1->setRepeatUntil(new \DateTime("2000-01-07"));
        $this->entityManager->persist($performance1);
        $this->entityManager->flush();

        $crawler = $this->client->request('GET', "/shows/{$show1->getSlug()}");
        $this->assertHTTPStatus(200);
        $this->assertEquals(0, $crawler->filter('.error_panel:contains("no performances")')->count());
        $this->assertEquals(0, $crawler->filter('.error_panel:contains("Validator Test 2")')->count());

    }
}
