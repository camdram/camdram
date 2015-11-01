<?php

namespace Acts\CamdramBundle\Tests\Service;

use Acts\CamdramBundle\Entity\Performance;
use Acts\CamdramBundle\Entity\Show;
use Acts\CamdramBundle\Entity\Venue;
use Acts\CamdramBundle\Service\DiaryHelper;

class DiaryHelperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $router;

    /**
     * @var \Acts\CamdramBundle\Service\DiaryHelper
     */
    private $diaryHelper;

    /**
     * @var \Acts\CamdramBundle\Entity\Performance
     */
    private $performance;

    public function setUp()
    {
        $this->router = $this->getMock('Symfony\\Component\\Routing\\RouterInterface');
        $this->diaryHelper = new DiaryHelper($this->router);
    }

    private function getPerformance()
    {
        $s = new Show();
        $s->setName('Test Show');
        $s->setSlug('test-show');

        $p = new Performance();
        $p->setShow($s);
        $p->setStartDate(new \DateTime('2013-02-10'));
        $p->setEndDate(new \DateTime('2013-02-15'));
        $p->setTime(new \DateTime('19:45'));

        return $p;
    }

    public function testCreateEventFromPerformance()
    {
        $performance = $this->getPerformance();
        $performance->setOtherVenue('Test Venue');

        $this->router->expects($this->once())
            ->method('generate')
            ->with('get_show', array('identifier' => 'test-show'))
            ->will($this->returnValue('/shows/test-show'));

        list($event) = $this->diaryHelper->createEventsFromPerformance($performance);

        $this->assertEquals('Test Show', $event->getName());
        $this->assertEquals(new \DateTime('2013-02-10'), $event->getStartDate());
        $this->assertEquals(new \DateTime('2013-02-15'), $event->getEndDate());
        $this->assertEquals(new \DateTime('19:45'), $event->getStartTime());
        $this->assertEquals('/shows/test-show', $event->getLink());
        $this->assertEquals('Test Venue', $event->getVenue());
        $this->assertEquals(null, $event->getVenueLink());
    }

    public function testCreateEventFromPerformance_VenueObject()
    {
        $performance = $this->getPerformance();
        $venue = new Venue();
        $venue->setName('Test Venue');
        $venue->setSlug('test-venue');
        $performance->setVenue($venue);

        $this->router->expects($this->exactly(2))
            ->method('generate')
            ->will($this->returnValueMap(array(
                    array('get_show', array('identifier' => 'test-show'), false, '/shows/test-show'),
                    array('get_venue', array('identifier' => 'test-venue'), false, '/venues/test-venue'),
                )));

        list($event) = $this->diaryHelper->createEventsFromPerformance($performance);
        $this->assertEquals('Test Show', $event->getName());
        $this->assertEquals(new \DateTime('2013-02-10'), $event->getStartDate());
        $this->assertEquals(new \DateTime('2013-02-15'), $event->getEndDate());
        $this->assertEquals(new \DateTime('19:45'), $event->getStartTime());
        $this->assertEquals('/shows/test-show', $event->getLink());
        $this->assertEquals('/venues/test-venue', $event->getVenueLink());
    }

    public function testCreateEventFromPerformances()
    {
        $s = new Show();
        $s->setName('Test Show');
        $s->setSlug('test-show');

        $p1 = new Performance();
        $p1->setStartDate(new \DateTime('2013-02-10'));
        $p1->setEndDate(new \DateTime('2013-02-15'));
        $p1->setTime(new \DateTime('19:45'));
        $p1->setShow($s);

        $p2 = new Performance();
        $p2->setStartDate(new \DateTime('2013-02-15'));
        $p2->setEndDate(new \DateTime('2013-02-15'));
        $p2->setTime(new \DateTime('14:30'));
        $p2->setShow($s);

        $events = $this->diaryHelper->createEventsFromPerformances(array($p1, $p2));
        $this->assertEquals(2, count($events));
        $this->assertEquals(new \DateTime('2013-02-10'), $events[0]->getStartDate());
        $this->assertEquals(new \DateTime('2013-02-15'), $events[1]->getStartDate());
    }

    public function testCreateEventFromShows()
    {
        $s1 = new Show();
        $s1->setName('Test Show 1');
        $s1->setSlug('test-show-1');

        $p1 = new Performance();
        $p1->setStartDate(new \DateTime('2013-04-01'));
        $p1->setEndDate(new \DateTime('2013-04-02'));
        $p1->setTime(new \DateTime('19:30'));
        $p1->setShow($s1);
        $s1->addPerformance($p1);

        $s2 = new Show();
        $s2->setName('Test Show 2');
        $s2->setSlug('test-show-2');

        $p2 = new Performance();
        $p2->setStartDate(new \DateTime('2013-02-10'));
        $p2->setEndDate(new \DateTime('2013-02-15'));
        $p2->setTime(new \DateTime('19:45'));
        $p2->setShow($s2);
        $s2->addPerformance($p2);

        $p3 = new Performance();
        $p3->setStartDate(new \DateTime('2013-02-15'));
        $p3->setEndDate(new \DateTime('2013-02-15'));
        $p3->setTime(new \DateTime('14:30'));
        $p3->setShow($s2);
        $s2->addPerformance($p3);

        $events = $this->diaryHelper->createEventsFromShows(array($s1, $s2));
        $this->assertEquals(3, count($events));
        $this->assertEquals(new \DateTime('2013-04-01'), $events[0]->getStartDate());
        $this->assertEquals(new \DateTime('2013-02-10'), $events[1]->getStartDate());
        $this->assertEquals(new \DateTime('2013-02-15'), $events[2]->getStartDate());
    }
}
