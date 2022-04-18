<?php

namespace Acts\CamdramBundle\Entity;

use Acts\CamdramApiBundle\Configuration\Annotation as Api;
use Acts\DiaryBundle\Model\EventInterface;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

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
     * @var Show
     *
     * @ORM\ManyToOne(targetEntity="Show", inversedBy="performances")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sid", referencedColumnName="id", onDelete="CASCADE", nullable=false)
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
     * @var ?Venue
     *
     * @ORM\ManyToOne(targetEntity="Venue", inversedBy="performances")
     * @ORM\JoinColumn(name="venue_id", referencedColumnName="id", onDelete="SET NULL")
     * @Gedmo\Versioned
     * @Api\Link(embed=true, route="get_venue", params={"identifier": "object.getVenue().getSlug()"})
     */
    private $venue;

    /**
     * @var ?string
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
     * @Serializer\VirtualProperty
     * @Serializer\XmlElement(cdata=false)
     */
    public function getDateString()
    {
        $startAt = clone $this->getStartAt();
        $startAt->setTimezone(new \DateTimeZone("Europe/London"));

        if (!$this->isRepeating()) {
            return $startAt->format('H:i, D jS F Y');
        }

        $repeatUntil = $this->getRepeatUntil();
        $str = ' – ' . $repeatUntil->format('D jS F Y');
        if ($startAt->format('F Y') === $repeatUntil->format('F Y')) {
            return $startAt->format('H:i, D jS') . $str;
        }
        if ($startAt->format('Y') === $repeatUntil->format('Y')) {
            return $startAt->format('H:i, D jS F') . $str;
        }
        return $startAt->format('H:i, D jS F Y') . $str;
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
     * Returns a DateTime with both date and time filled in of the final
     * performance.
     *
     * Maintains UK time across a daylight saving change.
     */
    public function getFinalDateTime(): ?\DateTime
    {
        $tz = new \DateTimeZone('Europe/London');
        $dateStr = $this->repeat_until->format("Y-m-d ");
        $startAtClone = clone $this->start_at;
        $startAtClone->setTimezone($tz);
        return new \DateTime($dateStr . $startAtClone->format("H:i:s"), $tz);
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

    public function getVenueName(): ?string
    {
        if ($this->venue) {
            return $this->venue->getName();
        } elseif ($this->other_venue) {
            return $this->other_venue;
        } else return null;
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
     * There is further validation client-side which generates warnings.
     *
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context, $payload)
    {
        if ($this->getStartAt() == null) {
            $context->buildViolation("Invalid or blank time, or start date. Try a different web browser if the problem persists.")
                    ->atPath('start_at')
                    ->addViolation();
            return;
        }
        if ($this->getRepeatUntil() == null) {
            $context->buildViolation("Invalid or blank end date. Try a different web browser if the problem persists.")
                    ->atPath('repeat_until')
                    ->addViolation();
            return;
        }
        // As the start date has a time associated but the end doesn't the time
        // must be removed.
        $startDateOnly = $this->getStartAt()->format("Ymd");
        $endDateOnly = $this->getRepeatUntil()->format("Ymd");
        if ($startDateOnly > $endDateOnly) {
            $context->buildViolation("The run can't finish before it's begun! Check your dates.")
                    ->atPath('repeat_until')
                    ->addViolation();
        }
        if ($this->getRepeatUntil() > new \DateTime("+18 months")) {
            $context->buildViolation("Shows may only be listed on Camdram up to 18 months in advance. Check your dates.")
                    ->atPath('repeat_until')
                    ->addViolation();
        }
    }
}
