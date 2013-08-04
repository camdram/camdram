<?php
namespace Acts\DiaryBundle\Event;

interface SingleDayEventInterface extends EventInterface
{
    /**
     * The date on which the event takes place
     *
     * @return \DateTime
     */
    public function getDate();
}