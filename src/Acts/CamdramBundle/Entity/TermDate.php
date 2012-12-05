<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ActsTermdates
 *
 * @ORM\Table(name="acts_termdates")
 * @ORM\Entity
 */
class TermDate
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
     * @ORM\Column(name="name", type="text", nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="friendlyname", type="text", nullable=false)
     */
    private $friendlyname;

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
     * @var boolean
     *
     * @ORM\Column(name="firstweek", type="boolean", nullable=false)
     */
    private $firstweek;

    /**
     * @var boolean
     *
     * @ORM\Column(name="lastweek", type="boolean", nullable=false)
     */
    private $lastweek;

    /**
     * @var boolean
     *
     * @ORM\Column(name="displayweek", type="boolean", nullable=false)
     */
    private $displayweek;

    /**
     * @var string
     *
     * @ORM\Column(name="vacation", type="text", nullable=false)
     */
    private $vacation;



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
     * Set name
     *
     * @param string $name
     * @return ActsTermdates
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
     * Set friendlyname
     *
     * @param string $friendlyname
     * @return ActsTermdates
     */
    public function setFriendlyname($friendlyname)
    {
        $this->friendlyname = $friendlyname;
    
        return $this;
    }

    /**
     * Get friendlyname
     *
     * @return string 
     */
    public function getFriendlyname()
    {
        return $this->friendlyname;
    }

    /**
     * Set startdate
     *
     * @param \DateTime $startdate
     * @return ActsTermdates
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
     * @return ActsTermdates
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
     * Set firstweek
     *
     * @param boolean $firstweek
     * @return ActsTermdates
     */
    public function setFirstweek($firstweek)
    {
        $this->firstweek = $firstweek;
    
        return $this;
    }

    /**
     * Get firstweek
     *
     * @return boolean 
     */
    public function getFirstweek()
    {
        return $this->firstweek;
    }

    /**
     * Set lastweek
     *
     * @param boolean $lastweek
     * @return ActsTermdates
     */
    public function setLastweek($lastweek)
    {
        $this->lastweek = $lastweek;
    
        return $this;
    }

    /**
     * Get lastweek
     *
     * @return boolean 
     */
    public function getLastweek()
    {
        return $this->lastweek;
    }

    /**
     * Set displayweek
     *
     * @param boolean $displayweek
     * @return ActsTermdates
     */
    public function setDisplayweek($displayweek)
    {
        $this->displayweek = $displayweek;
    
        return $this;
    }

    /**
     * Get displayweek
     *
     * @return boolean 
     */
    public function getDisplayweek()
    {
        return $this->displayweek;
    }

    /**
     * Set vacation
     *
     * @param string $vacation
     * @return ActsTermdates
     */
    public function setVacation($vacation)
    {
        $this->vacation = $vacation;
    
        return $this;
    }

    /**
     * Get vacation
     *
     * @return string 
     */
    public function getVacation()
    {
        return $this->vacation;
    }
}