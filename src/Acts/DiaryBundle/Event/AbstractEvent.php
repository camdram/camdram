<?php
namespace Acts\DiaryBundle\Event;

abstract class AbstractEvent implements EventInterface
{
    private $name;

    private $venue;

    private $start_time;

    private $end_time;

    private $link;

    private $venue_link;

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getVenue()
    {
        return $this->venue;
    }

    public function getLink()
    {
        return $this->link;
    }

    public function setLink($link)
    {
        $this->link = $link;
    }

    public function setVenue($venue)
    {
        $this->venue = $venue;
    }

    public function getVenueLink()
    {
        return $this->venue_link;
    }

    public function setVenueLink($link)
    {
        $this->venue_link = $link;
    }

    public function getStartTime()
    {
        return $this->start_time;
    }

    public function setStartTime(\DateTime $start_time)
    {
        $this->start_time = $start_time;
    }

    public function getEndTime()
    {
        return $this->end_time;
    }

    public function setEndTime(\DateTime $end_time)
    {
        $this->start_time = $end_time;
    }
}