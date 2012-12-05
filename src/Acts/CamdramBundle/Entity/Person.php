<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ActsPeopleData
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
     * @ORM\Column(name="name", type="text", nullable=false)
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="mapto", type="integer", nullable=false)
     */
    private $mapto;

    /**
     * @var boolean
     *
     * @ORM\Column(name="norobots", type="boolean", nullable=false)
     */
    private $norobots;

   /**
     *
     * @ORM\ManyToMany(targetEntity="Show")
     * @ORM\JoinTable(name="acts_shows_people_link",
     *   joinColumns={@ORM\JoinColumn(name="pid", referencedColumnName="id")},
     *   inverseJoinColumns={@ORM\JoinColumn(name="sid", referencedColumnName="id")}
     * )
     */
    private $shows;
    
    /**
     *
     * @ORM\OneToMany(targetEntity="Role", mappedBy="person")
     * 
     */
    private $roles;


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
     * @return ActsPeopleData
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
     * Set mapto
     *
     * @param integer $mapto
     * @return ActsPeopleData
     */
    public function setMapto($mapto)
    {
        $this->mapto = $mapto;
    
        return $this;
    }

    /**
     * Get mapto
     *
     * @return integer 
     */
    public function getMapto()
    {
        return $this->mapto;
    }

    /**
     * Set norobots
     *
     * @param boolean $norobots
     * @return ActsPeopleData
     */
    public function setNorobots($norobots)
    {
        $this->norobots = $norobots;
    
        return $this;
    }

    /**
     * Get norobots
     *
     * @return boolean 
     */
    public function getNorobots()
    {
        return $this->norobots;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->shows = new \Doctrine\Common\Collections\ArrayCollection();
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
}
