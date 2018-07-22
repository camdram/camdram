<?php

namespace Acts\DiaryBundle\Event;

/**
 * Class MultiDayEventInterface
 */
interface MultiDayEventInterface extends EventInterface
{
    /**
     * The first date on which the event takes place
     *
     * @return \DateTime
     */
    public function getStartDate();

    /**
     * The last date on which the event takes place
     *
     * @return \DateTime
     */
    public function getEndDate();
}
