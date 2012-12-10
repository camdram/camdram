<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Exclude;

/**
 * Person
 *
 * @ORM\Table(name="acts_people_data")
 * @ORM\Entity
 */
class Person
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="mapto", type="integer", nullable=false)
     */
    private $map_to;

    /**
     * @var boolean
     *
     * @ORM\Column(name="norobots", type="boolean", nullable=false)
     */
    private $no_robots;

   /**
     *
     * @ORM\ManyToMany(targetEntity="Show", mappedBy="people")

     * @Exclude
     */
    private $shows;
    
    /**
     *
     * @ORM\OneToMany(targetEntity="Role", mappedBy="person")
     * @Exclude
     */
    private $roles;

    /**
     * @var User
     *
     * @ORM\OneToOne(targetEntity="User", mappedBy="person")
     */
    private $user;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->shows = new \Doctrine\Common\Collections\ArrayCollection();
        $this->roles = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
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
     * @return Person
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
     * Set map_to
     *
     * @param integer $mapTo
     * @return Person
     */
    public function setMapTo($mapTo)
    {
        $this->map_to = $mapTo;
    
        return $this;
    }

    /**
     * Get map_to
     *
     * @return integer 
     */
    public function getMapTo()
    {
        return $this->map_to;
    }

    /**
     * Set no_robots
     *
     * @param boolean $noRobots
     * @return Person
     */
    public function setNoRobots($noRobots)
    {
        $this->no_robots = $noRobots;
    
        return $this;
    }

    /**
     * Get no_robots
     *
     * @return boolean 
     */
    public function getNoRobots()
    {
        return $this->no_robots;
    }

    /**
     * Add shows
     *
     * @param \Acts\CamdramBundle\Entity\Show $shows
     * @return Person
     */
    public function addShow(\Acts\CamdramBundle\Entity\Show $shows)
    {
        $this->shows[] = $shows;
    
        return $this;
    }

    /**
     * Remove shows
     *
     * @param \Acts\CamdramBundle\Entity\Show $shows
     */
    public function removeShow(\Acts\CamdramBundle\Entity\Show $shows)
    {
        $this->shows->removeElement($shows);
    }

    /**
     * Get shows
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getShows()
    {
        return $this->shows;
    }

    /**
     * Add roles
     *
     * @param \Acts\CamdramBundle\Entity\Role $roles
     * @return Person
     */
    public function addRole(\Acts\CamdramBundle\Entity\Role $roles)
    {
        $this->roles[] = $roles;
    
        return $this;
    }

    /**
     * Remove roles
     *
     * @param \Acts\CamdramBundle\Entity\Role $roles
     */
    public function removeRole(\Acts\CamdramBundle\Entity\Role $roles)
    {
        $this->roles->removeElement($roles);
    }

    /**
     * Get roles
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Set user
     *
     * @param \Acts\CamdramBundle\Entity\User $user
     * @return Person
     */
    public function setUser(\Acts\CamdramBundle\Entity\User $user = null)
    {
        $this->user = $user;
    
        return $this;
    }

    /**
     * Get user
     *
     * @return \Acts\CamdramBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }
}