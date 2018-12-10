<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Gedmo\Mapping\Annotation as Gedmo;
use Acts\CamdramApiBundle\Configuration\Annotation as Api;
use Acts\DiaryBundle\Model\EventInterface;

/**
 * Performance
 *
 * @ORM\Table(name="acts_performances")
 * @ORM\Entity(repositoryClass="Acts\CamdramBundle\Entity\PerformanceRepository")
 * @Serializer\ExclusionPolicy("all")
 * @Gedmo\Loggable
 * @Serializer\XmlRoot("performance")
 */
class Performance implements EventInterface
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
     * @ORM\ManyToOne(targetEntity="Show", inversedBy="performances")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sid", referencedColumnName="id", onDelete="CASCADE")
     * })
     * @Gedmo\Versioned
     * @Api\Link(embed=true, route="get_show", params={"identifier": "object.getShow().getSlug()"})
     */
    private $show;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_at", type="datetime", nullable=false)
     * @Serializer\Expose
     * @Gedmo\Versioned
     * @Serializer\XmlElement(cdata=false)
     */
    private $start_at;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="repeat_until", type="date", nullable=false)
     * @Serializer\Expose(if="object.isRepeating()")
     * @Gedmo\Versioned
     * @Serializer\XmlElement(cdata=false)
     * @Serializer\Type("DateTime<'Y-m-d'>")
     */
    private $repeat_until;

    /**
     * @var \Venue
     *
     * @ORM\ManyToOne(targetEntity="Venue", inversedBy="performances")
     * @ORM\JoinColumn(name="venid", referencedColumnName="id", onDelete="SET NULL")
     * @Gedmo\Versioned
     * @Api\Link(embed=true, route="get_venue", params={"identifier": "object.getVenue().getSlug()"})
     */
    private $venue;

    /**
     * @var string
     *
     * @ORM\Column(name="venue", type="string", length=255, nullable=true)
     * @Serializer\Expose
     * @Gedmo\Versioned
     * @Serializer\XmlElement(cdata=false)
     */
    private $other_venue;

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
     * Set show_id
     *
     * @param int $showId
     *
     * @return Performance
     */
    public function setShowId($showId)
    {
        $this->show_id = $showId;

        return $this;
    }

    /**
     * Get show_id
     *
     * @return int
     */
    public function getShowId()
    {
        return $this->show_id;
    }

    /**
     * @Serializer\VirtualProperty
     * @Serializer\XmlElement(cdata=false)
     */
    public function getDateString()
    {
        $startAt = clone $this->getStartAt();
        $startAt->setTimezone(new \DateTimeZone("Europe/London"));

        $str = $startAt->format('H:i');

        $str .= ', ' . $startAt->format('D jS F Y');
        if ($this->isRepeating()) {
            $str .= ' - '.$this->getRepeatUntil()->format('D jS F Y');
        }
        
        return $str;
    }

    /**
     * Set start_at
     *
     * @param \DateTime $startAt
     *
     * @return Performance
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
     * Set repeat_until
     *
     * @param \DateTime $repeatUntil
     *
     * @return Performance
     */
    public function setRepeatUntil($repeatUntil)
    {
        $this->repeat_until = $repeatUntil;

        return $this;
    }

    /**
     * Get repeat_until
     *
     * @return \DateTime
     */
    public function getRepeatUntil()
    {
        return $this->repeat_until;
    }

    /**
     * @return bool
     */
    public function isRepeating()
    {
        return $this->getStartAt()->format('Y-m-d') != $this->getRepeatUntil()->format('Y-m-d');
    }

    /**
     * Set other_venue
     *
     * @param string $venueName
     *
     * @return Performance
     */
    public function setOtherVenue($venueName)
    {
        $this->other_venue = $venueName;

        return $this;
    }

    /**
     * Get other_venue
     *
     * @return string
     */
    public function getOtherVenue()
    {
        return $this->other_venue;
    }

    /**
     * Get venue_name
     *
     * @return string
     */
    public function getVenueName()
    {
        if ($this->venue) {
            return $this->venue->getName();
        } elseif ($this->other_venue) {
            return $this->other_venue;
        }
    }

    /**
     * Set show
     *
     * @param \Acts\CamdramBundle\Entity\Show $show
     *
     * @return Performance
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
     * Get venue
     *
     * @return \Acts\CamdramBundle\Entity\Venue
     */
    public function getVenue()
    {
        return $this->venue;
    }

    /**
     * Set venue
     *
     * @param \Acts\CamdramBundle\Entity\Venue $venue
     *
     * @return Performance
     */
    public function setVenue(\Acts\CamdramBundle\Entity\Venue $venue = null)
    {
        $this->venue = $venue;

        return $this;
    }

    public function getName()
    {
        return $this->getShow()->getName();
    }

    public function getUpdatedAt()
    {
        return $this->getShow()->getTimestamp();
    }

    public function getEndAt()
    {
        return null;
    }

    /**
     * @Serializer\VirtualProperty()
     * @Serializer\XmlElement(cdata=false)
     * @deprecated
     */
    public function getStartDate()
    {
        $date = clone $this->getStartAt();
        $date->setTime(0, 0, 0);
        return $date;
    }

    /**
     * @Serializer\VirtualProperty()
     * @Serializer\XmlElement(cdata=false)
     * @deprecated
     */
    public function getEndDate()
    {
        return $this->getRepeatUntil();
    }

    /**
     * @Serializer\VirtualProperty()
     * @Serializer\XmlElement(cdata=false)
     * @deprecated
     */
    public function getTime()
    {
        $startAt = $this->getStartAt();
        return \DateTime::createFromFormat('!H:i', $startAt->format('H').':'.$startAt->format('i'));
    }
}
