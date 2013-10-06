<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Criteria;

use JMS\Serializer\Annotation\Exclude;

/**
 * Person
 *
 * @ORM\Table(name="acts_people_data")
 * @ORM\Entity(repositoryClass="PersonRepository")
 */
class Person extends Entity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="mapto", type="integer", nullable=false)
     * @Exclude
     */
    private $map_to;

    /**
     * @var boolean
     *
     * @ORM\Column(name="norobots", type="boolean", nullable=false)
     * @Exclude
     */
    private $no_robots;

    /**
     *
     * @ORM\OneToMany(targetEntity="Role", mappedBy="person")
     * @Exclude
     */
    private $roles;

    /**
     * @var User
     *
     * @ORM\OneToMany(targetEntity="User", mappedBy="person")
     * @Exclude
     */
    private $users;

    /**
     * @var array
     *
     * @ORM\OneToMany(targetEntity="NameAlias", mappedBy="person")
     * @Exclude
     */
    private $aliases;

    protected $entity_type = 'person';

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->roles = new \Doctrine\Common\Collections\ArrayCollection();
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
        $this->aliases = new \Doctrine\Common\Collections\ArrayCollection();

        $this->map_to = 0;
        $this->no_robots = 0;
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
        $criteria = Criteria::create()
            ->orderBy(array('show_id' => 'DESC'));
        return $this->roles->matching($criteria);
    }
    
    /**
     * Add users
     *
     * @param \Acts\CamdramBundle\Entity\User $users
     * @return Person
     */
    public function addUser(\Acts\CamdramBundle\Entity\User $users)
    {
        $this->users[] = $users;
    
        return $this;
    }

    /**
     * Remove users
     *
     * @param \Acts\CamdramBundle\Entity\User $users
     */
    public function removeUser(\Acts\CamdramBundle\Entity\User $users)
    {
        $this->users->removeElement($users);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Add aliases
     *
     * @param \Acts\CamdramBundle\Entity\NameAlias $aliases
     * @return Person
     */
    public function addAlias(\Acts\CamdramBundle\Entity\NameAlias $aliases)
    {
        $this->aliases[] = $aliases;
    
        return $this;
    }

    /**
     * Remove aliases
     *
     * @param \Acts\CamdramBundle\Entity\NameAlias $aliases
     */
    public function removeAlias(\Acts\CamdramBundle\Entity\NameAlias $aliases)
    {
        $this->aliases->removeElement($aliases);
    }

    /**
     * Get aliases
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAliases()
    {
        return $this->aliases;
    }

    public function addAliase(\Acts\CamdramBundle\Entity\NameAlias $aliases)
    {
        //Required by Doctrine as it can't handle irregular plurals...
    }

    public function removeAliase(\Acts\CamdramBundle\Entity\NameAlias $aliases)
    {
        //Required by Doctrine as it can't handle irregular plurals...
    }

    public function getEntityType()
    {
        return 'person';
    }

    /**
     * A ranking used by the autocomplete index
     * For people, return the start date of the most recent show
     *
     * @return int
     */
    public function getRank()
    {
        $latest = null;
        foreach ($this->getRoles() as $role) {
            if ($role->getShow() && (!$latest || $role->getShow()->getStartAt() > $latest)) {
                $latest = $role->getShow()->getStartAt();
            }
        }
        if (!$latest) return 0;
        else return $latest->format('U');
    }

    public function isIndexable()
    {
        return count($this->getRoles()) > 0;
    }

}
