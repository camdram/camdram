<?php

namespace Acts\DiaryBundle\Diary;

use Acts\DiaryBundle\Model\EventInterface;

/**
 * Class DiaryView
 *
 * A view of a Diary object. This class is the base of the logic for working out how to output diaries. A DiaryView
 * contains a number of Weeks. The weeks that are shown are decided upon based upon the events that have been added
 * (so long as they are within the specified time period).
 */
class DiaryView
{
    /**
     * @var array<Week> Array of Week objects, keyed of the Unix timestamp of each week's start time for easy sorting.
     */
    private $weeks = array();

    /**
     * @var ?\DateTime The earliest date that may appear in the diary view
     */
    private $start_date;

    /**
     * @var ?\DateTime The latest date that may appear in the diary view
     */
    private $end_date;

    /** @return array<Week> */
    public function getWeeks(): array
    {
        ksort($this->weeks);

        return $this->weeks;
    }

    /**
     * Returns (and creates if necessary) the week in which a certain \DateTime belongs
     */
    private function getWeekForDate(\DateTime $date): ?Week
    {
        if ($this->start_date && $this->end_date) {
            if ($date < $this->start_date || $date >= $this->end_date) {
                return null;
            }
        }

        foreach ($this->weeks as $week) {
            if ($week->contains($date)) {
                return $week;
            }
        }

        $week = new Week($date);
        $this->weeks[$week->getStartAt()->format('U')] = $week;

        return $week;
    }

    /**
     * Add an event to the DiaryView. Works out the week(s) to which the event should be added. Multi-day events
     * may need to be added to multiple weeks if they cross a week boundary.
     */
    public function addEvent(EventInterface $event): void
    {
        //If it's a multi-day event, we may well need to display it in multiple weeks
        $week_start = Week::getWeekStart($event->getStartAt());
        do {
            $week = $this->getWeekForDate($week_start);
            if ($week) {
                $week->addEvent($event);
            }
            $week_start->modify('+7 days');
        } while ($week_start < $event->getRepeatUntil());
    }

    /**
     * A quick way of adding lots of events to the view
     * @param EventInterface[] $events
     */
    public function addEvents(array $events): void
    {
        foreach ($events as $event) {
            $this->addEvent($event);
        }
    }

    public function addLabel(Label $label): void
    {
        if ($label->getType() == Label::TYPE_WEEK) {
            $this->getWeekForDate($label->getStartAt())->setLabel($label);
        } elseif ($label->getType() == Label::TYPE_PERIOD) {
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

    /** @param Label[] $labels */
    public function addLabels(array $labels): void
    {
        foreach ($labels as $label) {
            $this->addLabel($label);
        }
    }

    /**
     * Sets the earliest and latest dates that can be shown in the diary view.
     *
     * @param \DateTime $start_date
     * @param \DateTime $end_date
     */
    public function setDateRange(\DateTime $start_date, \DateTime $end_date): void
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
