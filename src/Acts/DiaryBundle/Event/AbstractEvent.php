<?php

namespace Acts\DiaryBundle\Event;

/**
 * Class AbstractEvent
 *
 * A standard implementation of an event
 */
abstract class AbstractEvent implements EventInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $venue;

    /**
     * @var \DateTime
     */
    private $start_time;

    /**
     * @var \DateTime
     */
    private $end_time;

    /**
     * @var string|null
     */
    private $link;

    /**
     * @var string|null
     */
    private $venue_link;

    /**
     * @var \DateTime
     */
    private $updated_at;

    /**
     * @var string|null
     */
    private $uid;

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
     * {@inheritdoc}
     */
    public function getVenue()
    {
        return $this->venue;
    }

    /**
     * {@inheritdoc}
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Set the URL reached by clicking on the event's name
     *
     * @param string|null $link
     */
    public function setLink($link)
    {
        $this->link = $link;
    }

    /**
     * Set the venue name
     *
     * @param string $venue
     */
    public function setVenue($venue)
    {
        $this->venue = $venue;
    }

    /**
     * {@inheritdoc}
     */
    public function getVenueLink()
    {
        return $this->venue_link;
    }

    /**
     * Set the URL reached by clicking on the venue name
     *
     * @param string|null $link
     */
    public function setVenueLink($link)
    {
        $this->venue_link = $link;
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
     * @param null|string $uid
     */
    public function setUid($uid)
    {
        $this->uid = $uid;
    }

    /**
     * @return null|string
     */
    public function getUid()
    {
        return $this->uid;
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
