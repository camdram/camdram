<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * WeekName
 *
 * @ORM\Table(name="acts_week_names")
 * @ORM\Entity(repositoryClass="Acts\CamdramBundle\Entity\WeekNameRepository")
 */
class WeekName
{
    /**
     * @var integer
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
     * @Gedmo\Slug(fields={"short_name"}, unique=false)
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
     * @var TimePeriod
     * @ORM\ManyToOne(targetEntity="TimePeriod", inversedBy="week_names")
     */
    private $time_period;

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
     * Set name
     *
     * @param string $name
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
     * Set time_period
     *
     * @param \Acts\CamdramBundle\Entity\TimePeriod $timePeriod
     * @return Week
     */
    public function setTimePeriod(\Acts\CamdramBundle\Entity\TimePeriod $timePeriod = null)
    {
        $this->time_period = $timePeriod;

        return $this;
    }

    /**
     * Get time_period
     *
     * @return \Acts\CamdramBundle\Entity\TimePeriod
     */
    public function getTimePeriod()
    {
        return $this->time_period;
    }

    /**
     * Set short_name
     *
     * @param string $shortName
     * @return WeekName
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
     * Set slug
     *
     * @param string $slug
     * @return WeekName
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
}
