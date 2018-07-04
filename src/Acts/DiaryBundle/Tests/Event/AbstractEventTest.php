<?php

namespace Acts\DiaryBundle\Tests\Event;

use PHPUnit\Framework\TestCase;

class AbstractEventTest extends TestCase
{
    public function testAbstractEvent()
    {
        $event = $this->getMockForAbstractClass('\\Acts\\DiaryBundle\\Event\\AbstractEvent');
        $event->setName('Test Event');
        $event->setVenue('Test Venue');
        $event->setStartTime(new \DateTime('14:00'));
        $event->setEndTime(new \DateTime('16:00'));
        $event->setLink('http://www.testevent.com/');
        $event->setVenueLink('http://www.testvenue.com/');

        $this->assertEquals('Test Event', $event->getName());
        $this->assertEquals('Test Venue', $event->getVenue());
        $this->assertEquals('14:00', $event->getStartTime()->format('H:i'));
        $this->assertEquals('16:00', $event->getEndTime()->format('H:i'));
        $this->assertEquals('http://www.testevent.com/', $event->getLink());
        $this->assertEquals('http://www.testvenue.com/', $event->getVenueLink());
    }
}
