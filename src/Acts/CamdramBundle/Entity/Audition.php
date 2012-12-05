<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ActsAuditions
 *
 * @ORM\Table(name="acts_auditions")
 * @ORM\Entity
 */
class Audition
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
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date", nullable=false)
     */
    private $date;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="starttime", type="time", nullable=false)
     */
    private $starttime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="endtime", type="time", nullable=false)
     */
    private $endtime;

    /**
     * @var string
     *
     * @ORM\Column(name="location", type="text", nullable=false)
     */
    private $location;

    /**
     * @var \ActsShows
     *
     * @ORM\ManyToOne(targetEntity="ActsShows")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="showid", referencedColumnName="id")
     * })
     */
    private $showid;

    /**
     * @var boolean
     *
     * @ORM\Column(name="display", type="boolean", nullable=false)
     */
    private $display;

    /**
     * @var boolean
     *
     * @ORM\Column(name="nonscheduled", type="boolean", nullable=false)
     */
    private $nonscheduled;



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
     * Set date
     *
     * @param \DateTime $date
     * @return ActsAuditions
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
     * Set starttime
     *
     * @param \DateTime $starttime
     * @return ActsAuditions
     */
    public function setStarttime($starttime)
    {
        $this->starttime = $starttime;
    
        return $this;
    }

    /**
     * Get starttime
     *
     * @return \DateTime 
     */
    public function getStarttime()
    {
        return $this->starttime;
    }

    /**
     * Set endtime
     *
     * @param \DateTime $endtime
     * @return ActsAuditions
     */
    public function setEndtime($endtime)
    {
        $this->endtime = $endtime;
    
        return $this;
    }

    /**
     * Get endtime
     *
     * @return \DateTime 
     */
    public function getEndtime()
    {
        return $this->endtime;
    }

    /**
     * Set location
     *
     * @param string $location
     * @return ActsAuditions
     */
    public function setLocation($location)
    {
        $this->location = $location;
    
        return $this;
    }

    /**
     * Get location
     *
     * @return string 
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set display
     *
     * @param boolean $display
     * @return ActsAuditions
     */
    public function setDisplay($display)
    {
        $this->display = $display;
    
        return $this;
    }

    /**
     * Get display
     *
     * @return boolean 
     */
    public function getDisplay()
    {
        return $this->display;
    }

    /**
     * Set nonscheduled
     *
     * @param boolean $nonscheduled
     * @return ActsAuditions
     */
    public function setNonscheduled($nonscheduled)
    {
        $this->nonscheduled = $nonscheduled;
    
        return $this;
    }

    /**
     * Get nonscheduled
     *
     * @return boolean 
     */
    public function getNonscheduled()
    {
        return $this->nonscheduled;
    }

    /**
     * Set showid
     *
     * @param \Acts\CamdramBundle\Entity\ActsShows $showid
     * @return ActsAuditions
     */
    public function setShowid(\Acts\CamdramBundle\Entity\ActsShows $showid = null)
    {
        $this->showid = $showid;
    
        return $this;
    }

    /**
     * Get showid
     *
     * @return \Acts\CamdramBundle\Entity\ActsShows 
     */
    public function getShowid()
    {
        return $this->showid;
    }
}