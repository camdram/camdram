<?php

namespace Acts\DiaryBundle\Diary;

use Acts\DiaryBundle\Model\EventInterface;
use JMS\Serializer\Annotation as Serializer;

/**
 * Class Diary
 *
 * A model representing a diary - essentially a container of events. It can also optionally take a start_date and
 * end_date, which, if specified, cause the diary to ignore events or parts of multi-day events that take place
 * outside that time period. Only start and end dates that are at the beginning/end of weeks (midnight Sunday morning)
 * have been tested.
 *
 * @Serializer\XmlRoot("diary")
 */
class Diary
{
    /**
     * @var array<EventInterface> An array of the events that have been added to the Diary.
     *
     * @Serializer\XmlList(inline = true, entry = "event")
     */
    private $events = array();

    /**
     * @var array<Label> An array of week names and time periods relevant to the diary
     *
     * @Serializer\XmlList(inline = true, entry = "label")
     */
    private $labels = array();

    /**
     * @var ?\DateTime
     *
     * @Serializer\XmlElement(cdata=false)
     * @Serializer\Type("DateTime<'Y-m-d'>")
     */
    private $start_date;

    /**
     * @var ?\DateTime
     *
     * @Serializer\XmlElement(cdata=false)
     * @Serializer\Type("DateTime<'Y-m-d'>")
     */
    private $end_date;

    /**
     * Add a single event to the diary
     */
    public function addEvent(EventInterface $event): void
    {
        $this->events[] = $event;
    }

    /**
     * A quick way of adding lots of events to the diary
     * @param array<EventInterface> $events
     */
    public function addEvents(iterable $events): void
    {
        foreach ($events as $event) {
            $this->addEvent($event);
        }
    }

    /**
     * Set the range of dates that the diary should display. Events or parts of multi-day events outside this range
     * will not be displayed. Only tested for ranges that are a whole number of weeks starting on Sunday.
     */
    public function setDateRange(\DateTime $start_date, \DateTime $end_date): void
    {
        $this->start_date = $start_date;
        $this->end_date = $end_date;
    }

    public function addLabel(string $type, string $text, \DateTime $start_at, \DateTime $end_at = null): void
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
        usort($this->events, function($a, $b) {
            return ($a->getStartAt()->getTimestamp() % 86400) <=> ($b->getStartAt()->getTimestamp() % 86400);
        });
        $view->addEvents($this->events);
        $view->addLabels($this->labels);

        return $view;
    }

    /**
     * Return all events in the diary
     *
     * @return array<EventInterface>
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
