<?php
namespace Acts\DiaryBundle\Diary;

use Acts\DiaryBundle\Event\EventInterface;

class Week
{
    private $rows = array();

    private $start;

    private $end;

    public function __construct(\DateTime $start) {
        $this->start = self::getWeekStart($start);
        $this->end = clone $this->start;
        $this->end->modify('+7 days');
    }

    public static function getWeekStart(\DateTime $date)
    {
        $day = $date->format('N');
        $start = clone $date;
        if ($day < 7) $start->modify('-'.$day.' days');
        $start->setTime(0, 0, 0);
        return $start;
    }

    public function contains(\DateTime $date) {
        return $this->start <= $date && $date < $this->end;
    }

    public function intersects(\DateTime $start, \DateTime $end) {
        return $this->start <= $start && $start < $this->end
            || $this->start <= $end && $end < $this->end
            || $start < $this->start && $this->end < $end;
    }

    public function addEvent(EventInterface $event)
    {
        foreach ($this->rows as $row) {
            if ($row->canAccept($event)) {
                $row->addEvent($event);
                return;
            }
        }

        $row = new DiaryRow($event->getStartTime(), $this->getStartAt());
        $row->addEvent($event);
        $id = $row->getStartTime()->format('U');

        if (isset($this->rows[$id])) {
            $baseid = $id;
            $counter = 1;
            while (isset($this->rows[$id])) {
                $id = $baseid.'_'.$counter;
                $counter++;
            }
        }

        $this->rows[$id] = $row;
    }

    public function getRows()
    {
        return $this->rows;
    }

    public function getStartAt()
    {
        return $this->start;
    }

    public function getEndAt()
    {
        return $this->end;
    }

    public function sort()
    {
        ksort($this->rows);
    }

    public function getHeaderDates()
    {
        $dates = array();
        for ($date = clone $this->start; $date < $this->end; $date->modify('+1 day')) {
            $dates[] = clone $date;
        }
        return $dates;

    }
}