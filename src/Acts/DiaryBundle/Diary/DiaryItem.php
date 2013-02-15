<?php
namespace Acts\DiaryBundle\Diary;

use Acts\DiaryBundle\Event\EventInterface;

class DiaryItem
{

    private $start_at;

    private $end_at;

    private $event;

    private $num_days;

    private $index;

    public function getEvent()
    {
        return $this->event;
    }

    public function setEvent(EventInterface $event)
    {
        $this->event = $event;
    }

    public function getStartAt()
    {
        return $this->start_at;
    }

    public function setStartAt(\DateTime $start_at)
    {
        $this->start_at = $start_at;
    }

    public function getEndAt()
    {
        return $this->end_at;
    }

    public function setEndAt(\DateTime $end_at)
    {
        $this->start_at = $end_at;
    }

    public function getNumberOfDays()
    {
        return $this->num_days;
    }

    public function setNumberOfDays($days)
    {
        $this->num_days = $days;
    }

    public function getStartIndex()
    {
        return $this->index;
    }

    public function getEndIndex()
    {
        return $this->index + $this->num_days - 1;
    }

    public function setIndex($index)
    {
        $this->index = $index;
    }

}