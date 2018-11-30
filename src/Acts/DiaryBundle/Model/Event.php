<?php

namespace Acts\DiaryBundle\Model;

/**
 * Class Event
 *
 * A standard implementation of an event
 */
class Event implements EventInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var VenueInterface\null
     */
    private $venue;

    /**
     * @var string
     */
    private $venue_name;

    /**
     * @var \DateTime
     */
    private $start_date;

    /**
     * @var \DateTime
     */
    private $end_date;

    /**
     * @var \DateTime
     */
    private $start_time;

    /**
     * @var \DateTime
     */
    private $end_time;

    /**
     * @var \DateTime
     */
    private $updated_at;

    /**
     * @var int
     */
    private $id;

    /**
     * @var string|null
     */
    private $description;

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the event's name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Set the event's venue
     */
    public function setVenue(VenueInterface $venue = null)
    {
        $this->venue = $venue;
        return $this;
    }

    /**
     * Set the event's venue
     */
    public function getVenue()
    {
        return $this->venue;
    }

    /**
     * Set the venue name
     *
     * @param string $venue
     */
    public function setVenueName($venueName)
    {
        $this->venue_name = $venueName;
        return $this;
    }

    /**
     * Get the venue name
     *
     * @param string $venue
     */
    public function getVenueName()
    {
        return $this->venue_name;
    }

    /**
     * Convenience method to set start and end dates to the same value
     */
    public function setDate(\DateTime $date)
    {
        $this->setStartDate($date);
        $this->setEndDate($date);
        return $this;
    }

    public function getStartDate()
    {
        return $this->start_date;
    }

    /**
     * Set the first date on which the event takes place
     *
     * @param \DateTime $start_date
     */
    public function setStartDate(\DateTime $start_date)
    {
        $this->start_date = $start_date;
    }

    /**
     * {@inheritdoc}
     */
    public function getEndDate()
    {
        return $this->end_date;
    }

    /**
     * Set the last date on which the event takes place
     *
     * @param \DateTime $end_date
     */
    public function setEndDate(\DateTime $end_date)
    {
        $this->end_date = $end_date;
    }

    /**
     * {@inheritdoc}
     */
    public function getStartTime()
    {
        return $this->start_time;
    }

    /**
     * Set the start time of the event
     *
     * @param \DateTime $start_time
     */
    public function setStartTime(\DateTime $start_time)
    {
        $this->start_time = $start_time;
    }

    /**
     * {@inheritdoc}
     */
    public function getEndTime()
    {
        return $this->end_time;
    }

    /**
     * Set the end time of the event
     *
     * @param \DateTime $end_time
     */
    public function setEndTime(\DateTime $end_time)
    {
        $this->end_time = $end_time;
    }

    /**
     * @param int $uid
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return null|string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param \DateTime $updated_at
     */
    public function setUpdatedAt($updated_at)
    {
        $this->updated_at = $updated_at;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * @param null|string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return null|string
     */
    public function getDescription()
    {
        return $this->description;
    }
}