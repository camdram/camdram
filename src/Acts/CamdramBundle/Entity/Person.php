<?php

namespace Acts\CamdramBundle\Entity;

use Acts\CamdramBundle\Search\SearchableInterface;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Criteria;
use Symfony\Component\Validator\Constraints as Assert;
use Acts\CamdramSecurityBundle\Entity\User;
use JMS\Serializer\Annotation as Serializer;
use Gedmo\Mapping\Annotation as Gedmo;
use Hateoas\Configuration\Annotation as Hateoas;

/**
 * Person
 *
 * @ORM\Table(name="acts_people_data", uniqueConstraints={@ORM\UniqueConstraint(name="people_slugs",columns={"slug"})})
 * @Gedmo\Loggable
 * @ORM\Entity(repositoryClass="PersonRepository")
 * @Serializer\XmlRoot("person")
 */
class Person
{
    /**
     * @var int
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
     * @Gedmo\Versioned
     * @Assert\NotBlank()
     * @Serializer\XmlElement(cdata=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     * @Gedmo\Versioned
     */
    private $description;

    /**
     * @var Image
     *
     * @ORM\ManyToOne(targetEntity="Image")
     * @Gedmo\Versioned
     * @Serializer\Expose()
     */
    private $image;

    /**
     * @Gedmo\Slug(handlers={
     *      @Gedmo\SlugHandler(class="Acts\CamdramBundle\Service\SlugHandler", options={})
     * }, fields={"name"})
     * @ORM\Column(name="slug", type="string", length=128, nullable=false)
     * @Serializer\Expose
     * @Serializer\XmlElement(cdata=false)
     */
    private $slug;

    /**
     * @var int
     *
     * @ORM\ManyToOne(targetEntity="Person")
     * @ORM\JoinColumn(name="mapto", nullable=true)
     * @Serializer\Exclude
     * @Gedmo\Versioned
     */
    private $mapped_to;

    /**
     * @var bool
     *
     * @ORM\Column(name="norobots", type="boolean", nullable=false)
     * @Serializer\Exclude
     * @Gedmo\Versioned
     */
    private $no_robots;

    /**
     * @ORM\OneToMany(targetEntity="Role", mappedBy="person")
     * @Serializer\Exclude
     */
    private $roles;

    /**
     * @var \Acts\CamdramSecurityBundle\Entity\User
     *
     * @ORM\OneToMany(targetEntity="\Acts\CamdramSecurityBundle\Entity\User", mappedBy="person")
     * @Serializer\Exclude
     */
    private $users;

    /**
     * @var \Acts\CamdramSecurityBundle\Entity\ExternalUser
     *
     * @ORM\OneToMany(targetEntity="Acts\CamdramSecurityBundle\Entity\ExternalUser", mappedBy="person")
     * @Serializer\Exclude
     */
    private $externalUsers;

    /**
     * @Serializer\Expose
     * @Serializer\XmlElement(cdata=false)
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
     * @param int $mapedTo
     *
     * @return Person
     */
    public function setMappedTo($mapTo)
    {
        $this->mapped_to = $mapTo;

        return $this;
    }

    /**
     * Get map_to
     *
     * @return int
     */
    public function getMappedTo()
    {
        return $this->mapped_to;
    }

    /**
     * Set no_robots
     *
     * @param bool $noRobots
     *
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
     * @return bool
     */
    public function getNoRobots()
    {
        return $this->no_robots;
    }

    /**
     * Add roles
     *
     * @param \Acts\CamdramBundle\Entity\Role $roles
     *
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
            ->orderBy(array('showId' => 'DESC'));

        return $this->roles->matching($criteria);
    }

    /**
     * Add users
     *
     * @param \Acts\CamdramSecurityBundle\Entity\User $users
     *
     * @return Person
     */
    public function addUser(User $users)
    {
        $this->users[] = $users;

        return $this;
    }

    /**
     * Remove users
     *
     * @param \Acts\CamdramSecurityBundle\Entity\User $users
     */
    public function removeUser(User $users)
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
        $activeDate = $this->getLastActive();

        return $activeDate ? $activeDate->format('Ymd') : 0;
    }


    public function isIndexable()
    {
        return !empty($this->getName()) && !$this->isMapped() && count($this->getRoles());
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
     * Set name
     *
     * @param string $name
     *
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
     *
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
     *
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
     * @param Image $image
     *
     * @return Person
     */
    public function setImage(Image $image = null)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return Image
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set description
     *
     * @param string $description
     *
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
     *
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
     *
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

    public function getShowCount()
    {
        $counter = 0;
        $show_id = null;
        foreach ($this->getRoles() as $role) {
            if ($role->getShow() && (!$show_id || $role->getShow() != $show_id)) {
                $counter++;
                $show_id = $role->getShow();
            }
        }

        return $counter;
    }

    public function getLastActive()
    {
        $latest = null;
        foreach ($this->getRoles() as $role) {
            if ($role->getShow() && (!$latest || $role->getShow()->getStartAt() > $latest) && $role->getShow()->getStartAt()) {
                $latest = $role->getShow()->getStartAt();
            }
        }

        return $latest;
    }

    public function isMapped()
    {
        return $this->getMappedTo() instanceof self;
    }
}
