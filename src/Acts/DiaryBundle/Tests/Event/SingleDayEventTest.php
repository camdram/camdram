<?php
namespace Acts\DiaryBundle\Tests\Event;

use Acts\DiaryBundle\Event\SingleDayEvent;

class SingleDayEventTest extends \PHPUnit_Framework_TestCase
{

    public function testSingleDayEvent()
    {
        $event = new SingleDayEvent();
        $event->setDate(new \DateTime('2014-02-01'));
        $this->assertEquals('1 February 2014', $event->getDate()->format('j F Y'));
    }

}
