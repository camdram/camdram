<?php

namespace Camdram\Tests\DiaryBundle\Diary;

use Acts\DiaryBundle\Diary\Week;
use Acts\DiaryBundle\Model\Event;
use PHPUnit\Framework\TestCase;

class DiaryWeekTest extends TestCase
{
    private $week;

    public function setUp(): void
    {
        $this->week = new Week(new \DateTime('2014-01-26'));
    }

    public function testGetWeekStart()
    {
        $this->assertEquals('2014-01-26', Week::getWeekStart(new \DateTime('2014-01-26'))->format('Y-m-d'));
        $this->assertEquals('2014-01-26', Week::getWeekStart(new \DateTime('2014-01-29'))->format('Y-m-d'));
        $this->assertEquals('2014-01-26', Week::getWeekStart(new \DateTime('2014-02-01'))->format('Y-m-d'));
        $this->assertEquals('2014-02-02', Week::getWeekStart(new \DateTime('2014-02-02'))->format('Y-m-d'));
    }

    public function testContains()
    {
        $this->assertFalse($this->week->contains(new \DateTime('2014-01-25')));
        $this->assertTrue($this->week->contains(new \DateTime('2014-01-26')));
        $this->assertTrue($this->week->contains(new \DateTime('2014-01-29')));
        $this->assertTrue($this->week->contains(new \DateTime('2014-02-01')));
        $this->assertFalse($this->week->contains(new \DateTime('2014-02-02')));
    }

    public function testIntersects()
    {
        $this->assertFalse($this->week->intersects(new \DateTime('2014-01-10'), new \DateTime('2014-01-25')));
        $this->assertTrue($this->week->intersects(new \DateTime('2014-01-26'), new \DateTime('2014-01-26')));
        $this->assertTrue($this->week->intersects(new \DateTime('2014-01-26'), new \DateTime('2014-01-30')));
        $this->assertTrue($this->week->intersects(new \DateTime('2014-01-20'), new \DateTime('2014-01-27')));
        $this->assertTrue($this->week->intersects(new \DateTime('2014-02-01'), new \DateTime('2014-02-10')));
        $this->assertFalse($this->week->intersects(new \DateTime('2014-02-02'), new \DateTime('2014-02-12')));
    }

    public function testGetters()
    {
        $this->assertEquals('2014-01-26', $this->week->getStartAt()->format('Y-m-d'));
        $this->assertEquals('2014-02-02', $this->week->getEndAt()->format('Y-m-d'));
    }

    public function testAddEvent()
    {
        $event = new Event();
        $event->setStartAt(new \DateTime('2014-02-01 14:00'));
        $event->setEndAt(new \DateTime('2014-02-01 15:00'));
        $this->week->addEvent($event);

        $rows = $this->week->getRows();
        $this->assertEquals(1, count($rows));
        $row = current($rows);
        $this->assertEquals(14 * 60, $row->getStartTime());
    }

    public function testAddEvent_DifferentDays()
    {
        $event1 = new Event();
        $event1->setStartAt(new \DateTime('2014-01-29 14:00'));
        $event1->setEndAt(new \DateTime('2014-01-29 15:00'));

        $event2 = new Event();
        $event2->setStartAt(new \DateTime('2014-02-01 14:00'));
        $event2->setEndAt(new \DateTime('2014-02-01 15:00'));

        $this->week->addEvent($event1);
        $this->week->addEvent($event2);

        $rows = $this->week->getRows();
        $this->assertEquals(1, count($rows));
    }

    public function testAddEvent_SameDay()
    {
        $event1 = new Event();
        $event1->setStartAt(new \DateTime('2014-01-29 14:00'));
        $event1->setEndAt(new \DateTime('2014-01-29 5:00'));

        $event2 = new Event();
        $event2->setStartAt(new \DateTime('2014-01-29 14:00'));
        $event2->setEndAt(new \DateTime('2014-01-29 15:00'));

        $this->week->addEvent($event1);
        $this->week->addEvent($event2);

        $rows = $this->week->getRows();
        $this->assertEquals(2, count($rows));
    }
}
