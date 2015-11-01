<?php

namespace Acts\DiaryBundle\Event;

/**
 * Class MultiDayEventInterface
 */
interface MultiDayEventInterface extends EventInterface
{
    /**
     * A single date within the range on which the event does not take place
     *
     * @return \DateTime|null
     */
    public function getExcludeDate();

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
