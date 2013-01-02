<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TermDate
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
     * @ORM\Column(name="name", type="string", length=100, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="friendlyname", type="string", length=100, nullable=false)
     */
    private $friendly_name;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="startdate", type="date", nullable=false)
     */
    private $start_date;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="enddate", type="date", nullable=false)
     */
    private $end_date;

    /**
     * @var integer
     *
     * @ORM\Column(name="firstweek", type="integer", nullable=false)
     */
    private $first_week;

    /**
     * @var integer
     *
     * @ORM\Column(name="lastweek", type="integer", nullable=false)
     */
    private $last_week;

    /**
     * @var boolean
     *
     * @ORM\Column(name="displayweek", type="boolean", nullable=false)
     */
    private $display_week;

    /**
     * @var string
     *
     * @ORM\Column(name="vacation", type="string", length=100, nullable=false)
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
     * @return TermDate
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
     * Set friendly_name
     *
     * @param string $friendlyName
     * @return TermDate
     */
    public function setFriendlyName($friendlyName)
    {
        $this->friendly_name = $friendlyName;
    
        return $this;
    }

    /**
     * Get friendly_name
     *
     * @return string 
     */
    public function getFriendlyName()
    {
        return $this->friendly_name;
    }

    /**
     * Set start_date
     *
     * @param \DateTime $startDate
     * @return TermDate
     */
    public function setStartDate($startDate)
    {
        $this->start_date = $startDate;
    
        return $this;
    }

    /**
     * Get start_date
     *
     * @return \DateTime 
     */
    public function getStartDate()
    {
        return $this->start_date;
    }

    /**
     * Set end_date
     *
     * @param \DateTime $endDate
     * @return TermDate
     */
    public function setEndDate($endDate)
    {
        $this->end_date = $endDate;
    
        return $this;
    }

    /**
     * Get end_date
     *
     * @return \DateTime 
     */
    public function getEndDate()
    {
        return $this->end_date;
    }

    /**
     * Set first_week
     *
     * @param boolean $firstWeek
     * @return TermDate
     */
    public function setFirstWeek($firstWeek)
    {
        $this->first_week = $firstWeek;
    
        return $this;
    }

    /**
     * Get first_week
     *
     * @return boolean 
     */
    public function getFirstWeek()
    {
        return $this->first_week;
    }

    /**
     * Set last_week
     *
     * @param boolean $lastWeek
     * @return TermDate
     */
    public function setLastWeek($lastWeek)
    {
        $this->last_week = $lastWeek;
    
        return $this;
    }

    /**
     * Get last_week
     *
     * @return boolean 
     */
    public function getLastWeek()
    {
        return $this->last_week;
    }

    /**
     * Set display_week
     *
     * @param boolean $displayWeek
     * @return TermDate
     */
    public function setDisplayWeek($displayWeek)
    {
        $this->display_week = $displayWeek;
    
        return $this;
    }

    /**
     * Get display_week
     *
     * @return boolean 
     */
    public function getDisplayWeek()
    {
        return $this->display_week;
    }

    /**
     * Set vacation
     *
     * @param string $vacation
     * @return TermDate
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