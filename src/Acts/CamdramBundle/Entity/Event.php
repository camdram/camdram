<?php

namespace Acts\CamdramBundle\Entity;

use Acts\CamdramApiBundle\Entity\ArrayEntity;
use Acts\CamdramSecurityBundle\Security\OwnableInterface;
use Acts\DiaryBundle\Model\EventInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Event
 *
 * @Gedmo\Loggable
 * @ORM\Table(name="acts_events",
 *      indexes={@ORM\Index(name="idx_event_fulltext", columns={"text"}, flags={"fulltext"})})
 * @ORM\Entity
 */
class Event extends BaseEntity implements EventInterface, OwnableInterface
{
    use MultipleSocsTrait;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @ORM\Column(name="text", type="string", length=255, nullable=false)
     * @Serializer\Exclude(if="object.getLinkId() != null")
     * @Serializer\XmlElement(cdata=false)
     */
    private $name;

    /**
     * This property holds the CLOCK TIME that the event ends regardless of time zones etc.
     *
     * @var \DateTime
     * @Assert\NotBlank()
     * @ORM\Column(name="endtime", type="time", nullable=false)
     * @Serializer\Exclude
     * @Serializer\XmlElement(cdata=false)
     */
    private $end_time;

    /**
     * This property holds the UTC date and time that the event starts.
     *
     * @var \DateTime
     * @Assert\NotBlank()
     * @ORM\Column(name="start_at", type="datetime", nullable=false)
     * @Serializer\XmlElement(cdata=false)
     */
    private $start_at;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @ORM\Column(name="description", type="text", nullable=false)
     * @Serializer\Exclude(if="object.getLinkId() != null")
     * @Serializer\XmlElement(cdata=false)
     */
    private $description;

    /**
     * @var ?Event
     *
     * @ORM\ManyToOne(targetEntity="Event", inversedBy="linked_dates")
     * @ORM\OrderBy({"start_at" = "ASC"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="linkid", referencedColumnName="id", onDelete="CASCADE")
     * })
     * @Serializer\SerializedName("root_event")
     */
    private $link_id;

    /**
     * @Assert\Valid(traverse=true)
     * @ORM\OneToMany(targetEntity="Event", mappedBy="link_id", cascade={"persist", "remove"})
     * @Serializer\Exclude
     */
    private $linked_dates;

    /**
     * @var ?ArrayCollection<Event>
     */
    private $deleted_dates;

    /**
     * All the registered scieties involved with this show.
     * @ORM\ManyToMany(targetEntity="Society", inversedBy="events")
     * @ORM\JoinTable(name="acts_event_soc_link")
     * @Serializer\Exclude
     */
    private $societies;

    /**
     * Should be in #f21343 notation, if it isn't the form is broken. (It uses JS to enforce the notation.)
     * @var ?string
     * @Assert\Regex("/^#[0-9A-Fa-f]{6}$/",
     *     message="The provided colour must be in six-digit hex notation. If this isn't working leave it blank and contact support.")
     * @ORM\Column(name="colour", type="string", length=7, nullable=true)
     * @Serializer\Exclude(if="object.getLinkId() != null")
     * @Serializer\XmlElement(cdata=false)
     */
    private $theme_color;

    /**
     * @var ?Image
     *
     * @ORM\ManyToOne(targetEntity="Image")
     * @Gedmo\Versioned
     * @Serializer\Exclude(if="object.getLinkId() != null")
     */
    private $image;

    public function __construct()
    {
        $this->deleted_dates = new ArrayCollection();
        $this->linked_dates = new ArrayCollection();
        $this->societies    = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param \DateTime $endTime
     */
    public function setEndTime($endTime): self
    {
        $this->end_time = $endTime;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getEndTime()
    {
        return $this->end_time;
    }

    /**
     * @param \DateTime $startAt
     */
    public function setStartAt($startAt): self
    {
        $this->start_at = $startAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getStartAt()
    {
        return $this->start_at;
    }

    /**
     * @param string $description
     */
    public function setDescription($description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return ($this->getLinkId() ?: $this)->description;
    }

    public function setLinkId(?Event $linkId): self
    {
        $this->link_id = $linkId;

        return $this;
    }

    /**
     * @return ?Event
     */
    public function getLinkId()
    {
        return $this->link_id;
    }

    /** @return iterable<Event> */
    public function getLinkedDates()
    {
        return $this->linked_dates;
    }

    public function addLinkedDate(Event $evt): self
    {
        $this->linked_dates->add($evt);
        $evt->setLinkId($this);
        return $this;
    }

    public function removeLinkedDate(Event $evt): self
    {
        $this->linked_dates->removeElement($evt);
        $this->getDeletedDates()->add($evt);
        return $this;
    }

    /** @return ArrayCollection<Event> */
    public function getDeletedDates(): ArrayCollection
    {
        if (!$this->deleted_dates) $this->deleted_dates = new ArrayCollection();
        return $this->deleted_dates;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getName(): ?string
    {
        return ($this->getLinkId() ?: $this)->name;
    }

    /**
     * Required for BaseEntity, actually returns the ID.
     */
    public function getSlug(): string
    {
        return "{$this->getId()}";
    }

    public static function getAceType(): string
    {
        return "event";
    }

    public function getEntityType(): string
    {
        return "event";
    }

    public function getTimes(): array
    {
        $dateList = [];
        $dateList[] = [$this->getStartAt(), $this->getEndTime()];

        foreach ($this->linked_dates as $linked) {
            $dateList[] = [$linked->getStartAt(), $linked->getEndTime()];
        }

        usort($dateList, function ($a, $b) { return $a <=> $b; });
        return $dateList;
    }

    public function getThemeColor()
    {
        return $this->theme_color;
    }

    public function setThemeColor(?string $color): self
    {
        $this->theme_color = $color;
        return $this;
    }

    public function setImage(?Image $image): self
    {
        $this->image = $image;
        return $this;
    }

    public function getImage()
    {
        return $this->image;
    }

    /**
     * @Serializer\Exclude(if="object.getLinkId() != null")
     * @Serializer\SerializedName("dates")
     * @Serializer\VirtualProperty()
     * @Serializer\XmlList(entry="date")
     */
    public function getSubeventsForAPI(): array
    {
        $evts = array_merge([$this], $this->linked_dates->toArray());
        $out = [];
        foreach ($evts as $date) {
            $out[] = new ArrayEntity([
                'start_at' => $date->getStartAt(),
                'end_at' => $date->getEndAt(),
                'id' => $date->getId(),
            ]);
        }
        return $out;
    }

    public function shouldSerializeSocieties(): bool
    {
        return $this->link_id == null;
    }

    // EventInterface

    /**
     * Returns the DateTime that this event ends calculated from the start_at
     * and end_time fields.
     * @Serializer\VirtualProperty()
     * @Serializer\XmlElement(cdata=false)
     */
    public function getEndAt(): \DateTime
    {
        $endtime = explode(':', $this->getEndTime()->format('H:i:s'));
        $enddatetime = clone $this->getStartAt();
        $enddatetime->setTimezone(new \DateTimeZone('Europe/London'));
        $enddatetime->setTime(...array_map('intval', $endtime));
        if ($enddatetime < $this->getStartAt()) $enddatetime->modify('+1 day');
        return $enddatetime;
    }

    public function getRepeatUntil()
    {
        return $this->getStartAt();
    }

    public function getUpdatedAt()
    {
        return new \DateTime('1970-01-01 01:00');
    }

    public function getVenue()
    {
        return null;
    }

    public function getVenueName()
    {
        return '';
    }
}
