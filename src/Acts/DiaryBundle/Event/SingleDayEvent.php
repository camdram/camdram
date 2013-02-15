<?php
namespace Acts\DiaryBundle\Event;

class SingleEvent extends AbstractEvent implements SingleEventInterface
{
    private $date;

    public function getDate()
    {
        return $this->date;
    }

    public function setDate(\DateTime $date)
    {
        $this->date = $date;
    }
}