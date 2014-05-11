<?php
namespace Acts\DiaryBundle\Diary;

use Acts\DiaryBundle\Event\EventInterface;
use Acts\DiaryBundle\Event\SingleDayEventInterface;
use Acts\DiaryBundle\Event\MultiDayEventInterface;

class DiaryRow
{
    /**
     * All the events within a single row of the diary must start within this number of minutes. A higher number
     * causes the diary to be more compact, but looks more confusing as events with different times appear in the same
     * row
     */
    const MAX_ROW_RANGE_MINUTES = 30;

    private $items = array();

    private $start;

    private $end;

    private $start_date;

    public function __construct(\DateTime $start_time, \DateTime $start_date)
    {
        $this->start = clone $start_time;
        $this->end = clone $start_time;
        $this->start_date = $start_date;
    }

    private function calculateIndex(\DateTime $date)
    {
        $diff = $this->start_date->diff($date, false);
        $days = $diff->days;
        if ($diff->invert) $days *= -1;
        if ($days < 0) $days = 0;
        if ($days > 6) $days = 6;
        return $days;
    }

    /**
     * @param int $start_index
     * @param int $end_index
     * @return bool
     */
    public function rangeIsFree($start_index, $end_index)
    {
        foreach ($this->items as $item) {
            $item_start = $item->getStartIndex();
            $item_end = $item->getEndIndex();
            if ($start_index <= $item_end && $end_index >= $item_start) return false;
        }
        return true;
    }

    /**
     * @param EventInterface $event
     * @return bool
     */
    public function canAccept(EventInterface $event)
    {
        //First check if the time is the same (within a certain threshold)
        $earliest = clone $this->start;
        $earliest->modify('-'.(self::MAX_ROW_RANGE_MINUTES+1).' minutes');
        $latest = clone $this->start;
        $latest->modify('+'.(self::MAX_ROW_RANGE_MINUTES+1).' minutes');
        if ($event->getStartTime() <= $earliest || $event->getStartTime() >= $latest) return false;

        //Now see if there's space in the row
        if ($event instanceof SingleDayEventInterface) {
            $index = $this->calculateIndex($event->getDate());
            return $this->rangeIsFree($index, $index);
        } elseif ($event instanceof MultiDayEventInterface) {
            $start_index = $this->calculateIndex($event->getStartDate());
            $end_index = $this->calculateIndex($event->getEndDate());
            return $this->rangeIsFree($start_index, $end_index);
        }
    }

    public function addItem(DiaryItem $item)
    {
        $this->items[$item->getStartIndex()] = $item;

        if (!$this->start || $item->getStartAt() < $this->start) {
            $this->start = $item->getStartAt();
        }
        if (!$this->end || $item->getStartAt() > $this->end) {
            $this->end = $item->getStartAt();
        }
    }

    public function addEvent(EventInterface $event)
    {
        $item = new DiaryItem();
        $item->setEvent($event);
        $item->setStartAt($event->getStartTime());
        $item->setEndAt($event->getEndTime());

        if ($event instanceof SingleDayEventInterface) {
            $item->setNumberOfDays(1);
            $item->setStartIndex($this->calculateIndex($event->getDate()));
            $this->addItem($item);
        } elseif ($event instanceof MultiDayEventInterface) {
            if ($event->getStartDate() < $event->getExcludeDate()
                    && $event->getExcludeDate() < $event->getEndDate()) {
                $start_index = $this->calculateIndex($event->getStartDate());
                $end_index = $this->calculateIndex($event->getExcludeDate());
                $item->setStartIndex($start_index);
                $item->setNumberOfDays($end_index - $start_index);
                $this->addItem($item);

                $item2 = clone $item;
                $start_index = $this->calculateIndex($event->getExcludeDate());
                $end_index = $this->calculateIndex($event->getEndDate());
                $item2->setStartIndex($start_index+1);
                $item2->setNumberOfDays($end_index - $start_index);
                $this->addItem($item2);
            } else {

                $start_index = $this->calculateIndex($event->getStartDate());
                $end_index = $this->calculateIndex($event->getEndDate());
                $numberOfDays = $end_index - $start_index + 1;
                if ($numberOfDays > 0) {
                    $item->setStartIndex($start_index);
                    $item->setNumberOfDays($end_index - $start_index + 1);
                    $this->addItem($item);
                }
            }
        }
    }

    public function getItems()
    {
        ksort($this->items);
        return $this->items;
    }

    public function getStartTime()
    {
        return $this->start;
    }

    public function getEndTime()
    {
        return $this->end;
    }

    public function getStartDate()
    {
        return $this->start_date;
    }
}
