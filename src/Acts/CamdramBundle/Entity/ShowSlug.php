<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\VirtualProperty;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as Serializer;
use Acts\CamdramApiBundle\Configuration\Annotation as Api;

/**
 * ShowSlug
 *
 * @ORM\Table(name="acts_show_slugs")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @Gedmo\Loggable
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
     * @ORM\Column(name="sid", type="integer", nullable=true)
     */
    private $showId;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=128, nullable=false)
     * @Gedmo\Versioned
     * @Serializer\Expose()
     * @Serializer\XmlElement(cdata=false)
     */
    private $slug;

    /**
     * @ORM\ManyToOne(targetEntity="Show", inversedBy="roles")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sid", referencedColumnName="id", onDelete="CASCADE")
     * })
     * @Gedmo\Versioned
     */
    private $show;

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
}
