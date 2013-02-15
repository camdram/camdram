<?php
namespace Acts\DiaryBundle\Diary;

use Acts\DiaryBundle\Event\EventInterface;

class Diary
{
    private $events = array();

    private $start_date;

    private $end_date;

    public function addEvent(EventInterface $event)
    {
        $this->events[] = $event;
    }

    public function addEvents(array $events)
    {
        foreach ($events as $event) {
            $this->addEvent($event);
        }
    }


    public function setDateRange(\DateTime $start_date, \DateTime $end_date)
    {
        $this->start_date = $start_date;
        $this->end_date = $end_date;
    }

    public function createView()
    {
        $view = new DiaryView();
        if ($this->start_date && $this->end_date) {
            $view->setDateRange($this->start_date, $this->end_date);
        }
        $view->addEvents($this->events);
        $view->sort();
        return $view;
    }
}