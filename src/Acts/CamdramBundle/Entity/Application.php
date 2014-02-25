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
     * @var \Show
     *
     * @ORM\ManyToOne(targetEntity="Show", inversedBy="applications")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="showid", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     * })
     */
    private $show;

    /**
     * @var \Society
     *
     * @ORM\ManyToOne(targetEntity="Society")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="socid", referencedColumnName="id", nullable=true, onDelete="CASCADE")
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
    private $deadlineDate;

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
    private $deadlineTime;


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
        $this->deadlineDate = $deadlineDate;
    
        return $this;
    }

    /**
     * Get deadline_date
     *
     * @return \DateTime 
     */
    public function getDeadlineDate()
    {
        return $this->deadlineDate;
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
     * Set deadlineTime
     *
     * @param \DateTime $deadlineTime
     * @return Application
     */
    public function setDeadlineTime($deadlineTime)
    {
        $this->deadlineTime = $deadlineTime;
    
        return $this;
    }

    /**
     * Get deadlineTime
     *
     * @return \DateTime 
     */
    public function getDeadlineTime()
    {
        return $this->deadlineTime;
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
