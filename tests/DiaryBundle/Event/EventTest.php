<?php

namespace Camdram\Tests\DiaryBundle\Event;

use PHPUnit\Framework\TestCase;

use Acts\DiaryBundle\Event\Event;

class EventTest extends TestCase
{
    public function testEvent()
    {
        $event = new Event();
        $event->setName('Test Event');
        $event->setVenue('Test Venue');
        $event->setStartDate(new \DateTime('2014-02-01'));
        $event->setEndDate(new \DateTime('2014-02-07'));
        $event->setStartTime(new \DateTime('14:00'));
        $event->setEndTime(new \DateTime('16:00'));
        $event->setLink('http://www.testevent.com/');
        $event->setVenueLink('http://www.testvenue.com/');

        $this->assertEquals('Test Event', $event->getName());
        $this->assertEquals('Test Venue', $event->getVenue());
        $this->assertEquals('1 February 2014', $event->getStartDate()->format('j F Y'));
        $this->assertEquals('7 February 2014', $event->getEndDate()->format('j F Y'));
        $this->assertEquals('14:00', $event->getStartTime()->format('H:i'));
        $this->assertEquals('16:00', $event->getEndTime()->format('H:i'));
        $this->assertEquals('http://www.testevent.com/', $event->getLink());
        $this->assertEquals('http://www.testvenue.com/', $event->getVenueLink());
    }

    public function testSingleDayEvent()
    {
        $event = new Event();
        $event->setDate(new \DateTime('2014-02-01'));
        $this->assertEquals('1 February 2014', $event->getStartDate()->format('j F Y'));
        $this->assertEquals('1 February 2014', $event->getEndDate()->format('j F Y'));
    }

}
