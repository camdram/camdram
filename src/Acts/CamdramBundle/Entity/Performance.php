<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ActsPerformances
 *
 * @ORM\Table(name="acts_performances")
 * @ORM\Entity
 */
class Performance
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
     * @var \ActsShows
     *
     * @ORM\ManyToOne(targetEntity="ActsShows")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sid", referencedColumnName="id")
     * })
     */
    private $sid;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="startdate", type="date", nullable=false)
     */
    private $startdate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="enddate", type="date", nullable=false)
     */
    private $enddate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="excludedate", type="date", nullable=false)
     */
    private $excludedate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="time", type="time", nullable=false)
     */
    private $time;

    /**
     * @var \ActsSocieties
     *
     * @ORM\ManyToOne(targetEntity="ActsSocieties")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="venid", referencedColumnName="id")
     * })
     */
    private $venid;

    /**
     * @var string
     *
     * @ORM\Column(name="venue", type="text", nullable=false)
     */
    private $venue;



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
     * Set startdate
     *
     * @param \DateTime $startdate
     * @return ActsPerformances
     */
    public function setStartdate($startdate)
    {
        $this->startdate = $startdate;
    
        return $this;
    }

    /**
     * Get startdate
     *
     * @return \DateTime 
     */
    public function getStartdate()
    {
        return $this->startdate;
    }

    /**
     * Set enddate
     *
     * @param \DateTime $enddate
     * @return ActsPerformances
     */
    public function setEnddate($enddate)
    {
        $this->enddate = $enddate;
    
        return $this;
    }

    /**
     * Get enddate
     *
     * @return \DateTime 
     */
    public function getEnddate()
    {
        return $this->enddate;
    }

    /**
     * Set excludedate
     *
     * @param \DateTime $excludedate
     * @return ActsPerformances
     */
    public function setExcludedate($excludedate)
    {
        $this->excludedate = $excludedate;
    
        return $this;
    }

    /**
     * Get excludedate
     *
     * @return \DateTime 
     */
    public function getExcludedate()
    {
        return $this->excludedate;
    }

    /**
     * Set time
     *
     * @param \DateTime $time
     * @return ActsPerformances
     */
    public function setTime($time)
    {
        $this->time = $time;
    
        return $this;
    }

    /**
     * Get time
     *
     * @return \DateTime 
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * Set venue
     *
     * @param string $venue
     * @return ActsPerformances
     */
    public function setVenue($venue)
    {
        $this->venue = $venue;
    
        return $this;
    }

    /**
     * Get venue
     *
     * @return string 
     */
    public function getVenue()
    {
        return $this->venue;
    }

    /**
     * Set sid
     *
     * @param \Acts\CamdramBundle\Entity\ActsShows $sid
     * @return ActsPerformances
     */
    public function setSid(\Acts\CamdramBundle\Entity\ActsShows $sid = null)
    {
        $this->sid = $sid;
    
        return $this;
    }

    /**
     * Get sid
     *
     * @return \Acts\CamdramBundle\Entity\ActsShows 
     */
    public function getSid()
    {
        return $this->sid;
    }

    /**
     * Set venid
     *
     * @param \Acts\CamdramBundle\Entity\ActsSocieties $venid
     * @return ActsPerformances
     */
    public function setVenid(\Acts\CamdramBundle\Entity\ActsSocieties $venid = null)
    {
        $this->venid = $venid;
    
        return $this;
    }

    /**
     * Get venid
     *
     * @return \Acts\CamdramBundle\Entity\ActsSocieties 
     */
    public function getVenid()
    {
        return $this->venid;
    }
}