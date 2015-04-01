<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Acts\CamdramBundle\Validator\Constraints\TechieAdvertExpiry;
use Acts\CamdramApiBundle\Configuration\Annotation as Api;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as Serializer;

/**
 * TechieAdvert
 *
 * @ORM\Table(name="acts_techies")
 * @ORM\Entity(repositoryClass="Acts\CamdramBundle\Entity\TechieAdvertRepository")
 * @ORM\EntityListeners({"Acts\CamdramBundle\EventListener\TechieAdvertListener" })
 * @UniqueEntity("show")
 * @Api\Feed(name="Camdram.net - Production Team Vacancies", titleField="feed_title",
 *     description="Production Team Vacancies advertised for shows in Cambridge",
 *     template="ActsCamdramBundle:TechieAdvert:rss.html.twig")
 * @TechieAdvertExpiry()
 * @Gedmo\Loggable
 * @Serializer\ExclusionPolicy("all")
 * @Api\Link(route="get_techie", params={"identifier": "object.getShow().getSlug()"})
 * @Serializer\XmlRoot("techieadvert")
 */
class TechieAdvert
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Serializer\Expose
     * @Serializer\XmlAttribute
     */
    private $id;

    /**
     * @var \Show
     *
     * @ORM\ManyToOne(targetEntity="Show", inversedBy="techie_adverts")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="showid", referencedColumnName="id", onDelete="CASCADE")
     * })
     * @Gedmo\Versioned
     * @Api\Link(embed=true, route="get_show", params={"identifier": "object.getShow().getSlug()"})
     */
    private $show;

    /**
     * @var string
     *
     * @ORM\Column(name="positions", type="text", nullable=false)
     * @Assert\NotBlank()
     * @Gedmo\Versioned
     * @Serializer\Expose
     */
    private $positions;

    /**
     * @var string
     *
     * @ORM\Column(name="contact", type="text", nullable=false)
     * @Assert\NotBlank()
     * @Gedmo\Versioned
     * @Serializer\Expose
     */
    private $contact;

    /**
     * @var boolean
     *
     * @ORM\Column(name="deadline", type="boolean", nullable=false)
     * @Gedmo\Versioned
     * @Serializer\Expose
     */
    private $deadline;

    /**
     * @var string
     *
     * @ORM\Column(name="deadlinetime", type="time", nullable=false)
     * @Gedmo\Versioned
     * @Serializer\Expose
     */
    private $deadlineTime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="expiry", type="date", nullable=false)
     * @Assert\Date()
     * @Gedmo\Versioned
     * @Serializer\Expose
     */
    private $expiry;

    /**
     * @var boolean
     *
     * @ORM\Column(name="display", type="boolean", nullable=false)
     * @Gedmo\Versioned
     */
    private $display = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="remindersent", type="boolean", nullable=false)
     * @Gedmo\Versioned
     */
    private $reminder_sent = false;

    /**
     * @var string
     *
     * @ORM\Column(name="techextra", type="text", nullable=false)
     * @Assert\Length(max=1140)
     * @Gedmo\Versioned
     * @Serializer\Expose
     */
    private $tech_extra = "";

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lastupdated", type="datetime", nullable=false)
     * @Gedmo\Versioned
     * @Serializer\Expose
     * @Serializer\XmlElement(cdata=false)
     */
    private $last_updated;

    public function __construct()
    {
        $this->setUpdatedAt(new \DateTime);
        $this->setDeadlineTime(new \DateTime('00:00'));
        $this->setExpiry(new \DateTime('+10 days'));
    }


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
     * @return TechieAdvert
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

    public function getPositionsOneLine()
    {
        return preg_replace('/\r?\n/',", ",$this->positions);
    }

    /**
     * Set contact
     *
     * @param string $contact
     * @return TechieAdvert
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
     * @return TechieAdvert
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
     * Set deadline_time
     *
     * @param string $deadlineTime
     * @return TechieAdvert
     */
    public function setDeadlineTime($deadlineTime)
    {
        $this->deadlineTime = $deadlineTime;

        return $this;
    }

    /**
     * Get deadline_time
     *
     * @return string
     */
    public function getDeadlineTime()
    {
        return $this->deadlineTime;
    }

    /**
     * Set expiry
     *
     * @param \DateTime $expiry
     * @return TechieAdvert
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
     * @return TechieAdvert
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
     * Set reminder_sent
     *
     * @param boolean $reminderSent
     * @return TechieAdvert
     */
    public function setReminderSent($reminderSent)
    {
        $this->reminder_sent = $reminderSent;

        return $this;
    }

    /**
     * Get reminder_sent
     *
     * @return boolean
     */
    public function getReminderSent()
    {
        return $this->reminder_sent;
    }

    /**
     * Set tech_extra
     *
     * @param string $techExtra
     * @return TechieAdvert
     */
    public function setTechExtra($techExtra)
    {
      if (!($techExtra)) {
            $this->tech_extra = "";
	} else {
            $this->tech_extra = $techExtra;
	}

        return $this;
    }

    /**
     * Get tech_extra
     *
     * @return string
     */
    public function getTechExtra()
    {
        return $this->tech_extra;
    }

    /**
     * Set last_updated
     *
     * @param \DateTime $lastUpdated
     * @return TechieAdvert
     */
    public function setUpdatedAt($lastUpdated)
    {
        $this->last_updated = $lastUpdated;

        return $this;
    }

    /**
     * Get last_updated
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->last_updated;
    }

    public function getCreatedAt()
    {
        return $this->last_updated;
    }

    /**
     * Set show
     *
     * @param \Acts\CamdramBundle\Entity\Show $showId
     * @return TechieAdvert
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

    public function getFeedTitle()
    {
        return $this->getShow()->getName(). ' - last updated '.$this->getUpdatedAt()->format('D, j M Y H:i T');
    }

    public function getSlug()
    {
        return $this->getShow()->getSlug();
    }


    /**
     * Set last_updated
     *
     * @param \DateTime $lastUpdated
     * @return TechieAdvert
     */
    public function setLastUpdated($lastUpdated)
    {
        $this->last_updated = $lastUpdated;

        return $this;
    }

    /**
     * Get last_updated
     *
     * @return \DateTime 
     */
    public function getLastUpdated()
    {
        return $this->last_updated;
    }
}
