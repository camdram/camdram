<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ActsTechies
 *
 * @ORM\Table(name="acts_techies")
 * @ORM\Entity
 */
class Techie
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
     *   @ORM\JoinColumn(name="showid", referencedColumnName="id")
     * })
     */
    private $showid;

    /**
     * @var string
     *
     * @ORM\Column(name="positions", type="text", nullable=false)
     */
    private $positions;

    /**
     * @var string
     *
     * @ORM\Column(name="contact", type="text", nullable=false)
     */
    private $contact;

    /**
     * @var boolean
     *
     * @ORM\Column(name="deadline", type="boolean", nullable=false)
     */
    private $deadline;

    /**
     * @var string
     *
     * @ORM\Column(name="deadlinetime", type="text", nullable=false)
     */
    private $deadlinetime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="expiry", type="date", nullable=false)
     */
    private $expiry;

    /**
     * @var boolean
     *
     * @ORM\Column(name="display", type="boolean", nullable=false)
     */
    private $display;

    /**
     * @var boolean
     *
     * @ORM\Column(name="remindersent", type="boolean", nullable=false)
     */
    private $remindersent;

    /**
     * @var string
     *
     * @ORM\Column(name="techextra", type="text", nullable=false)
     */
    private $techextra;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lastupdated", type="datetime", nullable=false)
     */
    private $lastupdated;



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
     * Set positions
     *
     * @param string $positions
     * @return ActsTechies
     */
    public function setPositions($positions)
    {
        $this->positions = $positions;
    
        return $this;
    }

    /**
     * Get positions
     *
     * @return string 
     */
    public function getPositions()
    {
        return $this->positions;
    }

    /**
     * Set contact
     *
     * @param string $contact
     * @return ActsTechies
     */
    public function setContact($contact)
    {
        $this->contact = $contact;
    
        return $this;
    }

    /**
     * Get contact
     *
     * @return string 
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * Set deadline
     *
     * @param boolean $deadline
     * @return ActsTechies
     */
    public function setDeadline($deadline)
    {
        $this->deadline = $deadline;
    
        return $this;
    }

    /**
     * Get deadline
     *
     * @return boolean 
     */
    public function getDeadline()
    {
        return $this->deadline;
    }

    /**
     * Set deadlinetime
     *
     * @param string $deadlinetime
     * @return ActsTechies
     */
    public function setDeadlinetime($deadlinetime)
    {
        $this->deadlinetime = $deadlinetime;
    
        return $this;
    }

    /**
     * Get deadlinetime
     *
     * @return string 
     */
    public function getDeadlinetime()
    {
        return $this->deadlinetime;
    }

    /**
     * Set expiry
     *
     * @param \DateTime $expiry
     * @return ActsTechies
     */
    public function setExpiry($expiry)
    {
        $this->expiry = $expiry;
    
        return $this;
    }

    /**
     * Get expiry
     *
     * @return \DateTime 
     */
    public function getExpiry()
    {
        return $this->expiry;
    }

    /**
     * Set display
     *
     * @param boolean $display
     * @return ActsTechies
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
     * Set remindersent
     *
     * @param boolean $remindersent
     * @return ActsTechies
     */
    public function setRemindersent($remindersent)
    {
        $this->remindersent = $remindersent;
    
        return $this;
    }

    /**
     * Get remindersent
     *
     * @return boolean 
     */
    public function getRemindersent()
    {
        return $this->remindersent;
    }

    /**
     * Set techextra
     *
     * @param string $techextra
     * @return ActsTechies
     */
    public function setTechextra($techextra)
    {
        $this->techextra = $techextra;
    
        return $this;
    }

    /**
     * Get techextra
     *
     * @return string 
     */
    public function getTechextra()
    {
        return $this->techextra;
    }

    /**
     * Set lastupdated
     *
     * @param \DateTime $lastupdated
     * @return ActsTechies
     */
    public function setLastupdated($lastupdated)
    {
        $this->lastupdated = $lastupdated;
    
        return $this;
    }

    /**
     * Get lastupdated
     *
     * @return \DateTime 
     */
    public function getLastupdated()
    {
        return $this->lastupdated;
    }

    /**
     * Set showid
     *
     * @param \Acts\CamdramBundle\Entity\ActsShows $showid
     * @return ActsTechies
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