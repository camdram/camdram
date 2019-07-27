<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Event
 *
 * @ORM\Table(name="acts_events")
 * @ORM\Entity
 */
class Event
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="string", length=255, nullable=false)
     */
    private $title;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="endtime", type="time", nullable=false)
     */
    private $end_time;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="starttime", type="time", nullable=false)
     */
    private $start_time;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date", nullable=false)
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=false)
     */
    private $description;

    /**
     * @var int
     *
     * @ORM\Column(name="linkid", type="integer", nullable=false)
     */
    private $link_id;

    /**
     * @var \Society
     *
     * @ORM\ManyToOne(targetEntity="Society")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="society_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     * })
     */
    private $society;

    /**
     * @var \Venue
     *
     * @ORM\ManyToOne(targetEntity="Venue")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="venue_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     * })
     */
    private $venue;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set end_time
     *
     * @param \DateTime $endTime
     *
     * @return Event
     */
    public function setEndTime($endTime)
    {
        $this->end_time = $endTime;

        return $this;
    }

    /**
     * Get end_time
     *
     * @return \DateTime
     */
    public function getEndTime()
    {
        return $this->end_time;
    }

    /**
     * Set start_time
     *
     * @param \DateTime $startTime
     *
     * @return Event
     */
    public function setStartTime($startTime)
    {
        $this->start_time = $startTime;

        return $this;
    }

    /**
     * Get start_time
     *
     * @return \DateTime
     */
    public function getStartTime()
    {
        return $this->start_time;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Event
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Event
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set link_id
     *
     * @param int $linkId
     *
     * @return Event
     */
    public function setLinkId($linkId)
    {
        $this->link_id = $linkId;

        return $this;
    }

    /**
     * Get link_id
     *
     * @return int
     */
    public function getLinkId()
    {
        return $this->link_id;
    }

    /**
     * Set society
     *
     * @param \Acts\CamdramBundle\Entity\Society $society
     *
     * @return Event
     */
    public function setSociety(\Acts\CamdramBundle\Entity\Society $society = null)
    {
        $this->society = $society;

        return $this;
    }

    /**
     * Get society
     *
     * @return \Acts\CamdramBundle\Entity\Society
     */
    public function getSociety()
    {
        return $this->society;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Event
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }
}
