<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TimePeriodGroup
 *
 * @ORM\Table(name="acts_time_period_groups")
 * @ORM\Entity
 */
class TimePeriodGroup
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
     * @ORM\OneToMany(targetEntity="TimePeriod", mappedBy="group")
     */
    private $periods;

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
     * @return TimePeriodGroup
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
     * @return TimePeriodGroup
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
     * Constructor
     */
    public function __construct()
    {
        $this->periods = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add periods
     *
     * @param \Acts\CamdramBundle\Entity\TimePeriod $periods
     * @return TimePeriodGroup
     */
    public function addPeriod(\Acts\CamdramBundle\Entity\TimePeriod $periods)
    {
        $this->periods[] = $periods;
    
        return $this;
    }

    /**
     * Remove periods
     *
     * @param \Acts\CamdramBundle\Entity\TimePeriod $periods
     */
    public function removePeriod(\Acts\CamdramBundle\Entity\TimePeriod $periods)
    {
        $this->periods->removeElement($periods);
    }

    /**
     * Get periods
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPeriods()
    {
        return $this->periods;
    }

    /**
     * Set start_at
     *
     * @param \DateTime $startAt
     * @return TimePeriodGroup
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
     * @return TimePeriodGroup
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
}