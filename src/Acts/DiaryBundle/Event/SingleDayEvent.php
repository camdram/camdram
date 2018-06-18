<?php

namespace Acts\DiaryBundle\Event;

/**
 * Class SingleEvent
 *
 * A standard implementation of a single day event
 */
class SingleDayEvent extends AbstractEvent implements SingleDayEventInterface
{
    /**
     * @var \DateTime
     */
    private $date;

    /**
     * {@inheritdoc}
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set the date on which the event takes place
     *
     * @param \DateTime $date
     */
    public function setDate(\DateTime $date)
    {
        $this->date = $date;
    }
}
