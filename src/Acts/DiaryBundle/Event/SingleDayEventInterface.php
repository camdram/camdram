<?php
namespace Acts\DiaryBundle\Event;

interface SingleDayEventInterface extends EventInterface
{
    /**
     * @return \DateTime
     */
    public function getDate();
}