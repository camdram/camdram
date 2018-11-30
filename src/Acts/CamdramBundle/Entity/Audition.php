<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Acts\CamdramApiBundle\Configuration\Annotation as Api;
use Acts\DiaryBundle\Model\EventInterface;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as Serializer;

/**
 * Audition
 *
 * @ORM\Table(name="acts_auditions")
 * @ORM\Entity(repositoryClass="Acts\CamdramBundle\Entity\AuditionRepository")
 * @Api\Feed(name="Camdram.net - Auditions", titleField="feed_title",
 *     description="Auditions advertised for shows in Cambridge",
 *     template="audition/rss.html.twig")
 * @Gedmo\Loggable
 * @Serializer\ExclusionPolicy("all")
 * @Serializer\XmlRoot("audition")
 * @Api\Link(route="get_audition", params={"identifier": "object.getShow().getSlug()"})
 */
class Audition implements EventInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Serializer\XmlAttribute
     * @Serializer\Expose()
     * @Serializer\Type("integer")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date", nullable=false)
     * @Assert\NotBlank()
     * @Assert\Date()
     * @Gedmo\Versioned
     * @Serializer\Expose()
     * @Serializer\XmlElement(cdata=false)
     */
    private $date;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="starttime", type="time", nullable=false)
     * @Assert\NotBlank()
     * @Assert\Time()
     * @Gedmo\Versioned
     * @Serializer\Expose()
     * @Serializer\XmlElement(cdata=false)
     */
    private $start_time;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="endtime", type="time", nullable=true)
     * @Assert\Time()
     * @Gedmo\Versioned
     * @Serializer\Expose()
     * @Serializer\XmlElement(cdata=false)
     */
    private $end_time;

    /**
     * @var string
     *
     * @ORM\Column(name="location", type="string", nullable=false)
     * @Assert\NotBlank()
     * @Gedmo\Versioned
     * @Serializer\Expose()
     * @Serializer\XmlElement(cdata=false)
     */
    private $location;

    /**
     * @var \Show
     *
     * @ORM\ManyToOne(targetEntity="Show", inversedBy="auditions")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="showid", referencedColumnName="id", onDelete="CASCADE")
     * })
     * @Gedmo\Versioned
     * @Api\Link(embed=true, route="get_show", params={"identifier": "object.getShow().getSlug()"})
     */
    private $show;

    /**
     * @var bool
     *
     * @ORM\Column(name="display", type="boolean", nullable=false)
     * @Gedmo\Versioned
     */
    private $display = false;

    /**
     * @var bool
     *
     * @ORM\Column(name="nonscheduled", type="boolean", nullable=false)
     * @Gedmo\Versioned
     */
    private $nonScheduled;

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
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Audition
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
     * Set start_time
     *
     * @param \DateTime $startTime
     *
     * @return Audition
     */
    public function setStartTime($startTime)
    {
        $this->start_time = $startTime;

        return $this;
    }

    /**
     * Get start_time
     *
     * @return \DateTime
     */
    public function getStartTime()
    {
        return $this->start_time;
    }

    /**
     * Set end_time
     *
     * @param \DateTime $endTime
     *
     * @return Audition
     */
    public function setEndTime($endTime)
    {
        $this->end_time = $endTime;

        return $this;
    }

    /**
     * Get end_time
     *
     * @return \DateTime
     */
    public function getEndTime()
    {
        return $this->end_time;
    }

    /**
     * Set location
     *
     * @param string $location
     *
     * @return Audition
     */
    public function setLocation($location)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get location
     *
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set display
     *
     * @param bool $display
     *
     * @return Audition
     */
    public function setDisplay($display)
    {
        $this->display = $display;

        return $this;
    }

    /**
     * Get display
     *
     * @return bool
     */
    public function getDisplay()
    {
        return $this->display;
    }

    /**
     * Set non_scheduled
     *
     * @param bool $nonScheduled
     *
     * @return Audition
     */
    public function setNonScheduled($nonScheduled)
    {
        $this->nonScheduled = $nonScheduled;

        return $this;
    }

    /**
     * Get nonSheduled
     *
     * @return bool
     */
    public function getNonScheduled()
    {
        return $this->nonScheduled;
    }

    /**
     * Set show
     *
     * @param \Acts\CamdramBundle\Entity\Show $show
     *
     * @return Audition
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
        return $this->getShow()->getName();
    }

    public function getSlug()
    {
        return $this->getShow()->getSlug();
    }

    // EventInterface

    public function getName()
    {
        return $this->show->getName();
    }

    public function getStartDate()
    {
        return $this->date;
    }

    public function getEndDate()
    {
        return $this->date;
    }

    public function getVenueName()
    {
        return $this->location;
    }

    public function getVenue()
    {
        return null;
    }

    public function getUpdatedAt()
    {
        return $this->getShow()->getTimestamp();
    }
}
