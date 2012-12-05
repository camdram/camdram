<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Exclude;

/**
 * ActsShows
 *
 * @ORM\Table(name="acts_shows")
 * @ORM\Entity
 */
class Show
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
     * @ORM\Column(name="dates", type="text", nullable=false)
     */
    private $dates;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="text", nullable=false)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="author", type="text", nullable=false)
     */
    private $author;

    /**
     * @var string
     *
     * @ORM\Column(name="prices", type="text", nullable=false)
     */
    private $prices;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=false)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="photourl", type="text", nullable=true)
     */
    private $photourl;

    /**
     * @var string
     *
     * @ORM\Column(name="venue", type="text", nullable=false)
     */
    private $venue;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="excludedate", type="date", nullable=false)
     */
    private $excludedate;

    /**
     * @var string
     *
     * @ORM\Column(name="society", type="text", nullable=true)
     */
    private $society;

    /**
     * @var boolean
     *
     * @ORM\Column(name="techsend", type="boolean", nullable=false)
     */
    private $techsend;

    /**
     * @var boolean
     *
     * @ORM\Column(name="actorsend", type="boolean", nullable=false)
     */
    private $actorsend;

    /**
     * @var string
     *
     * @ORM\Column(name="audextra", type="text", nullable=true)
     */
    private $audextra;

    /**
     * @var integer
     *
     * @ORM\Column(name="socid", type="integer", nullable=false)
     */
    private $socid;

    /**
     * @var integer
     *
     * @ORM\Column(name="venid", type="integer", nullable=false)
     */
    private $venid;

    /**
     * @var integer
     *
     * @ORM\Column(name="authorizeid", type="integer", nullable=false)
     */
    private $authorizeid;

    /**
     * @var boolean
     *
     * @ORM\Column(name="entered", type="boolean", nullable=false)
     */
    private $entered;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="entryexpiry", type="date", nullable=false)
     */
    private $entryexpiry;

    /**
     * @var string
     *
     * @ORM\Column(name="category", type="string", nullable=false)
     */
    private $category;

    /**
     * @var string
     *
     * @ORM\Column(name="bookingcode", type="text", nullable=false)
     */
    private $bookingcode;

    /**
     * @var integer
     *
     * @ORM\Column(name="primaryref", type="integer", nullable=false)
     */
    private $primaryref;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="timestamp", type="datetime", nullable=false)
     */
    private $timestamp;


    /**
     *
     * @ORM\ManyToMany(targetEntity="Person")
     * @ORM\JoinTable(name="acts_shows_people_link",
     *   joinColumns={@ORM\JoinColumn(name="sid", referencedColumnName="id")},
     *   inverseJoinColumns={@ORM\JoinColumn(name="pid", referencedColumnName="id")}
     * )
     * @Exclude
     */
    private $people;

    /**
     *
     * @ORM\OneToMany(targetEntity="Role", mappedBy="show")
     * @Exclude
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
     * Set dates
     *
     * @param string $dates
     * @return ActsShows
     */
    public function setDates($dates)
    {
        $this->dates = $dates;
    
        return $this;
    }

    /**
     * Get dates
     *
     * @return string 
     */
    public function getDates()
    {
        return $this->dates;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return ActsShows
     */
    public function setTitle($title)
    {
        $this->title = $title;
    
        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set author
     *
     * @param string $author
     * @return ActsShows
     */
    public function setAuthor($author)
    {
        $this->author = $author;
    
        return $this;
    }

    /**
     * Get author
     *
     * @return string 
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set prices
     *
     * @param string $prices
     * @return ActsShows
     */
    public function setPrices($prices)
    {
        $this->prices = $prices;
    
        return $this;
    }

    /**
     * Get prices
     *
     * @return string 
     */
    public function getPrices()
    {
        return $this->prices;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return ActsShows
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
     * Set photourl
     *
     * @param string $photourl
     * @return ActsShows
     */
    public function setPhotourl($photourl)
    {
        $this->photourl = $photourl;
    
        return $this;
    }

    /**
     * Get photourl
     *
     * @return string 
     */
    public function getPhotourl()
    {
        return $this->photourl;
    }

    /**
     * Set venue
     *
     * @param string $venue
     * @return ActsShows
     */
    public function setVenue($venue)
    {
        $this->venue = $venue;
    
        return $this;
    }

    /**
     * Get venue
     *
     * @return string 
     */
    public function getVenue()
    {
        return $this->venue;
    }

    /**
     * Set excludedate
     *
     * @param \DateTime $excludedate
     * @return ActsShows
     */
    public function setExcludedate($excludedate)
    {
        $this->excludedate = $excludedate;
    
        return $this;
    }

    /**
     * Get excludedate
     *
     * @return \DateTime 
     */
    public function getExcludedate()
    {
        return $this->excludedate;
    }

    /**
     * Set society
     *
     * @param string $society
     * @return ActsShows
     */
    public function setSociety($society)
    {
        $this->society = $society;
    
        return $this;
    }

    /**
     * Get society
     *
     * @return string 
     */
    public function getSociety()
    {
        return $this->society;
    }

    /**
     * Set techsend
     *
     * @param boolean $techsend
     * @return ActsShows
     */
    public function setTechsend($techsend)
    {
        $this->techsend = $techsend;
    
        return $this;
    }

    /**
     * Get techsend
     *
     * @return boolean 
     */
    public function getTechsend()
    {
        return $this->techsend;
    }

    /**
     * Set actorsend
     *
     * @param boolean $actorsend
     * @return ActsShows
     */
    public function setActorsend($actorsend)
    {
        $this->actorsend = $actorsend;
    
        return $this;
    }

    /**
     * Get actorsend
     *
     * @return boolean 
     */
    public function getActorsend()
    {
        return $this->actorsend;
    }

    /**
     * Set audextra
     *
     * @param string $audextra
     * @return ActsShows
     */
    public function setAudextra($audextra)
    {
        $this->audextra = $audextra;
    
        return $this;
    }

    /**
     * Get audextra
     *
     * @return string 
     */
    public function getAudextra()
    {
        return $this->audextra;
    }

    /**
     * Set socid
     *
     * @param integer $socid
     * @return ActsShows
     */
    public function setSocid($socid)
    {
        $this->socid = $socid;
    
        return $this;
    }

    /**
     * Get socid
     *
     * @return integer 
     */
    public function getSocid()
    {
        return $this->socid;
    }

    /**
     * Set venid
     *
     * @param integer $venid
     * @return ActsShows
     */
    public function setVenid($venid)
    {
        $this->venid = $venid;
    
        return $this;
    }

    /**
     * Get venid
     *
     * @return integer 
     */
    public function getVenid()
    {
        return $this->venid;
    }

    /**
     * Set authorizeid
     *
     * @param integer $authorizeid
     * @return ActsShows
     */
    public function setAuthorizeid($authorizeid)
    {
        $this->authorizeid = $authorizeid;
    
        return $this;
    }

    /**
     * Get authorizeid
     *
     * @return integer 
     */
    public function getAuthorizeid()
    {
        return $this->authorizeid;
    }

    /**
     * Set entered
     *
     * @param boolean $entered
     * @return ActsShows
     */
    public function setEntered($entered)
    {
        $this->entered = $entered;
    
        return $this;
    }

    /**
     * Get entered
     *
     * @return boolean 
     */
    public function getEntered()
    {
        return $this->entered;
    }

    /**
     * Set entryexpiry
     *
     * @param \DateTime $entryexpiry
     * @return ActsShows
     */
    public function setEntryexpiry($entryexpiry)
    {
        $this->entryexpiry = $entryexpiry;
    
        return $this;
    }

    /**
     * Get entryexpiry
     *
     * @return \DateTime 
     */
    public function getEntryexpiry()
    {
        return $this->entryexpiry;
    }

    /**
     * Set category
     *
     * @param string $category
     * @return ActsShows
     */
    public function setCategory($category)
    {
        $this->category = $category;
    
        return $this;
    }

    /**
     * Get category
     *
     * @return string 
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set bookingcode
     *
     * @param string $bookingcode
     * @return ActsShows
     */
    public function setBookingcode($bookingcode)
    {
        $this->bookingcode = $bookingcode;
    
        return $this;
    }

    /**
     * Get bookingcode
     *
     * @return string 
     */
    public function getBookingcode()
    {
        return $this->bookingcode;
    }

    /**
     * Set primaryref
     *
     * @param integer $primaryref
     * @return ActsShows
     */
    public function setPrimaryref($primaryref)
    {
        $this->primaryref = $primaryref;
    
        return $this;
    }

    /**
     * Get primaryref
     *
     * @return integer 
     */
    public function getPrimaryref()
    {
        return $this->primaryref;
    }

    /**
     * Set timestamp
     *
     * @param \DateTime $timestamp
     * @return ActsShows
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;
    
        return $this;
    }

    /**
     * Get timestamp
     *
     * @return \DateTime 
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->people = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add people
     *
     * @param \Acts\CamdramBundle\Entity\Person $people
     * @return Show
     */
    public function addPeople(\Acts\CamdramBundle\Entity\Person $people)
    {
        $this->people[] = $people;
    
        return $this;
    }

    /**
     * Remove people
     *
     * @param \Acts\CamdramBundle\Entity\Person $people
     */
    public function removePeople(\Acts\CamdramBundle\Entity\Person $people)
    {
        $this->people->removeElement($people);
    }

    /**
     * Get people
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPeople()
    {
        return $this->people;
    }

    /**
     * Add roles
     *
     * @param \Acts\CamdramBundle\Entity\Role $roles
     * @return Show
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
