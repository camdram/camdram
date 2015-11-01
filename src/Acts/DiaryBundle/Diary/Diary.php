<?php

namespace Acts\DiaryBundle\Diary;

use Acts\DiaryBundle\Event\EventInterface;

/**
 * Class Diary
 *
 * A model representing a diary - essentially a container of events. It can also optionally take a start_date and
 * end_date, which, if specified, cause the diary to ignore events or parts of multi-day events that take place
 * outside that time period. Only start and end dates that are at the beginning/end of weeks (midnight Sunday morning)
 * have been tested.
 */
class Diary
{
    /**
     * @var array An array of the events that have been added to the Diary.
     */
    private $events = array();

    private $labels = array();

    /**
     * @var \DateTime
     */
    private $start_date;

    /**
     * @var \DateTime
     */
    private $end_date;

    /**
     * Add a single event to the diary
     *
     * @param EventInterface $event
     */
    public function addEvent(EventInterface $event)
    {
        $this->events[] = $event;
    }

    /**
     * A quick way of adding lots of events to the diary
     *
     * @param array $events
     */
    public function addEvents(array $events)
    {
        foreach ($events as $event) {
            $this->addEvent($event);
        }
    }

    /**
     * Set the range of dates that the diary should display. Events or parts of multi-day events outside this range
     * will not be displayed. Only tested for ranges that are a whole number of weeks starting on Sunday.
     *
     * @param \DateTime $start_date
     * @param \DateTime $end_date
     */
    public function setDateRange(\DateTime $start_date, \DateTime $end_date)
    {
        $this->start_date = $start_date;
        $this->end_date = $end_date;
    }

    public function addLabel($type, $text, \DateTime $start_at, \DateTime $end_at = null)
    {
        $this->labels[] = new Label($type, $text, $start_at, $end_at);
    }

    /**
     * Create a DiaryView of this Diary (which contains details about how to render it)
     *
     * @return DiaryView
     */
    public function createView()
    {
        $view = new DiaryView();
        if ($this->start_date && $this->end_date) {
            $view->setDateRange($this->start_date, $this->end_date);
        }
        $view->addEvents($this->events);
        $view->addLabels($this->labels);

        return $view;
    }

    /**
     * Return all events in the diary
     *
     * @return array
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * Returns the diary's start date
     *
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->start_date;
    }

    /**
     * Returns the diary's end date
     *
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->end_date;
    }
}
