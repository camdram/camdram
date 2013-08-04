<?php
namespace Acts\DiaryBundle\Diary;

use Acts\DiaryBundle\Event\EventInterface;

/**
 * Class DiaryItem
 *
 * A single item in the diary. Loosely corresponds to an event, but an event may have multiple correponding DiaryItem
 * objects, e.g. if it takes place across multiple weeks or is a single event that occurs across midnight. Represents
 * a single 'block' in the diary.
 *
 * @package Acts\DiaryBundle\Diary
 */
class DiaryItem
{

    /**
     * @var \DateTime The start time of this diary item
     */
    private $start_at;

    /**
     * @var \DateTime The end time of this diary item
     */
    private $end_at;

    /**
     * @var \Acts\DiaryBundle\Event\EventInterface The event associated with this diary item
     */
    private $event;

    /**
     * @var int The number of days this event lasts
     */
    private $num_days;

    /**
     * @var int The column in which this event starts (0-based)
     */
    private $index;

    /**
     * @return EventInterface
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @param EventInterface $event
     */
    public function setEvent(EventInterface $event)
    {
        $this->event = $event;
    }

    /**
     * @return \DateTime
     */
    public function getStartAt()
    {
        return $this->start_at;
    }

    /**
     * @param \DateTime $start_at
     */
    public function setStartAt(\DateTime $start_at)
    {
        $this->start_at = $start_at;
    }

    /**
     * @return \DateTime
     */
    public function getEndAt()
    {
        return $this->end_at;
    }

    /**
     * @param \DateTime $end_at
     */
    public function setEndAt(\DateTime $end_at)
    {
        $this->start_at = $end_at;
    }

    /**
     * @return int
     */
    public function getNumberOfDays()
    {
        return $this->num_days;
    }

    /**
     * @param int $days
     */
    public function setNumberOfDays($days)
    {
        $this->num_days = $days;
    }

    /**
     * @return int
     */
    public function getStartIndex()
    {
        return $this->index;
    }

    /**
     * @return int
     */
    public function getEndIndex()
    {
        return $this->index + $this->num_days - 1;
    }

    /**
     * @param $index
     */
    public function setIndex($index)
    {
        $this->index = $index;
    }

}