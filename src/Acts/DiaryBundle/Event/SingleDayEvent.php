<?php
namespace Acts\DiaryBundle\Event;

/**
 * Class SingleEvent
 *
 * A standard implementation of a single day event
 *
 * @package Acts\DiaryBundle\Event
 */
class SingleEvent extends AbstractEvent implements SingleEventInterface
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