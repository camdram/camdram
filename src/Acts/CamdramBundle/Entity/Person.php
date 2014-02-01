<?php

namespace Acts\CamdramBundle\Entity;

use Acts\CamdramBundle\Search\SearchableInterface;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Criteria;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as Serializer;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Person
 *
 * @ORM\Table(name="acts_people_data", uniqueConstraints={@ORM\UniqueConstraint(name="people_slugs",columns={"slug"})})
 * @ORM\Entity(repositoryClass="PersonRepository")
 */
class Person implements SearchableInterface
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Serializer\XmlAttribute
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var \Hoyes\ImageManagerBundle\Entity\Image
     *
     * @ORM\ManyToOne(targetEntity="\Hoyes\ImageManagerBundle\Entity\Image")
     */
    private $image;

    /**
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(name="slug", type="string", length=128, nullable=true)
     * @Serializer\Expose
     */
    private $slug;

    /**
     * @var integer
     *
     * @ORM\Column(name="mapto", type="integer", nullable=false)
     * @Serializer\Exclude
     */
    private $map_to;

    /**
     * @var boolean
     *
     * @ORM\Column(name="norobots", type="boolean", nullable=false)
     * @Serializer\Exclude
     */
    private $no_robots;

    /**
     *
     * @ORM\OneToMany(targetEntity="Role", mappedBy="person")
     * @Serializer\Exclude
     */
    private $roles;

    /**
     * @var User
     *
     * @ORM\OneToMany(targetEntity="User", mappedBy="person")
     * @Serializer\Exclude
     */
    private $users;

    /**
     * @var User
     *
     * @ORM\OneToMany(targetEntity="Acts\CamdramSecurityBundle\Entity\ExternalUser", mappedBy="person")
     * @Serializer\Exclude
     */
    private $externalUsers;

    /**
     * @var array
     *
     * @ORM\OneToMany(targetEntity="NameAlias", mappedBy="person")
     * @Serializer\Exclude
     */
    private $aliases;

    /**
     * @Serializer\Expose
     */
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
        return $this->entity_type;
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
     * Set facebook_id
     *
     * @param string $facebookId
     * @return Person
     */
    public function setFacebookId($facebookId)
    {
        $this->facebook_id = $facebookId;
    
        return $this;
    }

    /**
     * Get facebook_id
     *
     * @return string 
     */
    public function getFacebookId()
    {
        return $this->facebook_id;
    }

    /**
     * Set twitter_id
     *
     * @param string $twitterId
     * @return Person
     */
    public function setTwitterId($twitterId)
    {
        $this->twitter_id = $twitterId;
    
        return $this;
    }

    /**
     * Get twitter_id
     *
     * @return string 
     */
    public function getTwitterId()
    {
        return $this->twitter_id;
    }

    /**
     * Set image
     *
     * @param \Hoyes\ImageManagerBundle\Entity\Image $image
     * @return Person
     */
    public function setImage(\Hoyes\ImageManagerBundle\Entity\Image $image = null)
    {
        $this->image = $image;
    
        return $this;
    }

    /**
     * Get image
     *
     * @return \Hoyes\ImageManagerBundle\Entity\Image 
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Person
     */
    public function setDescription($description)
    {
        $this->description = $description;
    
        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return Person
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
     * Add externalUsers
     *
     * @param \Acts\CamdramSecurityBundle\Entity\ExternalUser $externalUsers
     * @return Person
     */
    public function addExternalUser(\Acts\CamdramSecurityBundle\Entity\ExternalUser $externalUsers)
    {
        $this->externalUsers[] = $externalUsers;
    
        return $this;
    }

    /**
     * Remove externalUsers
     *
     * @param \Acts\CamdramSecurityBundle\Entity\ExternalUser $externalUsers
     */
    public function removeExternalUser(\Acts\CamdramSecurityBundle\Entity\ExternalUser $externalUsers)
    {
        $this->externalUsers->removeElement($externalUsers);
    }

    /**
     * Get externalUsers
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getExternalUsers()
    {
        return $this->externalUsers;
    }
}