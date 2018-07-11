<?php

namespace Camdram\Tests\DiaryBundle\Diary;

use Acts\DiaryBundle\Diary\DiaryItem;
use Acts\DiaryBundle\Event\SingleDayEvent;
use PHPUnit\Framework\TestCase;

class DiaryItemTest extends TestCase
{
    public function testDiaryItem()
    {
        $event = new SingleDayEvent();
        $event->setName('Test Event');

        $item = new DiaryItem();
        $item->setStartAt(new \DateTime('2014-02-01'));
        $item->setEndAt(new \DateTime('2014-02-05'));
        $item->setStartIndex(1);
        $item->setNumberOfDays(4);
        $item->setEvent($event);

        $this->assertEquals('1 February 2014', $item->getStartAt()->format('j F Y'));
        $this->assertEquals('5 February 2014', $item->getEndAt()->format('j F Y'));
        $this->assertEquals(1, $item->getStartIndex());
        $this->assertEquals(4, $item->getNumberOfDays());
        $this->assertEquals($event, $item->getEvent());
    }

    public function testGetEndIndex()
    {
        $item = new DiaryItem();
        $item->setStartIndex(1);
        $item->setNumberOfDays(4);

        $this->assertEquals(4, $item->getEndIndex());
    }
}
