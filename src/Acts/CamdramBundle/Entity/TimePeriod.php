<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as Serializer;

/**
 * TimePeriod
 *
 * @ORM\Table(name="acts_time_periods")
 * @ORM\Entity(repositoryClass="Acts\CamdramBundle\Entity\TimePeriodRepository")
 */
class TimePeriod
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="short_name", type="string", length=255)
     */
    private $short_name;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="full_name", type="string", length=255)
     */
    private $full_name;

    /**
     * @Gedmo\Slug(fields={"name"}, unique=false)
     * @ORM\Column(name="slug", type="string", length=128, nullable=true)
     */
    private $slug;

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
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="WeekName", mappedBy="time_period")
     * @Serializer\Exclude
     */
    private $week_names;

    public function __construct()
    {
        $this->weeks = new ArrayCollection();
    }

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
     * Set short_name
     *
     * @param string $shortName
     *
     * @return TimePeriod
     */
    public function setShortName($shortName)
    {
        $this->short_name = $shortName;

        return $this;
    }

    /**
     * Get short_name
     *
     * @return string
     */
    public function getShortName()
    {
        return $this->short_name;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return TimePeriod
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
     * Set start_at
     *
     * @param \DateTime $startAt
     *
     * @return TimePeriod
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
     * @return TimePeriod
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
     * Add weeks
     *
     * @param \Acts\CamdramBundle\Entity\WeekName $weeks
     *
     * @return TimePeriod
     */
    public function addWeek(\Acts\CamdramBundle\Entity\WeekName $weeks)
    {
        $this->weeks[] = $weeks;

        return $this;
    }

    /**
     * Remove weeks
     *
     * @param \Acts\CamdramBundle\Entity\WeekName $weeks
     */
    public function removeWeek(\Acts\CamdramBundle\Entity\WeekName $weeks)
    {
        $this->weeks->removeElement($weeks);
    }

    /**
     * Get weeks
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getWeeks()
    {
        return $this->weeks;
    }

    /**
     * Set full_name
     *
     * @param string $fullName
     *
     * @return TimePeriod
     */
    public function setFullName($fullName)
    {
        $this->full_name = $fullName;

        return $this;
    }

    /**
     * Get full_name
     *
     * @return string
     */
    public function getFullName()
    {
        return $this->full_name;
    }

    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return TimePeriod
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Add week_names
     *
     * @param \Acts\CamdramBundle\Entity\WeekName $weekNames
     *
     * @return TimePeriod
     */
    public function addWeekName(\Acts\CamdramBundle\Entity\WeekName $weekNames)
    {
        $this->week_names[] = $weekNames;

        return $this;
    }

    /**
     * Remove week_names
     *
     * @param \Acts\CamdramBundle\Entity\WeekName $weekNames
     */
    public function removeWeekName(\Acts\CamdramBundle\Entity\WeekName $weekNames)
    {
        $this->week_names->removeElement($weekNames);
    }

    /**
     * Get week_names
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getWeekNames()
    {
        return $this->week_names;
    }
}
