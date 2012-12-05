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
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="text", nullable=false)
     */
    private $text;

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
     * @var integer
     *
     * @ORM\Column(name="linkid", type="integer", nullable=false)
     */
    private $link_id;

    /**
     * @var integer
     *
     * @ORM\Column(name="socid", type="integer", nullable=false)
     */
    private $society_id;

    /**
     * @var \Society
     *
     * @ORM\ManyToOne(targetEntity="Society")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="socid", referencedColumnName="id")
     * })
     */
    private $society;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set text
     *
     * @param string $text
     * @return Event
     */
    public function setText($text)
    {
        $this->text = $text;
    
        return $this;
    }

    /**
     * Get text
     *
     * @return string 
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set end_time
     *
     * @param \DateTime $endTime
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
     * @param integer $linkId
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
     * @return integer 
     */
    public function getLinkId()
    {
        return $this->link_id;
    }

    /**
     * Set society_id
     *
     * @param integer $societyId
     * @return Event
     */
    public function setSocietyId($societyId)
    {
        $this->society_id = $societyId;
    
        return $this;
    }

    /**
     * Get society_id
     *
     * @return integer 
     */
    public function getSocietyId()
    {
        return $this->society_id;
    }

    /**
     * Set society
     *
     * @param \Acts\CamdramBundle\Entity\Society $society
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
}