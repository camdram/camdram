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
     * @ORM\Column(name="start_at", type="datetime", nullable=false)
     * @Assert\NotBlank()
     * @Assert\DateTime()
     * @Gedmo\Versioned
     * @Serializer\Expose()
     * @Serializer\XmlElement(cdata=false)
     */
    private $start_at;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end_at", type="datetime", nullable=false)
     * @Assert\DateTime()
     * @Gedmo\Versioned
     * @Serializer\Expose()
     * @Serializer\XmlElement(cdata=false)
     */
    private $end_at;

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
     * @var Show
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
     * @Serializer\VirtualProperty
     * @Serializer\XmlElement(cdata=false)
     */
    public function getDateString()
    {
        $startAt = clone $this->getStartAt();
        $startAt->setTimezone(new \DateTimeZone("Europe/London"));

        $startDate = $startAt->format('D jS M');
        $startTime = $startAt->format('H:i');
        $str = $startDate . ', ' . $startTime;

        if ($this->getEndAt()) {
            $endAt = clone $this->getEndAt();
            $endAt->setTimezone(new \DateTimeZone("Europe/London"));
            if ($startAt == $endAt) return $str;

            $endDate = $endAt->format('D jS M');
            $endTime = $endAt->format('H:i');
            $str .= '–';
            if ($endDate != $startDate) $str .= $endDate . ', ';
            $str .= $endTime;
        }

        return $str;
    }

    /**
     * Set start_at
     *
     * @param \DateTime $startAt
     *
     * @return Audition
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
     *
     * @return Audition
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

    public function getRepeatUntil()
    {
        return $this->getStartAt();
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
