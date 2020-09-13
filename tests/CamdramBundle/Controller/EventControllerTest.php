<?php
namespace Camdram\Tests\CamdramBundle\Controller;

use Camdram\Tests\RestTestCase;
use Acts\CamdramBundle\Entity\Event;
use Acts\CamdramBundle\Entity\Society;
use Acts\CamdramBundle\Service\Time;

class EventControllerTest extends RestTestCase
{
    /**
     * @var Event
     */
    private $event;

    public function setUp(): void
    {
        parent::setUp();

        $this->event = new Event();
        $this->event->setName("Test Event")
            ->setDescription("A test event")
            ->setStartAt(new \DateTime("+1 week"))
            ->setEndTime(new \DateTime("18:00"));
        $this->entityManager->persist($this->event);
        $this->entityManager->flush();
    }

    public function testEventList()
    {
        $crawler = $this->client->request('GET', '/events');
        $this->assertEquals($crawler->filter('#content:contains("Upcoming events")')->count(), 1);
        $this->assertEquals($crawler->filter('#content:contains("Test Event")')->count(), 1);
        $crawler = $this->client->request('GET', '/events/historic');
        $this->assertEquals($crawler->filter('#content:contains("Past events")')->count(), 1);
        $this->assertEquals($crawler->filter('#content:contains("Test Event")')->count(), 0);

        Time::mockDateTime(new \DateTime("+2 week"));

        $crawler = $this->client->request('GET', '/events');
        $this->assertEquals($crawler->filter('#content:contains("Upcoming events")')->count(), 1);
        $this->assertEquals($crawler->filter('#content:contains("Test Event")')->count(), 0);
        $crawler = $this->client->request('GET', '/events/historic');
        $this->assertEquals($crawler->filter('#content:contains("Past events")')->count(), 1);
        $this->assertEquals($crawler->filter('#content:contains("Test Event")')->count(), 1);
    }

    public function testViewLoggedOut()
    {
        $crawler = $this->client->request('GET', '/events/1');
        $this->assertEquals($crawler->filter('#content:contains("Test Event")')->count(), 1);
        $this->assertEquals($crawler->filter('#content:contains("Event Administration")')->count(), 0);
    }

    public function testViewAsEventOwner()
    {
        $user = $this->createUser();
        $this->aclProvider->grantAccess($this->event, $user);
        $this->login($user);

        $crawler = $this->client->request('GET', '/events/1');
        $this->assertEquals($crawler->filter('#content:contains("Test Event")')->count(), 1);
        $this->assertEquals($crawler->filter('#content:contains("Event Administration")')->count(), 1);
    }

    public function testViewAsAdmin()
    {
        $user = $this->createUser();
        $this->aclProvider->grantAdmin($user);
        $this->login($user);

        $crawler = $this->client->request('GET', '/events/1');
        $this->assertEquals($crawler->filter('#content:contains("Test Event")')->count(), 1);
        $this->assertEquals($crawler->filter('#content:contains("Event Administration")')->count(), 1);
    }

    private function doCreateEvent(string $name, \DateTime $startDate, bool $shouldPass = true)
    {
        $user = $this->createUser();
        $this->login($user);

        $crawler = $this->client->request('GET', '/events/new');
        $form = $crawler->selectButton('Create')->form();
        $form['event[name]'] = $name;
        $form['event[description]'] = "Event description";
        $form['event[start_at][date]'] = $startDate->format('Y-m-d');
        $form['event[start_at][time]'] = '12:45';
        $form['event[endtime]'] = '13:30';
        $crawler = $this->client->submit($form);

        if ($shouldPass) {
            $this->assertHTTPStatus(200);
            $this->assertEquals($crawler->filter("#content:contains(\"$name\")")->count(), 1);
            $this->assertEquals($crawler->filter('#content:contains("Event Administration")')->count(), 1);
        } else {
            $this->assertHTTPStatus(400);
            $this->assertTrue($crawler->filter('small.error')->count() > 0);
        }
    }

    public function testCreateEventsAllowed()
    {
        // Create shows at different dates to test the validator.
        $this->doCreateEvent("Historic event", new \DateTime("2001-05-06"));
        $this->doCreateEvent("Future event", new \DateTime("+3 days"));
    }

    public function testEditEvent()
    {
        $user = $this->createUser();
        $this->aclProvider->grantAccess($this->event, $user);
        $this->login($user);

        $crawler = $this->client->request('GET', '/events/1/edit');
        $this->assertEquals($crawler->filter('#content:contains("Edit Event")')->count(), 1);

        $input = $crawler->filter('input[name="event[name]"]');
        $this->assertEquals("Test Event", $input->attr('value'));
    }
}
