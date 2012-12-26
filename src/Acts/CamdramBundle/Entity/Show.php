<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Criteria;

use JMS\Serializer\Annotation\Exclude;

/**
 * Show
 *
 * @ORM\Table(name="acts_shows")
 * @ORM\Entity
 */
class Show extends Entity
{
    /**
     * @var string
     *
     * @ORM\Column(name="dates", type="string", length=255, nullable=false)
     */
    private $dates;

    /**
     * @var string
     *
     * @ORM\Column(name="author", type="string", length=255, nullable=false)
     */
    private $author;

    /**
     * @var string
     *
     * @ORM\Column(name="prices", type="string", length=255, nullable=false)
     */
    private $prices;

    /**
     * @var string
     *
     * @ORM\Column(name="photourl", type="text", nullable=true)
     */
    private $photo_url;

    /**
     * @var string
     *
     * @ORM\Column(name="venue", type="string", length=255, nullable=false)
     */
    private $venue_name;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="excludedate", type="date", length=255, nullable=false)
     */
    private $exclude_date;

    /**
     * @var string
     *
     * @ORM\Column(name="society", type="string", length=255, nullable=true)
     */
    private $society_name;

    /**
     * @var boolean
     *
     * @ORM\Column(name="techsend", type="boolean", nullable=false)
     */
    private $tech_send;

    /**
     * @var boolean
     *
     * @ORM\Column(name="actorsend", type="boolean", nullable=false)
     */
    private $actor_send;

    /**
     * @var string
     *
     * @ORM\Column(name="audextra", type="text", nullable=true)
     */
    private $audextra;

    /**
     * @var Society
     *
     * @ORM\ManyToOne(targetEntity="Society", inversedBy="shows")
     * @Exclude
     * @ORM\JoinColumn(name="socid", referencedColumnName="id")
     */
    private $society;

    /**
     * @var Venue
     *
     * @ORM\ManyToOne(targetEntity="Venue", inversedBy="shows")
     * @Exclude
     * @ORM\JoinColumn(name="venid", referencedColumnName="id")
     */
    private $venue;

    /**
     * @var integer
     *
     * @ORM\Column(name="authorizeid", type="integer", nullable=false)
     */
    private $authorize_id;

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
    private $entry_expiry;

    /**
     * @var string
     *
     * @ORM\Column(name="category", type="string", length=255, nullable=false)
     */
    private $category;

    /**
     * @var string
     *
     * @ORM\Column(name="bookingcode", type="string", length=255, nullable=false)
     */
    private $booking_code;

    /**
     * @var integer
     *
     * @ORM\Column(name="primaryref", type="integer", nullable=false)
     */
    private $primary_ref;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="timestamp", type="datetime", nullable=false)
     */
    private $timestamp;

    /**
     *
     * @ORM\OneToMany(targetEntity="Role", mappedBy="show")
     * @ORM\OrderBy({"type" = "ASC", "order" = "ASC"})
     */
    private $roles;

    /**
     * @var array
     *
     * @ORM\OneToMany(targetEntity="Performance", mappedBy="show")
     * @ORM\OrderBy({"start_date" = "ASC"})
     * @Exclude
     */
    private $performances;

    /**
     * Set dates
     *
     * @param string $dates
     * @return Show
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
     * Set author
     *
     * @param string $author
     * @return Show
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
     * @return Show
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
     * Set photo_url
     *
     * @param string $photoUrl
     * @return Show
     */
    public function setPhotoUrl($photoUrl)
    {
        $this->photo_url = $photoUrl;
    
        return $this;
    }

    /**
     * Get photo_url
     *
     * @return string 
     */
    public function getPhotoUrl()
    {
        return $this->photo_url;
    }



    /**
     * Set venue_name
     *
     * @param string $venueName
     * @return Show
     */
    public function setVenueName($venueName)
    {
        $this->venue_name = $venueName;
    
        return $this;
    }

    /**
     * Get venue_name
     *
     * @return string 
     */
    public function getVenueName()
    {
        return $this->venue_name;
    }

    /**
     * Set exclude_date
     *
     * @param \DateTime $excludeDate
     * @return Show
     */
    public function setExcludeDate($excludeDate)
    {
        $this->exclude_date = $excludeDate;
    
        return $this;
    }

    /**
     * Get exclude_date
     *
     * @return \DateTime 
     */
    public function getExcludeDate()
    {
        return $this->exclude_date;
    }

    /**
     * Set society_name
     *
     * @param string $societyName
     * @return Show
     */
    public function setSocietyName($societyName)
    {
        $this->society_name = $societyName;
    
        return $this;
    }

    /**
     * Get society_name
     *
     * @return string 
     */
    public function getSocietyName()
    {
        return $this->society_name;
    }

    /**
     * Set tech_send
     *
     * @param boolean $techSend
     * @return Show
     */
    public function setTechSend($techSend)
    {
        $this->tech_send = $techSend;
    
        return $this;
    }

    /**
     * Get tech_send
     *
     * @return boolean 
     */
    public function getTechSend()
    {
        return $this->tech_send;
    }

    /**
     * Set actor_send
     *
     * @param boolean $actorSend
     * @return Show
     */
    public function setActorSend($actorSend)
    {
        $this->actor_send = $actorSend;
    
        return $this;
    }

    /**
     * Get actor_send
     *
     * @return boolean 
     */
    public function getActorSend()
    {
        return $this->actor_send;
    }

    /**
     * Set audextra
     *
     * @param string $audextra
     * @return Show
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
     * Set authorize_id
     *
     * @param integer $authorizeId
     * @return Show
     */
    public function setAuthorizeId($authorizeId)
    {
        $this->authorize_id = $authorizeId;
    
        return $this;
    }

    /**
     * Get authorize_id
     *
     * @return integer 
     */
    public function getAuthorizeId()
    {
        return $this->authorize_id;
    }

    /**
     * Set entered
     *
     * @param boolean $entered
     * @return Show
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
     * Set entry_expiry
     *
     * @param \DateTime $entryExpiry
     * @return Show
     */
    public function setEntryExpiry($entryExpiry)
    {
        $this->entry_expiry = $entryExpiry;
    
        return $this;
    }

    /**
     * Get entry_expiry
     *
     * @return \DateTime 
     */
    public function getEntryExpiry()
    {
        return $this->entry_expiry;
    }

    /**
     * Set category
     *
     * @param string $category
     * @return Show
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
     * Set booking_code
     *
     * @param string $bookingCode
     * @return Show
     */
    public function setBookingCode($bookingCode)
    {
        $this->booking_code = $bookingCode;
    
        return $this;
    }

    /**
     * Get booking_code
     *
     * @return string 
     */
    public function getBookingCode()
    {
        return $this->booking_code;
    }

    /**
     * Set primary_ref
     *
     * @param integer $primaryRef
     * @return Show
     */
    public function setPrimaryRef($primaryRef)
    {
        $this->primary_ref = $primaryRef;
    
        return $this;
    }

    /**
     * Get primary_ref
     *
     * @return integer 
     */
    public function getPrimaryRef()
    {
        return $this->primary_ref;
    }

    /**
     * Set timestamp
     *
     * @param \DateTime $timestamp
     * @return Show
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
     * Set venue
     *
     * @param \Acts\CamdramBundle\Entity\Venue $venue
     * @return Show
     */
    public function setVenue(\Acts\CamdramBundle\Entity\Venue $venue = null)
    {
        $this->venue = $venue;
    
        return $this;
    }

    /**
     * Get venue
     *
     * @return \Acts\CamdramBundle\Entity\Venue 
     */
    public function getVenue()
    {
        return $this->venue;
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

    public function getRolesByType($type)
    {
        if (!is_string($type)) {
            throw new \InvalidArgumentException('The service name given to Show::getRolesByType() must be a string');
        }

        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq("type", $type))

        ;
        return $this->getRoles()->matching($criteria);
    }

    /**
     * Set society
     *
     * @param \Acts\CamdramBundle\Entity\Society $society
     * @return Show
     */
    public function setSociety(\Acts\CamdramBundle\Entity\Society $society = null)
    {
        $this->society = $society;
    
        return $this;
    }

    /**
     * Get society
     *
     * @return \Acts\CamdramBundle\Entity\Society 
     */
    public function getSociety()
    {
        return $this->society;
    }

    /**
     * Add performances
     *
     * @param \Acts\CamdramBundle\Entity\Performance $performances
     * @return Show
     */
    public function addPerformance(\Acts\CamdramBundle\Entity\Performance $performances)
    {
        $this->performances[] = $performances;
    
        return $this;
    }

    /**
     * Remove performances
     *
     * @param \Acts\CamdramBundle\Entity\Performance $performances
     */
    public function removePerformance(\Acts\CamdramBundle\Entity\Performance $performances)
    {
        $this->performances->removeElement($performances);
    }

    /**
     * Get performances
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPerformances()
    {
        return $this->performances;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->roles = new \Doctrine\Common\Collections\ArrayCollection();
        $this->performances = new \Doctrine\Common\Collections\ArrayCollection();
    }

}