<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\VirtualProperty;
use JMS\Serializer\Annotation as Serializer;
use Acts\CamdramApiBundle\Configuration\Annotation as Api;

/**
 * ShowSlug
 *
 * @ORM\Table(name="acts_show_slugs")
 * @ORM\Entity
 * @Serializer\XmlRoot("role")
 * @Serializer\ExclusionPolicy("all")
 */
class ShowSlug
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
     * @var int
     *
     * @ORM\Column(name="sid", type="integer", nullable=false)
     */
    private $showId;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=128, nullable=false)
     * @Serializer\Expose()
     * @Serializer\XmlElement(cdata=false)
     */
    private $slug;

    /**
     * @ORM\ManyToOne(targetEntity="Show", inversedBy="roles")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sid", referencedColumnName="id")
     * })
     */
    private $show;

    /**
     * @ORM\ManyToOne(targetEntity="Show", inversedBy="roles")
     * @ORM\Column(name="create_date", type="datetime")
     */
    private $createdDate;

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
     * @return ShowSlug
     */
    public function setShowId($showId)
    {
        $this->showId = $showId;

        return $this;
    }

    /**
     * Get show_id
     *
     * @return int
     */
    public function getShowId()
    {
        return $this->showId;
    }

    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return ShowSlug
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
     * Set show
     *
     * @param \Acts\CamdramBundle\Entity\Show $show
     *
     * @return ShowSlug
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
     * Set creation timestamp
     *
     * @param \DateTime $date
     *
     * @return ShowSlug
     */
    public function setCreatedDate(\DateTime $date = null)
    {
        $this->createdDate = $date;

        return $this;
    }

    /**
     * Get creation timestamp
     *
     * @return \DateTime
     */
    public function getCreatedDate()
    {
        return $this->createdDate;
    }
}
