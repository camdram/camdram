<?php
namespace Acts\DiaryBundle\Diary;

use Acts\DiaryBundle\Event\EventInterface;
use Acts\DiaryBundle\Event\MultiDayEventInterface;
use Acts\DiaryBundle\Event\SingleDayEventInterface;

/**
 * Class DiaryView
 *
 * A view of a Diary object. This class is the base of the logic for working out how to output diaries. A DiaryView
 * contains a number of Weeks. The weeks that are shown are decided upon based upon the events that have been added
 * (so long as they are within the specified time period).
 *
 * @package Acts\DiaryBundle\Diary
 */
class DiaryView
{
    /**
     * @var array Array of Week objects, keyed of the Unix timestamp of each week's start time for easy sorting.
     */
    private $weeks = array();

    /**
     * @var \DateTime The earliest date that may appear in the diary view
     */
    private $start_date;

    /**
     * @var \DateTime The latest date that may appear in the diary view
     */
    private $end_date;

    public function getWeeks()
    {
        return $this->weeks;
    }

    /**
     * Returns (and creates if necessary) the week in which a certain \DateTime belongs
     *
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

    /**
     * Add an event to the DiaryView. Works out the week(s) to which the event should be added. Multi-day events
     * may need to be added to multiple weeks if they cross a week boundary.
     *
     * @param EventInterface $event
     */
    public function addEvent(EventInterface $event)
    {
        if ($event instanceof MultiDayEventInterface) {
            //If it's a multi-day event, we may well need to display it in multiple weeks
            $week_start = Week::getWeekStart($event->getStartDate());
            do {
                $week = $this->getWeekForDate($week_start);
                if ($week) $week->addEvent($event);
                $week_start->modify('+7 days');
            } while ($week_start < $event->getEndDate());
        }
        elseif ($event instanceof SingleDayEventInterface) {
            $this->getWeekForDate($event->getDate())->addEvent($event);

            //If its end time is before its start time, we assume this means it continues until that time the next day
            if ($event->getEndTime() < $event->getStartTime()
                        && $event->getDate()->format('N') == 6) {
                $tomorrow = $event->getDate()->modify('+1 day');
                $week = $this->getWeekForDate($tomorrow);
                if ($week) $week->addEvent($event);
            }
        }
    }

    /**
     * A quick way of adding lots of events to the view
     *
     * @param array $events
     */
    public function addEvents(array $events)
    {
        foreach ($events as $event) {
            $this->addEvent($event);
        }
    }

    public function addLabel(Label $label)
    {
        if ($label->getType() == Label::TYPE_WEEK) {
            $this->getWeekForDate($label->getStartAt())->setLabel($label);
        }
        elseif ($label->getType() == Label::TYPE_PERIOD) {
            $found = false;
            foreach ($this->weeks as $week) {
                if ($week->contains($label->getStartAt())) {
                    $week->setPeriodLabel($label);
                    $found = true;
                    break;
                }
            }
            if (!$found && count($this->weeks) > 0 && $label->getStartAt() < reset($this->weeks)->getStartAt()) {
                //We haven't found a corresponding week. The start date is before the beginning of the period, so
                // add the period label to the first week.
                reset($this->weeks)->setPeriodLabel($label);
            }
        }
    }

    public function addLabels(array $labels)
    {
        foreach ($labels as $label) {
            $this->addLabel($label);
        }
    }

    /**
     * Once all the events have been added, this sorts the weeks, rows, and items into the correct order.
     */
    public function sort()
    {
        ksort($this->weeks);
        foreach ($this->weeks as $week) {
            $week->sort();
        }
    }

    /**
     * Sets the earliest and latest dates that can be shown in the diary view.
     *
     * @param \DateTime $start_date
     * @param \DateTime $end_date
     */
    public function setDateRange(\DateTime $start_date, \DateTime $end_date)
    {
        $this->start_date = $start_date;
        $this->end_date = $end_date;

        //Ensure all weeks in the period are present
        $date = clone $start_date;
        while ($date < $end_date) {
            $this->getWeekForDate($date);
            $date->modify('+1 week');
        }
    }

}