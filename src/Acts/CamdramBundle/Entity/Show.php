<?php

namespace Acts\CamdramBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Criteria;

use JMS\Serializer\Annotation as Serializer;

/**
 * Show
 *
 * @ORM\Table(name="acts_shows")
 * @ORM\Entity(repositoryClass="Acts\CamdramBundle\Entity\ShowRepository")
 */
class Show extends Entity
{
    /**
     * @var string
     *
     * @ORM\Column(name="dates", type="string", length=255, nullable=false)
     */
    private $dates = '';

    /**
     * @var string
     *
     * @ORM\Column(name="author", type="string", length=255, nullable=true)
     */
    private $author;

    /**
     * @var string
     *
     * @ORM\Column(name="prices", type="string", length=255, nullable=true)
     */
    private $prices = '';

    /**
     * @var string
     *
     * @ORM\Column(name="photourl", type="text", nullable=true)
     * @Serializer\Exclude
     */
    private $photo_url = '';

    /**
     * @var string
     *
     * @ORM\Column(name="venue", type="string", length=255, nullable=true)
     */
    private $venue_name = '';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="excludedate", type="date", length=255, nullable=true)
     * @Serializer\Exclude
     */
    private $exclude_date;

    /**
     * @var string
     *
     * @ORM\Column(name="society", type="string", length=255, nullable=true)
     */
    private $society_name = '';

    /**
     * @var boolean
     *
     * @ORM\Column(name="techsend", type="boolean", nullable=false)
     * @Serializer\Exclude
     */
    private $tech_send = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="actorsend", type="boolean", nullable=false)
     * @Serializer\Exclude
     */
    private $actor_send = false;

    /**
     * @var string
     *
     * @ORM\Column(name="audextra", type="text", nullable=true)
     * @Serializer\Exclude
     */
    private $audextra;

    /**
     * @var Society
     *
     * @ORM\ManyToOne(targetEntity="Society", inversedBy="shows")
     * @Serializer\Exclude
     * @ORM\JoinColumn(name="socid", referencedColumnName="id")
     */
    private $society;

    /**
     * @var Venue
     *
     * @ORM\ManyToOne(targetEntity="Venue", inversedBy="shows")
     * @Serializer\Exclude
     * @ORM\JoinColumn(name="venid", referencedColumnName="id")
     */
    private $venue;

    /**
     * @var integer
     *
     * @ORM\Column(name="authorizeid", type="integer", nullable=true)
     */
    private $authorize_id;

    /**
     * @var boolean
     *
     * @ORM\Column(name="entered", type="boolean", nullable=false)
     */
    private $entered = 1;

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
     * @ORM\Column(name="bookingcode", type="string", length=255, nullable=true)
     */
    private $booking_code;

    /**
     * @var integer
     *
     * @ORM\Column(name="primaryref", type="integer", nullable=true)
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
     * @Serializer\Exclude
     */
    private $roles;

    /**
     * @var array
     *
     * @ORM\OneToMany(targetEntity="Performance", mappedBy="show", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\OrderBy({"start_date" = "ASC"})
     * @Serializer\Exclude
     */
    private $performances;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_at", type="datetime", nullable=true)
     */
    private $start_at;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end_at", type="datetime", nullable=true)
     */
    private $end_at;

    /**
     * @var string
     *
     * @ORM\Column(name="freebase_id", type="string", nullable=true)
     */
    private $freebase_id;

    protected $entity_type = 'show';

    private $multi_venue;

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
        if ($this->venue_name) {
            return $this->venue_name;
        }
        elseif ($this->venue) {
            return $this->venue->getName();
        }
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
            ->orderBy(array('order' => 'ASC'))

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
        parent::__construct();
        $this->roles = new \Doctrine\Common\Collections\ArrayCollection();
        $this->performances = new \Doctrine\Common\Collections\ArrayCollection();
        $this->entry_expiry = new \DateTime;
        $this->timestamp = new \DateTime;
    }

    public function getEntityType()
    {
        return 'show';
    }

    /**
     * Set start_at
     *
     * @param \DateTime $startAt
     * @return Show
     */
    public function setStartAt($startAt)
    {
        $this->start_at = $startAt;
    
        return $this;
    }

    /**
     * Get start_at
     *
     * @return \DateTime 
     */
    public function getStartAt()
    {
        return $this->start_at;
    }

    /**
     * Set end_at
     *
     * @param \DateTime $endAt
     * @return Show
     */
    public function setEndAt($endAt)
    {
        $this->end_at = $endAt;
    
        return $this;
    }

    /**
     * Get end_at
     *
     * @return \DateTime 
     */
    public function getEndAt()
    {
        return $this->end_at;
    }

    /**
     * Add time_periods
     *
     * @param \Acts\CamdramBundle\Entity\TimePeriod $timePeriods
     * @return Show
     */
    public function addTimePeriod(\Acts\CamdramBundle\Entity\TimePeriod $timePeriods)
    {
        $this->time_periods[] = $timePeriods;
    
        return $this;
    }

    /**
     * Remove time_periods
     *
     * @param \Acts\CamdramBundle\Entity\TimePeriod $timePeriods
     */
    public function removeTimePeriod(\Acts\CamdramBundle\Entity\TimePeriod $timePeriods)
    {
        $this->time_periods->removeElement($timePeriods);
    }

    /**
     * Get time_periods
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTimePeriods()
    {
        return $this->time_periods;
    }

    public function fixPerformanceExcludes()
    {
        foreach ($this->getPerformances() as $performance) {
            /** @var $performance \Acts\CamdramBundle\Entity\Performance */
            if ($performance->getExcludeDate()) {
                if ($performance->getStartDate() > $performance->getExcludeDate() || $performance->getEndDate() < $performance->getExcludeDate()) {
                    $performance->setExcludeDate(null);
                }
                else {
                    $p2 = clone $performance;
                    $start = clone $p2->getExcludeDate();
                    $start->add(new \DateInterval('P1D'));
                    $p2->setStartDate($start);
                    $p2->setExcludeDate(null);
                    $this->performances[] = $p2;

                    $end = clone $performance->getExcludeDate();
                    $end->sub(new \DateInterval('P1D'));
                    $performance->setEndDate($end);
                    $performance->setExcludeDate(null);
                }
            }
        }
    }

    public function getMultiVenue()
    {
        if ($this->multi_venue) return $this->multi_venue;

        if (count($this->getPerformances()) == 0) return 'single';

        $venue = null;
        $same = true;
        foreach ($this->getPerformances() as $performance) {
            if ($performance->getVenue()) $cur_venue = $performance->getVenue()->getName();
            else $cur_venue = $performance->getVenueName();
            if ($venue == null) {
                $venue = $cur_venue;
            }
            else if ($venue != $cur_venue) {
                $same = false;
                break;
            }
        }
        return $same ? 'single' : 'multi';
    }

    public function setMultiVenue($value)
    {
        $this->multi_venue = $value;
    }

    public function updateVenues()
    {
        //If it's a multi-venue show, set the main venue (and vice-versa)
        switch ($this->getMultiVenue()) {
            case 'single':
                foreach ($this->getPerformances() as $performance) {
                    $performance->setVenue($this->getVenue());
                    $performance->setVenueName($this->getVenueName());
                }
                break;
            case 'multi':
                //Try to work out the 'main' venue
                //First count venue objects and venue names
                $venues = array();
                $venue_counts = array();
                $name_counts = array();
                foreach ($this->getPerformances() as $performance) {
                    if ($performance->getVenue()) {
                        $key = $performance->getVenue()->getId();
                        if (!isset($venue_counts[$key])) $venue_counts[$key] = 1;
                        else $venue_counts[$key]++;
                        $venues[$key] = $performance->getVenue();
                    }
                    if ($performance->getVenueName()) {
                        $key = $performance->getVenueName();
                        if (!isset($name_counts[$key])) $name_counts[$key] = 1;
                        else $name_counts[$key]++;
                    }
                    //Favour a venue object over a venue name
                    if (count($venue_counts) > 0) {
                        $venue_id = array_search(max($venue_counts), $venue_counts);
                        $this->setVenue($venues[$venue_id]);
                    }
                    else {
                        $venue_name = array_search(max($name_counts), $name_counts);
                        $this->setVenueName($venue_name);
                    }
                }
                break;
        }
    }

    public function updateTimes()
    {
        $min = null;
        $max = null;
        foreach ($this->getPerformances() as $performance) {
            if (is_null($min) || $performance->getStartDate() < $min) $min = $performance->getStartDate();
            if (is_null($max) || $performance->getEndDate() > $max) $max = $performance->getEndDate();
        }
        $this->setStartAt($min);
        $this->setEndAt($max);
    }

    /**
     * A ranking used by the autocomplete index
     * For shows, return the Unix timestamp of the show's start date
     *
     * @return int
     */
    public function getRank()
    {
        if ($this->getStartAt()) {
            return $this->getStartAt()->format('U');
        }
        else {
            return 0;
        }

    }


    /**
     * Set freebase_id
     *
     * @param string $freebaseId
     * @return Show
     */
    public function setFreebaseId($freebaseId)
    {
        $this->freebase_id = $freebaseId;

        return $this;
    }

    /**
     * Get freebase_id
     *
     * @return string 
     */
    public function getFreebaseId()
    {
        return $this->freebase_id;
    }

    public function isIndexable()
    {
        return $this->getAuthorizeId() !== null;
    }
}
