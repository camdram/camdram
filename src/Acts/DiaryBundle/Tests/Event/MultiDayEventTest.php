<?php
namespace Acts\DiaryBundle\Tests\Event;

use Acts\DiaryBundle\Event\MultiDayEvent;

class MultiDayEventTest extends \PHPUnit_Framework_TestCase
{

    public function testMultiDayEvent()
    {
        $event = new MultiDayEvent();
        $event->setStartDate(new \DateTime('2014-02-01'));
        $event->setExcludeDate(new \DateTime('2014-02-05'));
        $event->setEndDate(new \DateTime('2014-02-07'));

        $this->assertEquals('1 February 2014', $event->getStartDate()->format('j F Y'));
        $this->assertEquals('5 February 2014', $event->getExcludeDate()->format('j F Y'));
        $this->assertEquals('7 February 2014', $event->getEndDate()->format('j F Y'));
    }

} 