<?php

namespace Acts\DiaryBundle\Diary;

use Acts\DiaryBundle\Model\EventInterface;

/**
 * Class Week
 *
 * A single week in the diary. Consists of a grid, with a column for each day and number of rows. Events are placed
 * into rows, roughly corresponding to their event time.
 */
class Week
{
    /**
     * @var array An array of DiaryRow objects, keyed on the row's time for easy sorting
     */
    private $rows = array();

    /**
     * @var \DateTime The beginning of the week
     */
    private $start;

    /**
     * @var \DateTime The end of the week
     */
    private $end;

    /**
     * @var string Some text describing this week
     */
    private $label;

    /**
     * @var string Some text describing a number of weeks, starting at this week
     */
    private $period_label;

    /**
     * Weeks are created by passing their start date, from which the end date is calculated
     *
     * @param \DateTime $start
     */
    public function __construct(\DateTime $start)
    {
        $this->start = self::getWeekStart($start);
        $this->end = clone $this->start;
        $this->end->modify('+7 days');
    }

    /**
     * Given a particular date, returns the date of the beginning of the week
     *
     * @param \DateTime $date
     *
     * @return \DateTime
     */
    public static function getWeekStart(\DateTime $date)
    {
        $day = $date->format('N');
        $start = clone $date;
        if ($day < 7) {
            $start->modify('-'.$day.' days');
        }
        $start->setTime(0, 0, 0);

        return $start;
    }

    /**
     * Returns whether this week contains a given date
     *
     * @param \DateTime $date
     *
     * @return bool
     */
    public function contains(\DateTime $date)
    {
        return $this->start <= $date && $date < $this->end;
    }

    /**
     * Returns whether this week intersects a given time range
     *
     * @param \DateTime $start
     * @param \DateTime $end
     *
     * @return bool
     */
    public function intersects(\DateTime $start, \DateTime $end)
    {
        return $this->start <= $end && $start < $this->end;
    }

    protected function createRow(EventInterface $event)
    {
        return new DiaryRow($event->getStartTime(), $this->getStartAt());
    }

    public function addEvent(EventInterface $event)
    {
        //First work out if there is a row that has free space for the event, and represents a sufficiently similar time
        foreach ($this->rows as $row) {
            if ($row->canAccept($event)) {
                $row->addEvent($event);

                return;
            }
        }

        //No such row exists, so we create one
        $row = $this->createRow($event);
        $row->addEvent($event);
        $id = $row->getStartTime()->format('U');

        //There may be multiple rows representing the same time, so we add a suffix e.g. '_1', '_2' to the array key
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

    /**
     * @return array The array of DiaryRow objects
     */
    public function getRows()
    {
        ksort($this->rows);

        return $this->rows;
    }

    /**
     * @return \DateTime The date of the start of the week
     */
    public function getStartAt()
    {
        return $this->start;
    }

    /**
     * @return \DateTime The date of the end of the week
     */
    public function getEndAt()
    {
        return $this->end;
    }

    /**
     * @param \Acts\DiaryBundle\Diary\Label $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * @return \Acts\DiaryBundle\Diary\Label
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $period_label
     */
    public function setPeriodLabel($period_label)
    {
        $this->period_label = $period_label;
    }

    /**
     * @return string
     */
    public function getPeriodLabel()
    {
        return $this->period_label;
    }

    /**
     * Returns a array of 7 dates, representing the date of each day in the week. Used by the template to output the
     * column headers
     *
     * @return array
     */
    public function getHeaderDates()
    {
        $dates = array();
        for ($date = clone $this->start; $date < $this->end; $date->modify('+1 day')) {
            $dates[] = clone $date;
        }

        return $dates;
    }
}
