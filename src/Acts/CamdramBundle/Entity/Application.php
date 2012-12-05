<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ActsApplications
 *
 * @ORM\Table(name="acts_applications")
 * @ORM\Entity
 */
class Application
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
     * @var \ActsSocieties
     *
     * @ORM\ManyToOne(targetEntity="ActsSocieties")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="socid", referencedColumnName="id")
     * })
     */
    private $socid;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="text", nullable=false)
     */
    private $text;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deadlinedate", type="date", nullable=false)
     */
    private $deadlinedate;

    /**
     * @var string
     *
     * @ORM\Column(name="furtherinfo", type="text", nullable=false)
     */
    private $furtherinfo;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deadlinetime", type="time", nullable=false)
     */
    private $deadlinetime;



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
     * @return ActsApplications
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
     * Set deadlinedate
     *
     * @param \DateTime $deadlinedate
     * @return ActsApplications
     */
    public function setDeadlinedate($deadlinedate)
    {
        $this->deadlinedate = $deadlinedate;
    
        return $this;
    }

    /**
     * Get deadlinedate
     *
     * @return \DateTime 
     */
    public function getDeadlinedate()
    {
        return $this->deadlinedate;
    }

    /**
     * Set furtherinfo
     *
     * @param string $furtherinfo
     * @return ActsApplications
     */
    public function setFurtherinfo($furtherinfo)
    {
        $this->furtherinfo = $furtherinfo;
    
        return $this;
    }

    /**
     * Get furtherinfo
     *
     * @return string 
     */
    public function getFurtherinfo()
    {
        return $this->furtherinfo;
    }

    /**
     * Set deadlinetime
     *
     * @param \DateTime $deadlinetime
     * @return ActsApplications
     */
    public function setDeadlinetime($deadlinetime)
    {
        $this->deadlinetime = $deadlinetime;
    
        return $this;
    }

    /**
     * Get deadlinetime
     *
     * @return \DateTime 
     */
    public function getDeadlinetime()
    {
        return $this->deadlinetime;
    }

    /**
     * Set showid
     *
     * @param \Acts\CamdramBundle\Entity\ActsShows $showid
     * @return ActsApplications
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

    /**
     * Set socid
     *
     * @param \Acts\CamdramBundle\Entity\ActsSocieties $socid
     * @return ActsApplications
     */
    public function setSocid(\Acts\CamdramBundle\Entity\ActsSocieties $socid = null)
    {
        $this->socid = $socid;
    
        return $this;
    }

    /**
     * Get socid
     *
     * @return \Acts\CamdramBundle\Entity\ActsSocieties 
     */
    public function getSocid()
    {
        return $this->socid;
    }
}