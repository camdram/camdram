<?php

namespace Acts\CamdramBundle\Entity;

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
 * @ORM\Table(name="acts_people_data", uniqueConstraints={@ORM\UniqueConstraint(name="people_slugs",columns={"slug"})},
 *      indexes={@ORM\Index(name="idx_person_fulltext", columns={"name", "slug"}, flags={"fulltext"})})
 * @Gedmo\Loggable
 * @ORM\Entity(repositoryClass="PersonRepository")
 * @Serializer\XmlRoot("person")
 */
class Person extends BaseEntity
{
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
     * @var ?string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     * @Gedmo\Versioned
     */
    private $description;

    /**
     * @var ?Image
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
     * @var Person|null
     *
     * @ORM\ManyToOne(targetEntity="Person", inversedBy="mapped_from")
     * @ORM\JoinColumn(name="mapto", nullable=true)
     * @Serializer\Exclude
     * @Gedmo\Versioned
     */
    private $mapped_to;

    /**
     * List of redirects to this person. Not expected to be exposed publicly except
     * through the existence of the redirects.
     *
     * @var \Doctrine\Common\Collections\Collection<Person>
     *
     * @ORM\OneToMany(targetEntity="Person", mappedBy="mapped_to", fetch="EXTRA_LAZY")
     * @Serializer\Exclude
     */
    private $mapped_from;

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
     * @var \Doctrine\Common\Collections\Collection<\Acts\CamdramSecurityBundle\Entity\User>
     *
     * @ORM\OneToMany(targetEntity="\Acts\CamdramSecurityBundle\Entity\User", mappedBy="person")
     * @Serializer\Exclude
     */
    private $users;

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
        $this->mapped_from = new \Doctrine\Common\Collections\ArrayCollection();
        $this->roles = new \Doctrine\Common\Collections\ArrayCollection();
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();

        $this->no_robots = false;
    }

    public function setMappedTo(?Person $mapTo): self
    {
        $this->mapped_to = $mapTo;

        return $this;
    }

    public function getMappedTo(): ?Person
    {
        return $this->mapped_to;
    }

    public function getMappedFrom()
    {
        return $this->mapped_from;
    }

    public function setNoRobots(bool $noRobots): self
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

    public function getEntityType(): string
    {
        return $this->entity_type;
    }

    public function getId(): ?int
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
        $name = preg_replace('/[\p{Cc}\p{Cf}]/u', '', $name);
        $this->name = $name;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
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

    public function getSlug(): ?string
    {
        return $this->slug;
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

    public function getFirstActive()
    {
        $first = null;
        foreach ($this->getRoles() as $role) {
            if ($role->getShow() && (!$first || $role->getShow()->getStartAt() < $first) && $role->getShow()->getStartAt()) {
                $first = $role->getShow()->getStartAt();
            }
        }

        return $first;
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
