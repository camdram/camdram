<?php

namespace Acts\DiaryBundle\Diary;

use Acts\DiaryBundle\Model\EventInterface;

class DiaryRow
{
    /**
     * All the events within a single row of the diary must start within this number of minutes. A higher number
     * causes the diary to be more compact, but looks more confusing as events with different times appear in the same
     * row
     */
    const MAX_ROW_RANGE_MINUTES = 30;

    private $items = array();

    private $start_time;

    private $start_date;

    public function __construct(\DateTime $start_at)
    {
        $this->start_date = clone $start_at;
        $this->start_date->setTime(0, 0, 0);
    }

    private function calculateIndex(\DateTime $date)
    {
        $diff = $this->start_date->diff($date, false);
        $days = $diff->days;
        if ($diff->invert) {
            $days *= -1;
        }
        if ($days < 0) {
            $days = 0;
        }
        if ($days > 6) {
            $days = 6;
        }

        return $days;
    }

    /**
     * @param int $start_index
     * @param int $end_index
     *
     * @return bool
     */
    public function rangeIsFree($start_index, $end_index)
    {
        foreach ($this->items as $item) {
            $item_start = $item->getStartIndex();
            $item_end = $item->getEndIndex();
            if ($start_index <= $item_end && $end_index >= $item_start) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param EventInterface $event
     *
     * @return bool
     */
    public function canAccept(EventInterface $event)
    {
        //First check if the time is the same (within a certain threshold) @phpstan-ignore-next-line
        $eventStartTime = $event->getStartAt()->format('H') * 60 + $event->getStartAt()->format('i');
        if ($this->start_time && (
                $eventStartTime <= $this->start_time - self::MAX_ROW_RANGE_MINUTES
                || $eventStartTime >= $this->start_time + self::MAX_ROW_RANGE_MINUTES)) {
            return false;
        }

        //Now see if there's space in the row
        $start_index = $this->calculateIndex($event->getStartAt());
        $end_index = $event->getRepeatUntil() ? $this->calculateIndex($event->getRepeatUntil()) : $start_index;

        return $this->rangeIsFree($start_index, $end_index);
    }

    public function addItem(DiaryItem $item)
    {
        $this->items[$item->getStartIndex()] = $item;

        // @phpstan-ignore-next-line
        $eventStartTime = $item->getStartAt()->format('H') * 60 + $item->getStartAt()->format('i');
        if (!$this->start_time || $eventStartTime < $this->start_time) {
            $this->start_time = $eventStartTime;
        }
    }

    public function addEvent(EventInterface $event)
    {
        $item = new DiaryItem();
        $item->setEvent($event);
        $item->setStartAt($event->getStartAt());
        $item->setEndAt($event->getEndAt());

        $start_index = $this->calculateIndex($event->getStartAt());
        $end_index = $event->getRepeatUntil() ? $this->calculateIndex($event->getRepeatUntil()) : $start_index;
        $numberOfDays = $end_index - $start_index + 1;

        if ($numberOfDays > 0) {
            $item->setStartIndex($start_index);
            $item->setNumberOfDays($end_index - $start_index + 1);
            $this->addItem($item);
        }
    }

    public function getItems()
    {
        ksort($this->items);

        return $this->items;
    }

    public function getStartTime()
    {
        return $this->start_time;
    }

    public function getStartDate()
    {
        return $this->start_date;
    }
}
