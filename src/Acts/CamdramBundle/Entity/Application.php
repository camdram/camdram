<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Application
 *
 * @ORM\Table(name="acts_applications")
 * @ORM\Entity(repositoryClass="Acts\CamdramBundle\Entity\ApplicationRepository")
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
     * @var integer
     *
     * @ORM\Column(name="showid", type="integer", nullable=true)
     */
    private $show_id;
    
    /**
     * @var \Show
     *
     * @ORM\ManyToOne(targetEntity="Show")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="showid", referencedColumnName="id")
     * })
     */
    private $show;

    /**
     * @var integer
     *
     * @ORM\Column(name="socid", type="integer", nullable=true)
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
    private $deadline_date;

    /**
     * @var string
     *
     * @ORM\Column(name="furtherinfo", type="text", nullable=false)
     */
    private $further_info;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deadlinetime", type="time", nullable=false)
     */
    private $deadline_time;


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
     * Set show_id
     *
     * @param integer $showId
     * @return Application
     */
    public function setShowId($showId)
    {
        $this->show_id = $showId;
    
        return $this;
    }

    /**
     * Get show_id
     *
     * @return integer 
     */
    public function getShowId()
    {
        return $this->show_id;
    }

    /**
     * Set society_id
     *
     * @param integer $societyId
     * @return Application
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
     * Set text
     *
     * @param string $text
     * @return Application
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
     * Set deadline_date
     *
     * @param \DateTime $deadlineDate
     * @return Application
     */
    public function setDeadlineDate($deadlineDate)
    {
        $this->deadline_date = $deadlineDate;
    
        return $this;
    }

    /**
     * Get deadline_date
     *
     * @return \DateTime 
     */
    public function getDeadlineDate()
    {
        return $this->deadline_date;
    }

    /**
     * Set further_info
     *
     * @param string $furtherInfo
     * @return Application
     */
    public function setFurtherInfo($furtherInfo)
    {
        $this->further_info = $furtherInfo;
    
        return $this;
    }

    /**
     * Get further_info
     *
     * @return string 
     */
    public function getFurtherInfo()
    {
        return $this->further_info;
    }

    /**
     * Set deadline_time
     *
     * @param \DateTime $deadlineTime
     * @return Application
     */
    public function setDeadlineTime($deadlineTime)
    {
        $this->deadline_time = $deadlineTime;
    
        return $this;
    }

    /**
     * Get deadline_time
     *
     * @return \DateTime 
     */
    public function getDeadlineTime()
    {
        return $this->deadline_time;
    }

    /**
     * Set show
     *
     * @param \Acts\CamdramBundle\Entity\Show $show
     * @return Application
     */
    public function setShow(\Acts\CamdramBundle\Entity\Show $show = null)
    {
        $this->show = $show;
    
        return $this;
    }

    /**
     * Get show
     *
     * @return \Acts\CamdramBundle\Entity\Show 
     */
    public function getShow()
    {
        return $this->show;
    }

    /**
     * Set society
     *
     * @param \Acts\CamdramBundle\Entity\Society $society
     * @return Application
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

