<?php
namespace Acts\DiaryBundle\Diary;

use Acts\DiaryBundle\Diary\EventInterface;

class Week
{
    private $events;

    public function addEvent(EventInterface $event)
    {
        $this->events[] = $event;
    }
}