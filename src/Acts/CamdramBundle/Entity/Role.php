<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\VirtualProperty;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as Serializer;
use Acts\CamdramApiBundle\Configuration\Annotation as Api;

/**
 * Role
 *
 * @ORM\Table(name="acts_shows_people_link")
 * @ORM\Entity(repositoryClass="Acts\CamdramBundle\Entity\RoleRepository")
 * @ORM\EntityListeners({"Acts\CamdramBundle\EventListener\RoleListener"})
 * @ORM\HasLifecycleCallbacks()
 * @Gedmo\Loggable
 * @Serializer\XmlRoot("role")
 * @Serializer\ExclusionPolicy("all")
 */
class Role
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
     * @ORM\Column(name="type", type="string", length=20, nullable=false)
     * @Gedmo\Versioned
     * @Serializer\Expose()
     * @Serializer\XmlElement(cdata=false)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="role", type="string", length=255, nullable=false)
     * @Gedmo\Versioned
     * @Serializer\Expose()
     * @Serializer\XmlElement(cdata=false)
     */
    private $role;

    /**
     * @var int
     *
     * @ORM\Column(name="`order`", type="integer", nullable=false)
     * @Gedmo\Versioned
     * @Serializer\Expose()
     * @Serializer\XmlAttribute()
     */
    private $order;

    /**
     * @var ?Show
     * @ORM\ManyToOne(targetEntity="Show", inversedBy="roles")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sid", referencedColumnName="id", onDelete="CASCADE")
     * })
     * @Gedmo\Versioned
     * @Api\Link(route="get_show", embed=true, params={"identifier": "object.getShow().getSlug()"})
     */
    private $show;

    /**
     * @var ?Person
     * @ORM\ManyToOne(targetEntity="Person", inversedBy="roles")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="pid", referencedColumnName="id", onDelete="CASCADE")
     * })
     * @Gedmo\Versioned
     * @Api\Link(route="get_person", embed=true, params={"identifier": "object.getPerson().getSlug()"})
     */
    private $person;

    /**
     * @var ?string
     * @ORM\Column(name="tag", type="string", length=20, nullable=true)
     * @Serializer\Expose()
     * @Serializer\XmlElement(cdata=false)
     * @Gedmo\Versioned
     */
    private $tag;

    /**
      * @var Position|null
     * @ORM\ManyToOne(targetEntity="Position")
     * @ORM\JoinColumn(onDelete="SET NULL", nullable=true)
     */
    private $position;

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
     * @return Role
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
     * Set type
     *
     * @param string $type
     *
     * @return Role
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set role
     *
     * @param string $role
     *
     * @return Role
     */
    public function setRole($role)
    {
        $this->role = htmlentities($role);

        return $this;
    }

    /**
     * Get role
     *
     * @return string
     */
    public function getRole()
    {
        return html_entity_decode($this->role);
    }

    /**
     * Set order
     *
     * @param int $order
     *
     * @return Role
     */
    public function setOrder($order)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Get order
     *
     * @return int
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Set show
     *
     * @param \Acts\CamdramBundle\Entity\Show $show
     *
     * @return Role
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
     * Set person
     *
     * @param \Acts\CamdramBundle\Entity\Person $person
     *
     * @return Role
     */
    public function setPerson(\Acts\CamdramBundle\Entity\Person $person = null)
    {
        $this->person = $person;

        return $this;
    }

    /**
     * Get person
     *
     * @return \Acts\CamdramBundle\Entity\Person
     */
    public function getPerson()
    {
        return $this->person;
    }

    public function setTag(?string $tag): self
    {
        $this->tag = $tag;

        return $this;
    }
    public function getTag(): ?string
    {
        return $this->tag;
    }
    public function getAdjustedTag(): string
    {
        return $this->tag ?? [
            'cast' => 'Acting', 'band' => 'Band', 'prod' => 'Crew'
        ][$this->type];
    }

    /**
     * Get name of person
     *
     * @VirtualProperty
     *
     * @return string
     */
    public function getPersonName()
    {
        return $this->person->getName();
    }

    /**
     * Get slug of person
     *
     * @VirtualProperty
     * @return string
     */
    public function getPersonSlug()
    {
        return $this->person->getSlug();
    }

    public function getPosition(): ?Position
    {
        return $this->position;
    }

    public function setPosition(?Position $position): self
    {
        $this->position = $position;

        return $this;
    }
}
