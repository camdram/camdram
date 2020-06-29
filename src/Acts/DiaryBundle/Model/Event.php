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
     * @var VenueInterface|null
     */
    private $venue;

    /**
     * @var string
     */
    private $venue_name;

    /**
     * @var \DateTime
     */
    private $start_at;

    /**
     * @var \DateTime
     */
    private $end_at;

    /**
     * @var \DateTime
     */
    private $repeat_until;

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

    public function setVenueName(?string $venueName): self
    {
        $this->venue_name = $venueName;
        return $this;
    }

    public function getVenueName(): ?string
    {
        return $this->venue_name;
    }

    public function getStartAt()
    {
        return $this->start_at;
    }

    /**
     * Set the first date on which the event takes place
     *
     * @param \DateTime $startAt
     */
    public function setStartAt(\DateTime $startAt)
    {
        $this->start_at = $startAt;
    }

    /**
     * {@inheritdoc}
     */
    public function getEndAt()
    {
        return $this->end_at;
    }

    /**
     * Set the last date on which the event takes place
     *
     * @param \DateTime $endAt
     */
    public function setEndAt(\DateTime $endAt)
    {
        $this->end_at = $endAt;
    }

    /**
     * {@inheritdoc}
     */
    public function getRepeatUntil()
    {
        return $this->repeat_until;
    }

    /**
     * Set the end time of the event
     *
     * @param \DateTime $repeatUntil
     */
    public function setRepeatUntil(\DateTime $repeatUntil)
    {
        $this->repeat_until = $repeatUntil;
    }

    public function setId(?int $id)
    {
        $this->id = $id;
    }

    public function getId(): ?int
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
