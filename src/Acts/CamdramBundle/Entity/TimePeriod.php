<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TimePeriod
 *
 * @ORM\Table(name="acts_time_periods")
 * @ORM\Entity(repositoryClass="Acts\CamdramBundle\Entity\TimePeriodRepository")
 */
class TimePeriod
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="short_name", type="string", length=255)
     */
    private $short_name;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="long_name", type="string", length=255)
     */
    private $long_name;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_at", type="datetime")
     */
    private $start_at;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end_at", type="datetime")
     */
    private $end_at;

    /**
     * @var boolean
     *
     * @ORM\Column(name="holiday", type="boolean")
     */
    private $holiday = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="visible", type="boolean")
     */
    private $visible = true;

    /**
     * @var TimePeriodGroup
     * @ORM\ManyToOne(targetEntity="TimePeriodGroup", inversedBy="periods")
     */
    private $group;

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
     * Set short_name
     *
     * @param string $shortName
     * @return TimePeriod
     */
    public function setShortName($shortName)
    {
        $this->short_name = $shortName;
    
        return $this;
    }

    /**
     * Get short_name
     *
     * @return string 
     */
    public function getShortName()
    {
        return $this->short_name;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return TimePeriod
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set long_name
     *
     * @param string $longName
     * @return TimePeriod
     */
    public function setLongName($longName)
    {
        $this->long_name = $longName;
    
        return $this;
    }

    /**
     * Get long_name
     *
     * @return string 
     */
    public function getLongName()
    {
        return $this->long_name;
    }

    /**
     * Set start_at
     *
     * @param \DateTime $startAt
     * @return TimePeriod
     */
    public function setStartAt($startAt)
    {
        $this->start_at = $startAt;
    
        return $this;
    }

    /**
     * Get start_at
     *
     * @return \DateTime 
     */
    public function getStartAt()
    {
        return $this->start_at;
    }

    /**
     * Set end_at
     *
     * @param \DateTime $endAt
     * @return TimePeriod
     */
    public function setEndAt($endAt)
    {
        $this->end_at = $endAt;
    
        return $this;
    }

    /**
     * Get end_at
     *
     * @return \DateTime 
     */
    public function getEndAt()
    {
        return $this->end_at;
    }

    /**
     * Set holiday
     *
     * @param boolean $holiday
     * @return TimePeriod
     */
    public function setHoliday($holiday)
    {
        $this->holiday = $holiday;
    
        return $this;
    }

    /**
     * Get holiday
     *
     * @return boolean 
     */
    public function getHoliday()
    {
        return $this->holiday;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->shows = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Set visible
     *
     * @param boolean $visible
     * @return TimePeriod
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;
    
        return $this;
    }

    /**
     * Get visible
     *
     * @return boolean 
     */
    public function getVisible()
    {
        return $this->visible;
    }

    /**
     * Add shows
     *
     * @param \Acts\CamdramBundle\Entity\Show $shows
     * @return TimePeriod
     */
    public function addShow(\Acts\CamdramBundle\Entity\Show $shows)
    {
        $this->shows[] = $shows;
    
        return $this;
    }

    /**
     * Remove shows
     *
     * @param \Acts\CamdramBundle\Entity\Show $shows
     */
    public function removeShow(\Acts\CamdramBundle\Entity\Show $shows)
    {
        $this->shows->removeElement($shows);
    }

    /**
     * Get shows
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getShows()
    {
        return $this->shows;
    }

    /**
     * Set group
     *
     * @param \Acts\CamdramBundle\Entity\TimePeriodGroup $group
     * @return TimePeriod
     */
    public function setGroup(\Acts\CamdramBundle\Entity\TimePeriodGroup $group = null)
    {
        $this->group = $group;
    
        return $this;
    }

    /**
     * Get group
     *
     * @return \Acts\CamdramBundle\Entity\TimePeriodGroup 
     */
    public function getGroup()
    {
        return $this->group;
    }

}