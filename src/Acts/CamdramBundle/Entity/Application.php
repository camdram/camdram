<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Acts\CamdramApiBundle\Configuration\Annotation as Api;
use JMS\Serializer\Annotation as Serializer;

/**
 * Application
 *
 * @ORM\Table(name="acts_applications")
 * @ORM\Entity(repositoryClass="Acts\CamdramBundle\Entity\ApplicationRepository")
 * @Api\Feed(name="Camdram.net - Director/Producer and show applications", titleField="feed_title",
 *     description="Applications to produce, direct, or put on shows in Cambridge",
 *     template="application/rss.html.twig")
 * @Gedmo\Loggable
 * @Serializer\ExclusionPolicy("all")
 * @Api\Link(route="get_application",
 *      params={"identifier": "object.getOwningEntity().getSlug()"})
 * @Serializer\XmlRoot("application")
 */
class Application
{
    /**
     * @var int
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
     * @ORM\ManyToOne(targetEntity="Show", inversedBy="applications")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="showid", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     * })
     * @Gedmo\Versioned
     * @Api\Link(embed=true, route="get_show", params={"identifier": "object.getShow().getSlug()"})
     */
    private $show;

    /**
     * @var \Society
     *
     * @ORM\ManyToOne(targetEntity="Society", inversedBy="applications")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="society_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     * })
     * @Gedmo\Versioned
     * @Api\Link(embed=true, route="get_society", params={"identifier": "object.getSociety().getSlug()"})
     */
    private $society;

    /**
     * @var \Venue
     *
     * @ORM\ManyToOne(targetEntity="Venue", inversedBy="applications")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="venue_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     * })
     * @Gedmo\Versioned
     * @Api\Link(embed=true, route="get_venue", params={"identifier": "object.getVenue().getSlug()"})
     */
    private $venue;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="text", nullable=false)
     * @Assert\NotBlank()
     * @Gedmo\Versioned
     * @Serializer\Expose
     */
    private $text;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deadlinedate", type="date", nullable=false)
     * @Assert\NotBlank()
     * @Assert\Type("\DateTimeInterface")
     * @Gedmo\Versioned
     * @Serializer\Expose
     * @Serializer\XmlElement(cdata=false)
     */
    private $deadlineDate;

    /**
     * @var string
     *
     * @ORM\Column(name="furtherinfo", type="text", nullable=false)
     * @Assert\NotBlank()
     * @Gedmo\Versioned
     * @Serializer\Expose
     */
    private $furtherInfo;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deadlinetime", type="time", nullable=false)
     * @Assert\NotBlank()
     * @Assert\Type("\DateTimeInterface")
     * @Gedmo\Versioned
     * @Serializer\Expose
     * @Serializer\XmlElement(cdata=false)
     */
    private $deadlineTime;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set text
     *
     * @param string $text
     *
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
     *
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
     *
     * @return Application
     */
    public function setFurtherInfo($furtherInfo)
    {
        $this->furtherInfo = $furtherInfo;

        return $this;
    }

    /**
     * Get further_info
     *
     * @return string
     */
    public function getFurtherInfo()
    {
        return $this->furtherInfo;
    }

    /**
     * Set deadlineTime
     *
     * @param \DateTime $deadlineTime
     *
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
     *
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
     *
     * @return Application
     */
    public function setSociety(Society $society = null)
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

    /**
     * Set venue
     *
     * @param \Acts\CamdramBundle\Entity\Venue $venue
     *
     * @return Application
     */
    public function setVenue(Venue $venue = null)
    {
        $this->venue = $venue;

        return $this;
    }

    /**
     * Get venue
     *
     * @return \Acts\CamdramBundle\Entity\Venue
     */
    public function getVenue()
    {
        return $this->venue;
    }

    /**
     * Gets the owning entity.
     */
    public function getOwningEntity()
    {
        return $this->show ?: $this->society ?: $this->venue;
    }

    public function getFeedTitle()
    {
        return $this->getOwningEntity()->getName();
    }

    public function getSlug()
    {
        return $this->getOwningEntity()->getSlug();
    }
}
