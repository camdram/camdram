<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ActsEvents
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
    private $endtime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="starttime", type="time", nullable=false)
     */
    private $starttime;

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
    private $linkid;

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
     * @return ActsEvents
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
     * Set endtime
     *
     * @param \DateTime $endtime
     * @return ActsEvents
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
     * Set starttime
     *
     * @param \DateTime $starttime
     * @return ActsEvents
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
     * Set date
     *
     * @param \DateTime $date
     * @return ActsEvents
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
     * @return ActsEvents
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
     * Set linkid
     *
     * @param integer $linkid
     * @return ActsEvents
     */
    public function setLinkid($linkid)
    {
        $this->linkid = $linkid;
    
        return $this;
    }

    /**
     * Get linkid
     *
     * @return integer 
     */
    public function getLinkid()
    {
        return $this->linkid;
    }

    /**
     * Set socid
     *
     * @param \Acts\CamdramBundle\Entity\ActsSocieties $socid
     * @return ActsEvents
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