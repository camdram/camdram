<?php

namespace Camdram\Tests\DiaryBundle\Diary;

use Acts\DiaryBundle\Diary\DiaryItem;
use Acts\DiaryBundle\Diary\DiaryRow;
use Acts\DiaryBundle\Model\Event;
use PHPUnit\Framework\TestCase;

class DiaryRowTest extends TestCase
{
    /**
     * @var DiaryRow
     */
    private $row;

    public function setUp(): void
    {
        $this->row = new DiaryRow(new \DateTime('2014-02-01'));
    }

    public function testGetTimes()
    {
        $item = new DiaryItem();
        $item->setStartAt(new \DateTime('2014-02-01 15:00'));
        $item->setEndAt(new \DateTime('2014-02-01 16:00'));
        $this->row->addItem($item);

        $this->assertEquals(15 * 60, $this->row->getStartTime());
        $this->assertEquals('1 February 2014', $this->row->getStartDate()->format('j F Y'));
    }

    public function testAddSingleDayEvent()
    {
        $event = new Event();
        $event->setStartAt(new \DateTime('2014-02-03 14:00'));
        $event->setEndAt(new \DateTime('2014-02-03 15:00'));
        $this->row->addEvent($event);

        $item = current($this->row->getItems());
        $this->assertEquals(2, $item->getStartIndex());
        $this->assertEquals(2, $item->getEndIndex());
        $this->assertEquals(1, $item->getNumberOfDays());
    }

    public function testAddMultiDayEvent()
    {
        $event = new Event();
        $event->setStartAt(new \DateTime('2014-02-02 14:00'));
        $event->setEndAt(new \DateTime('2014-02-02 15:00'));
        $event->setRepeatUntil(new \DateTime('2014-02-05'));
        $this->row->addEvent($event);

        $item = current($this->row->getItems());
        $this->assertEquals(1, $item->getStartIndex());
        $this->assertEquals(4, $item->getEndIndex());
        $this->assertEquals(4, $item->getNumberOfDays());
    }

    public function testAddMultiDayEvent_OverlapStart()
    {
        $event = new Event();
        $event->setStartAt(new \DateTime('2014-01-30 14:00'));
        $event->setStartAt(new \DateTime('2014-01-30 15:00'));
        $event->setRepeatUntil(new \DateTime('2014-02-02'));
        $this->row->addEvent($event);

        $item = current($this->row->getItems());
        $this->assertEquals(0, $item->getStartIndex());
        $this->assertEquals(1, $item->getEndIndex());
        $this->assertEquals(2, $item->getNumberOfDays());
    }

    public function testAddMultiDayEvent_OverlapEnd()
    {
        $event = new Event();
        $event->setStartAt(new \DateTime('2014-02-06 14:00'));
        $event->setRepeatUntil(new \DateTime('2014-02-10'));
        $this->row->addEvent($event);

        $item = current($this->row->getItems());
        $this->assertEquals(5, $item->getStartIndex());
        $this->assertEquals(6, $item->getEndIndex());
        $this->assertEquals(2, $item->getNumberOfDays());
    }

    public function testCanAccept_WithinTimeThreshold()
    {
        $event1 = new Event();
        $event1->setStartAt(new \DateTime('2014-02-01 14:00'));
        $this->row->addEvent($event1);

        $event2= new Event();
        $event2->setStartAt(new \DateTime('2014-02-02 14:15'));

        $this->assertTrue($this->row->canAccept($event2));
    }

    public function testCanAccept_OutsideTimeThreshold()
    {
        $event1 = new Event();
        $event1->setStartAt(new \DateTime('2014-02-01 14:00'));
        $this->row->addEvent($event1);

        $event2 = new Event();

        //after
        $event2->setStartAt(new \DateTime('2014-02-02 16:00'));
        $this->assertFalse($this->row->canAccept($event2));

        //before
        $event2->setStartAt(new \DateTime('2014-02-02 12:00'));
        $this->assertFalse($this->row->canAccept($event2));
    }

    public function testCanAccept_Clash()
    {
        $event1 = new Event();
        $event1->setStartAt(new \DateTime('2014-02-01 14:00'));
        $event1->setEndAt(new \DateTime('2014-02-01 15:00'));
        $this->row->addEvent($event1);

        $event2 = new Event();
        $event2->setStartAt(new \DateTime('2014-02-01 14:00'));
        $event2->setEndAt(new \DateTime('2014-02-01 15:00'));

        $this->assertFalse($this->row->canAccept($event2));
    }

    public function testCanAccept_MultiDay()
    {
        $event1 = new Event();
        $event1->setStartAt(new \DateTime('2014-02-02 14:00'));
        $event1->setEndAt(new \DateTime('2014-02-02 15:00'));
        $event1->setRepeatUntil(new \DateTime('2014-02-04'));
        $this->row->addEvent($event1);

        $event2 = new Event();
        $event2->setStartAt(new \DateTime('2014-02-01 14:00'));
        $event2->setEndAt(new \DateTime('2014-02-01 15:00'));

        $this->assertTrue($this->row->canAccept($event2));
    }

    public function testCanAccept_MultiDayClash()
    {
        $event1 = new Event();
        $event1->setStartAt(new \DateTime('2014-02-02 14:00'));
        $event1->setEndAt(new \DateTime('2014-02-02 15:00'));
        $event1->setRepeatUntil(new \DateTime('2014-02-04'));
        $this->row->addEvent($event1);

        $event2 = new Event();
        $event2->setStartAt(new \DateTime('2014-02-02 14:00'));
        $event2->setEndAt(new \DateTime('2014-02-02 15:00'));

        $this->assertFalse($this->row->canAccept($event2));
    }
}
