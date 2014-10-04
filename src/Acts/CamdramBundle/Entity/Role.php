<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Exclude;

/**
 * Role
 *
 * @ORM\Table(name="acts_shows_people_link")
 * @ORM\Entity(repositoryClass="Acts\CamdramBundle\Entity\RoleRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Role
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="sid", type="integer", nullable=true)
     */
    private $show_id;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=20, nullable=false)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="role", type="string", length=255, nullable=false)
     */
    private $role;

    /**
     * @var integer
     *
     * @ORM\Column(name="`order`", type="integer", nullable=false)
     */
    private $order;

    /**
     *
     * @ORM\ManyToOne(targetEntity="Show", inversedBy="roles")
     * @Exclude
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sid", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $show;

    /**
     *
     * @ORM\ManyToOne(targetEntity="Person", inversedBy="roles")
     * @Exclude
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="pid", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $person;


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
     * Set show_id
     *
     * @param integer $showId
     * @return Role
     */
    public function setShowId($showId)
    {
        $this->show_id = $showId;

        return $this;
    }

    /**
     * Get show_id
     *
     * @return integer
     */
    public function getShowId()
    {
        return $this->show_id;
    }

    /**
     * Set type
     *
     * @param string $type
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
     * @param integer $order
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
     * @return integer
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Set show
     *
     * @param \Acts\CamdramBundle\Entity\Show $show
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
}
