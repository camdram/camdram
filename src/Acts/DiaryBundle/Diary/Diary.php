<?php
namespace Acts\DiaryBundle\Diary;

use Acts\DiaryBundle\Diary\EventInterface;

class Diary
{
    private $weeks;

    public function addEvent(EventInterface $event)
    {
        //Create a new week if necessary
        //Add event to appropriate week
    }

    public function getWeeks()
    {
        return $this->weeks;
    }
}