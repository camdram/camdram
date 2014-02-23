<?php
namespace Acts\DiaryBundle\Tests\Diary;

use Acts\DiaryBundle\Diary\DiaryItem;
use Acts\DiaryBundle\Diary\DiaryRow;
use Acts\DiaryBundle\Event\MultiDayEvent;
use Acts\DiaryBundle\Event\SingleDayEvent;

class DiaryRowTest extends \PHPUnit_Framework_TestCase
{

    private $row;

    public function setUp()
    {
        $this->row = new DiaryRow(new \DateTime('14:00'), new \DateTime('2014-02-01'));
    }

    public function testGetTimes()
    {
        $item = new DiaryItem();
        $item->setStartAt(new \DateTime('15:00'));
        $item->setEndAt(new \DateTime('16:00'));
        $this->row->addItem($item);

        $this->assertEquals('14:00', $this->row->getStartTime()->format('H:i'));
        $this->assertEquals('15:00', $this->row->getEndTime()->format('H:i'));
        $this->assertEquals('1 February 2014', $this->row->getStartDate()->format('j F Y'));
    }

    public function testAddSingleDayEvent()
    {
        $event = new SingleDayEvent();
        $event->setDate(new \DateTime('2014-02-03'));
        $event->setStartTime(new \DateTime('14:00'));
        $event->setEndTime(new \DateTime('15:00'));
        $this->row->addEvent($event);

        $item = current($this->row->getItems());
        $this->assertEquals(2, $item->getStartIndex());
        $this->assertEquals(2, $item->getEndIndex());
        $this->assertEquals(1, $item->getNumberOfDays());
    }

    public function testAddMultiDayEvent()
    {
        $event = new MultiDayEvent();
        $event->setStartDate(new \DateTime('2014-02-02'));
        $event->setEndDate(new \DateTime('2014-02-05'));
        $event->setStartTime(new \DateTime('14:00'));
        $event->setEndTime(new \DateTime('15:00'));
        $this->row->addEvent($event);

        $item = current($this->row->getItems());
        $this->assertEquals(1, $item->getStartIndex());
        $this->assertEquals(4, $item->getEndIndex());
        $this->assertEquals(4, $item->getNumberOfDays());
    }

    public function testAddMultiDayEvent_OverlapStart()
    {
        $event = new MultiDayEvent();
        $event->setStartDate(new \DateTime('2014-01-30'));
        $event->setEndDate(new \DateTime('2014-02-02'));
        $event->setStartTime(new \DateTime('14:00'));
        $event->setEndTime(new \DateTime('15:00'));
        $this->row->addEvent($event);

        $item = current($this->row->getItems());
        $this->assertEquals(0, $item->getStartIndex());
        $this->assertEquals(1, $item->getEndIndex());
        $this->assertEquals(2, $item->getNumberOfDays());
    }

    public function testAddMultiDayEvent_OverlapEnd()
    {
        $event = new MultiDayEvent();
        $event->setStartDate(new \DateTime('2014-02-06'));
        $event->setEndDate(new \DateTime('2014-02-10'));
        $event->setStartTime(new \DateTime('14:00'));
        $event->setEndTime(new \DateTime('15:00'));
        $this->row->addEvent($event);

        $item = current($this->row->getItems());
        $this->assertEquals(5, $item->getStartIndex());
        $this->assertEquals(6, $item->getEndIndex());
        $this->assertEquals(2, $item->getNumberOfDays());
    }

    public function testAddMultiDayEvent_ExcludeDate()
    {
        $event = new MultiDayEvent();
        $event->setStartDate(new \DateTime('2014-02-03'));
        $event->setExcludeDate(new \DateTime('2014-02-05'));
        $event->setEndDate(new \DateTime('2014-02-10'));
        $event->setStartTime(new \DateTime('14:00'));
        $event->setEndTime(new \DateTime('15:00'));
        $this->row->addEvent($event);

        $items = $this->row->getItems();
        $this->assertEquals(2, $items[2]->getStartIndex());
        $this->assertEquals(3, $items[2]->getEndIndex());
        $this->assertEquals(2, $items[2]->getNumberOfDays());
        $this->assertEquals(5, $items[5]->getStartIndex());
        $this->assertEquals(6, $items[5]->getEndIndex());
        $this->assertEquals(2, $items[5]->getNumberOfDays());
    }

    public function testCanAccept_WithinTimeThreshold()
    {
        $event = new SingleDayEvent();
        $event->setDate(new \DateTime('2014-02-01'));
        $event->setStartTime(new \DateTime('14:00'));
        $event->setEndTime(new \DateTime('15:00'));
        $this->assertTrue($this->row->canAccept($event));
    }

    public function testCanAccept_OutsideTimeThreshold()
    {
        $event = new SingleDayEvent();
        $event->setDate(new \DateTime('2014-02-01'));
        $event->setStartTime(new \DateTime('16:00'));
        $event->setEndTime(new \DateTime('17:00'));
        $this->assertFalse($this->row->canAccept($event));
    }

    public function testCanAccept_Clash()
    {
        $event1 = new SingleDayEvent();
        $event1->setDate(new \DateTime('2014-02-01'));
        $event1->setStartTime(new \DateTime('14:00'));
        $event1->setEndTime(new \DateTime('15:00'));
        $this->row->addEvent($event1);

        $event2 = new SingleDayEvent();
        $event2->setDate(new \DateTime('2014-02-01'));
        $event2->setStartTime(new \DateTime('14:00'));
        $event2->setEndTime(new \DateTime('15:00'));

        $this->assertFalse($this->row->canAccept($event2));
    }

    public function testCanAccept_MultiDay()
    {
        $event1 = new MultiDayEvent();
        $event1->setStartDate(new \DateTime('2014-02-02'));
        $event1->setEndDate(new \DateTime('2014-02-04'));
        $event1->setStartTime(new \DateTime('14:00'));
        $event1->setEndTime(new \DateTime('15:00'));
        $this->row->addEvent($event1);

        $event2 = new SingleDayEvent();
        $event2->setDate(new \DateTime('2014-02-01'));
        $event2->setStartTime(new \DateTime('14:00'));
        $event2->setEndTime(new \DateTime('15:00'));

        $this->assertTrue($this->row->canAccept($event2));
    }

    public function testCanAccept_MultiDayClash()
    {
        $event1 = new MultiDayEvent();
        $event1->setStartDate(new \DateTime('2014-02-02'));
        $event1->setEndDate(new \DateTime('2014-02-04'));
        $event1->setStartTime(new \DateTime('14:00'));
        $event1->setEndTime(new \DateTime('15:00'));
        $this->row->addEvent($event1);

        $event2 = new SingleDayEvent();
        $event2->setDate(new \DateTime('2014-02-02'));
        $event2->setStartTime(new \DateTime('14:00'));
        $event2->setEndTime(new \DateTime('15:00'));

        $this->assertFalse($this->row->canAccept($event2));
    }

}