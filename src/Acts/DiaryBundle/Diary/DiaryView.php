<?php
namespace Acts\DiaryBundle\Diary;

use Acts\DiaryBundle\Event\EventInterface;
use Acts\DiaryBundle\Event\MultiDayEventInterface;
use Acts\DiaryBundle\Event\SingleDayEventInterface;

class DiaryView
{

    private $weeks = array();

    private $start_date;

    private $end_date;

    public function getWeeks()
    {
        return $this->weeks;
    }

    /**
     * @param \DateTime $date
     * @return Week
     */
    private function getWeekForDate(\DateTime $date)
    {
        if ($this->start_date && $this->end_date) {
            if ($date < $this->start_date || $date >= $this->end_date) return;
        }

        foreach ($this->weeks as $week) {
            if ($week->contains($date)) return $week;
        }

        $week = new Week($date);
        $this->weeks[$week->getStartAt()->format('U')] = $week;
        return $week;
    }

    public function addEvent(EventInterface $event)
    {
        if ($event instanceof MultiDayEventInterface) {
            $week_start = Week::getWeekStart($event->getStartDate());
            do {
                $week = $this->getWeekForDate($week_start);
                if ($week) $week->addEvent($event);
                $week_start->modify('+7 days');
            } while ($week_start < $event->getEndDate());
        }
        elseif ($event instanceof SingleDayEventInterface) {
            $this->getWeekForDate($event->getDate())->addEvent($event);

            //If the event goes on until the next day
            if ($event->getEndTime() < $event->getStartTime()
                        && $event->getDate()->format('N') == 6) {
                $tomorrow = $event->getDate()->modify('+1 day');
                $week = $this->getWeekForDate($tomorrow);
                if ($week) $week->addEvent($event);
            }
        }
    }

    public function addEvents(array $events)
    {
        foreach ($events as $event) {
            $this->addEvent($event);
        }
    }

    public function sort()
    {
        ksort($this->weeks);
        foreach ($this->weeks as $week) {
            $week->sort();
        }
    }

    public function setDateRange(\DateTime $start_date, \DateTime $end_date)
    {
        $this->start_date = $start_date;
        $this->end_date = $end_date;
    }

}