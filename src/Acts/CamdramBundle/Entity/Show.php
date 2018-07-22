<?php

namespace Acts\CamdramBundle\Entity;

use Acts\CamdramBundle\Search\SearchableInterface;
use Acts\CamdramSecurityBundle\Security\OwnableInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Criteria;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as Serializer;
use Acts\CamdramSecurityBundle\Entity\User;
use Acts\CamdramApiBundle\Configuration\Annotation as Api;

/**
 * Show
 *
 * @ORM\Table(name="acts_shows", uniqueConstraints={@ORM\UniqueConstraint(name="show_slugs",columns={"slug"})})
 * @ORM\Entity(repositoryClass="Acts\CamdramBundle\Entity\ShowRepository")
 * @ORM\EntityListeners({"Acts\CamdramBundle\EventListener\ShowListener"})
 * @Serializer\ExclusionPolicy("all")
 * @Serializer\XmlRoot("show")
 * @Gedmo\Loggable
 * @Api\Feed(name="Camdram - Shows", titleField="name",
 *   description="Shows produced by students in Cambridge",
 *   template="show/rss.html.twig")
 * @Api\Link(route="get_show", params={"identifier": "object.getSlug()"})
 */
class Show implements OwnableInterface
{
    /**
     * The show's ID
     *
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Serializer\XmlAttribute
     * @Serializer\Expose()
     * @Serializer\Type("integer")
     */
    private $id;

    /**
     * The show's name
     *
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=false)
     * @Assert\NotBlank()
     * @Gedmo\Versioned
     * @Serializer\Expose()
     * @Serializer\Type("string")
     * @Serializer\XmlElement(cdata=false)
     */
    private $name;

    /**
     * A description of the show
     *
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     * @Gedmo\Versioned
     * @Serializer\Expose()
     * @Serializer\Type("string")
     */
    private $description;

    /**
     * @var Image
     *
     * @ORM\ManyToOne(targetEntity="Image")
     * @Gedmo\Versioned
     */
    private $image;

    /**
     * @var int
     *
     * @ORM\Column(name="facebook_id", type="string", length=50, nullable=true)
     * @Gedmo\Versioned
     */
    private $facebook_id;

    /**
     * @var int
     *
     * @ORM\Column(name="twitter_id", type="string", length=50, nullable=true)
     * @Gedmo\Versioned
     */
    private $twitter_id;

    /**
     * The 'slug' of the show (used to generate the URL)
     *
     * @Gedmo\Slug(handlers={
     *      @Gedmo\SlugHandler(class="Acts\CamdramBundle\Service\DateSlugHandler", options={
     *          @Gedmo\SlugHandlerOption(name="dateField", value="start_at"),
     *      })
     * }, fields={"name"})
     * @ORM\Column(name="slug", type="string", length=128, nullable=true)
     * @Serializer\Expose
     * @Serializer\Type("string")
     * @Serializer\XmlElement(cdata=false)
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="dates", type="string", length=255, nullable=false)
     */
    private $dates = '';

    /**
     * The show's author (free text string, which may be blank or contain one or more author names)
     *
     * @var string
     *
     * @ORM\Column(name="author", type="string", length=255, nullable=true)
     * @Gedmo\Versioned
     * @Serializer\Expose()
     * @Serializer\Type("string")
     * @Serializer\XmlElement(cdata=false)
     */
    private $author;

    /**
     * A string representing the ticket price options
     *
     * @var string
     *
     * @ORM\Column(name="prices", type="string", length=255, nullable=true)
     * @Gedmo\Versioned
     * @Serializer\Expose()
     * @Serializer\Type("string")
     * @Serializer\XmlElement(cdata=false)
     */
    private $prices = '';

    /**
     * @var string
     *
     * @ORM\Column(name="photourl", type="text", nullable=true)
     */
    private $photo_url = '';

    /**
     * The show's main venue, if it is not linked to a venue resource
     *
     * @var string
     *
     * @ORM\Column(name="venue", type="string", length=255, nullable=true)
     * @Gedmo\Versioned
     * @Serializer\Expose()
     * @Serializer\Type("string")
     * @Serializer\XmlElement(cdata=false)
     */
    private $other_venue = '';

    /**
     * The show's society, if it is not linked to a venue resource
     *
     * @var string
     *
     * @ORM\Column(name="society", type="string", length=255, nullable=true)
     * @Gedmo\Versioned
     * @Serializer\Expose()
     * @Serializer\Type("string")
     * @Serializer\XmlElement(cdata=false)
     */
    private $other_society = '';

    /**
     * @var bool
     *
     * @ORM\Column(name="techsend", type="boolean", nullable=false)
     */
    private $tech_send = false;

    /**
     * @var bool
     *
     * @ORM\Column(name="actorsend", type="boolean", nullable=false)
     */
    private $actor_send = false;

    /**
     * @var string
     *
     * @ORM\Column(name="audextra", type="text", nullable=true)
     * @Gedmo\Versioned
     */
    private $audextra;

    /**
     * @var Society
     *
     * @ORM\ManyToOne(targetEntity="Society", inversedBy="shows")
     * @ORM\JoinColumn(name="socid", referencedColumnName="id", onDelete="SET NULL")
     * @Gedmo\Versioned
     * @Api\Link(embed=true, route="get_society", params={"identifier": "object.getSociety().getSlug()"})
     */
    private $society;

    /**
     * @var Venue
     *
     * @ORM\ManyToOne(targetEntity="Venue", inversedBy="shows")
     * @ORM\JoinColumn(name="venid", referencedColumnName="id", onDelete="SET NULL")
     * @Gedmo\Versioned
     * @Api\Link(embed=true, route="get_venue", params={"identifier": "object.getVenue().getSlug()"})
     */
    private $venue;

    /**
     * @var int
     *
     * @ORM\ManyToOne(targetEntity="\Acts\CamdramSecurityBundle\Entity\User", inversedBy="authorised_shows")
     * @ORM\JoinColumn(name="authorizeid", referencedColumnName="id", nullable=true)
     * @Gedmo\Versioned
     */
    private $authorised_by;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="entryexpiry", type="date", nullable=false)
     */
    private $entry_expiry;

    /**
     * The show's genre (takes one of several predefined values)
     *
     * @var string
     *
     * @ORM\Column(name="category", type="string", length=255, nullable=false)
     * @Assert\NotBlank()
     * @Serializer\Expose
     * @Serializer\Type("string")
     * @Serializer\XmlElement(cdata=false)
     */
    private $category;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="timestamp", type="datetime", nullable=false)
     */
    private $timestamp;

    /**
     * @var array
     *
     * @ORM\OneToMany(targetEntity="TechieAdvert", mappedBy="show")
     */
    private $techie_adverts;

    /**
     * @var array
     *
     * @ORM\OneToMany(targetEntity="Audition", mappedBy="show", cascade={"all"}, orphanRemoval=true)
     */
    private $auditions;

    /**
     * @var array
     *
     * @ORM\OneToMany(targetEntity="Application", mappedBy="show")
     */
    private $applications;

    /**
     * @ORM\OneToMany(targetEntity="Role", mappedBy="show")
     * @ORM\OrderBy({"type" = "ASC", "order" = "ASC"})
     * @Api\Link(route="get_show_roles", params={"identifier": "object.getSlug()"})
     */
    private $roles;

    /**
     * @var array
     *
     * @ORM\OneToMany(targetEntity="Performance", mappedBy="show", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\OrderBy({"start_date" = "ASC"})
     * @Serializer\Expose()
     * @Serializer\XmlList(entry = "performance")
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

    private $multi_venue;

    /**
     * @var string
     * @ORM\Column(name="facebookurl", type="string", length=2083, nullable=true)
     */
    private $facebook_url;
    /**
     * @var string
     * @ORM\Column(name="otherurl", type="string", length=2083, nullable=true)
     */
    private $other_url;

    /**
     * A URL from which tickets for the show can be bought
     *
     * @var string
     * @Assert\Url()
     * @Gedmo\Versioned
     * @Serializer\Expose
     * @ORM\Column(name="onlinebookingurl", type="string", length=2083, nullable=true)
     * @Serializer\Expose
     * @Serializer\Type("string")
     * @Serializer\XmlElement(cdata=false)
     */
    private $online_booking_url;

    private $weeks = array();

    private $entity_type = 'show';

    public function getEntityType()
    {
        return $this->entity_type;
    }

    /**
     * Set dates
     *
     * @param string $dates
     *
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
     *
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
     *
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
     *
     * @return Show
     */
    public function setPhotoUrl($photoUrl)
    {
        $this->photo_url = $photoUrl;

        return $this;
    }

    /**
     * Get photoUrl
     *
     * @return string
     */
    public function getPhotoUrl()
    {
        return $this->photo_url;
    }

    /**
     * Set other_venue
     *
     * @param string $venueName
     *
     * @return Show
     */
    public function setOtherVenue($venueName)
    {
        $this->other_venue = $venueName;

        return $this;
    }

    public function getOtherVenue()
    {
        if (!$this->venue) {
            return $this->other_venue;
        } else {
            return null;
        }
    }

    /**
     * Get venue_name
     *
     * @return string
     */
    public function getVenueName()
    {
        if ($this->other_venue) {
            return $this->other_venue;
        } elseif ($this->venue) {
            return $this->venue->getName();
        }
    }

    public function getOtherSociety()
    {
        return $this->other_society;
    }

    /**
     * Set other_society
     *
     * @param string $societyName
     *
     * @return Show
     */
    public function setOtherSociety($societyName)
    {
        $this->other_society = $societyName;

        return $this;
    }

    public function getSocietyName()
    {
        if ($this->other_society) {
            return $this->other_society;
        } elseif ($this->society) {
            return $this->society->getName();
        }
    }

    /**
     * Set tech_send
     *
     * @param bool $techSend
     *
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
     * @return bool
     */
    public function getTechSend()
    {
        return $this->tech_send;
    }

    /**
     * Set actor_send
     *
     * @param bool $actorSend
     *
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
     * @return bool
     */
    public function getActorSend()
    {
        return $this->actor_send;
    }

    /**
     * Set audextra
     *
     * @param string $audextra
     *
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
     * Set entry_expiry
     *
     * @param \DateTime $entryExpiry
     *
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
     *
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
     * Set timestamp
     *
     * @param \DateTime $timestamp
     *
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
     *
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
     *
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

    /**
     * Get all roles of a given type (cast, production team or band)
     * associated with this show. The results are returned sorted by the
     * explicit ordering field followed by the primary key, i.e. the results
     * are returned in the explicit ordering or in the order they were entered
     * into the database.
     */
    public function getRolesByType($type)
    {
        if (!is_string($type)) {
            throw new \InvalidArgumentException('The service name given to Show::getRolesByType() must be a string');
        }

        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('type', $type))
            ->orderBy(array('order' => 'ASC', 'id' => 'ASC'))
        ;

        return $this->getRoles()->matching($criteria);
    }

    public function getRolesByPerson(Person $person)
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('person', $person))
            ->orderBy(array('order' => 'ASC', 'id' => 'ASC'))
        ;

        return $this->getRoles()->matching($criteria);
    }

    /**
     * Set society
     *
     * @param \Acts\CamdramBundle\Entity\Society $society
     *
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
     *
     * @return Show
     */
    public function addPerformance(\Acts\CamdramBundle\Entity\Performance $performance)
    {
        $this->performances->add($performance);
        $performance->setShow($this);
        if (!($performance->getOtherVenue())) {
            if ($this->getVenue()) {
                $performance->setVenue($this->getVenue());
            } else {
                $performance->setOtherVenue($this->getOtherVenue());
            }
        }

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
        $this->techie_adverts = new \Doctrine\Common\Collections\ArrayCollection();
        $this->auditions = new \Doctrine\Common\Collections\ArrayCollection();
        $this->applications = new \Doctrine\Common\Collections\ArrayCollection();
        $this->entry_expiry = new \DateTime();
        $this->timestamp = new \DateTime();
    }

    /**
     * Set start_at
     *
     * @param \DateTime $startAt
     *
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
     *
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

    public function fixPerformanceExcludes()
    {
        foreach ($this->getPerformances() as $performance) {
            /** @var $performance \Acts\CamdramBundle\Entity\Performance */
            if ($performance->getExcludeDate()) {
                if ($performance->getStartDate() > $performance->getExcludeDate() || $performance->getEndDate() < $performance->getExcludeDate()) {
                    $performance->setExcludeDate(null);
                } else {
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
        if ($this->multi_venue) {
            return $this->multi_venue;
        }

        if (count($this->getPerformances()) == 0) {
            return 'single';
        }

        $venue = null;
        $same = true;
        foreach ($this->getPerformances() as $performance) {
            if ($performance->getVenue()) {
                $cur_venue = $performance->getVenue()->getName();
            } else {
                $cur_venue = $performance->getVenueName();
            }
            if ($venue == null) {
                $venue = $cur_venue;
            } elseif ($venue != $cur_venue) {
                $same = false;
                break;
            }
        }

        return $same ? 'single' : 'multi';
    }

    public function setMultiVenue($value)
    {
        $this->multi_venue = $value;
        $this->updateVenues();
    }

    public function updateVenues()
    {
        switch ($this->getMultiVenue()) {
            case 'single':
                foreach ($this->getPerformances() as $performance) {
                    $performance->setVenue($this->getVenue());
                    $performance->setOtherVenue($this->getOtherVenue());
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
                        if (!isset($venue_counts[$key])) {
                            $venue_counts[$key] = 1;
                        } else {
                            $venue_counts[$key]++;
                        }
                        $venues[$key] = $performance->getVenue();
                    }
                    if ($performance->getOtherVenue()) {
                        $key = $performance->getOtherVenue();
                        if (!isset($name_counts[$key])) {
                            $name_counts[$key] = 1;
                        } else {
                            $name_counts[$key]++;
                        }
                    }
                    //Favour a venue object over a venue name
                    if (count($venue_counts) > 0) {
                        $venue_id = array_search(max($venue_counts), $venue_counts);
                        $this->setVenue($venues[$venue_id]);
                    } else {
                        $venue_name = array_search(max($name_counts), $name_counts);
                        $this->setOtherVenue($venue_name);
                    }
                }
                break;
        }
    }

    /**
        * A ranking used by the autocomplete index
        * For shows, return the Unix timestamp of the show's start date
        *
        * @return int
        */
    public function getRank()
    {
        return $this->start_at ? $this->start_at->format('Ymd') : 0;
    }


    /**
     * Set freebase_id
     *
     * @param string $freebaseId
     *
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
        return $this->getAuthorisedBy() !== null;
    }

    /**
     * Add techie_adverts
     *
     * @param \Acts\CamdramBundle\Entity\TechieAdvert $techieAdverts
     *
     * @return Show
     */
    public function addTechieAdvert(\Acts\CamdramBundle\Entity\TechieAdvert $techieAdverts)
    {
        $this->techie_adverts[] = $techieAdverts;

        return $this;
    }

    /**
     * Remove techie_adverts
     *
     * @param \Acts\CamdramBundle\Entity\TechieAdvert $techieAdverts
     */
    public function removeTechieAdvert(\Acts\CamdramBundle\Entity\TechieAdvert $techieAdverts)
    {
        $this->techie_adverts->removeElement($techieAdverts);
    }

    /**
     * Get techie_adverts
     *
     * @return TechieAdvert
     * @Api\Link(route="get_techie", name="techie_advert", params={"identifier": "object.getSlug()"},
     *      targetType="techie_advert")
     */
    public function getActiveTechieAdvert()
    {
        $now = new \DateTime();
        $today = new \DateTime($now->format('Y-m-d'));
        $criteria = Criteria::create()
            ->where(Criteria::expr()->gt('expiry', $today))
            ->orWhere(Criteria::expr()->andX(
                Criteria::expr()->gte('expiry', $today),
                Criteria::expr()->gt('deadlineTime', $now)
            ));

        return $this->techie_adverts->matching($criteria)->first();
    }

    /**
     * Get techie_adverts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTechieAdverts()
    {
        return $this->techie_adverts;
    }

    /**
     * Add auditions
     *
     * @param \Acts\CamdramBundle\Entity\Audition $auditions
     *
     * @return Show
     */
    public function addAudition(\Acts\CamdramBundle\Entity\Audition $auditions)
    {
        $this->auditions[] = $auditions;

        return $this;
    }

    /**
     * Remove auditions
     *
     * @param \Acts\CamdramBundle\Entity\Audition $auditions
     */
    public function removeAudition(\Acts\CamdramBundle\Entity\Audition $auditions)
    {
        $this->auditions->removeElement($auditions);
    }

    public function mergeAuditions($auditions)
    {
        foreach ($auditions as $audition) {
            $audition->setShow($this);
            if (!$audition->getId()) {
                $this->addAudition($audition);
            } else {
                foreach ($this->auditions as $k => $a) {
                    if ($a->getId() == $audition->getId()) {
                        $this->auditions[$k] = $audition;
                        break;
                    }
                }
            }
        }
    }

    /**
     * Get auditions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAuditions()
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('display', 0))
            ->andWhere(Criteria::expr()->gte('date', new \DateTime()));

        return $this->auditions->matching($criteria);
    }

    public function getScheduledAuditions()
    {
        $criteria = Criteria::create()
            ->andWhere(Criteria::expr()->gte('date', new \DateTime()))
            ->andWhere(Criteria::expr()->eq('nonScheduled', false));

        return $this->auditions->matching($criteria);
    }

    public function setScheduledAuditions($auditions)
    {
        foreach ($this->getScheduledAuditions() as $k => $audition) {
            $found = false;
            foreach ($auditions as $a) {
                if ($audition->getId() == $a->getId()) {
                    $this->auditions[$k] = $a;
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $this->auditions->remove($k);
            }
        }

        foreach ($auditions as &$audition) {
            if (!$audition->getId()) {
                $audition->setShow($this);
                $audition->setNonScheduled(false);
                $this->addAudition($audition);
            }
        }

        return $this;
    }

    public function getNonScheduledAuditions()
    {
        $criteria = Criteria::create()
            ->andWhere(Criteria::expr()->eq('nonScheduled', true));

        return $this->auditions->matching($criteria);
    }

    public function setNonScheduledAuditions($auditions)
    {
        foreach ($this->getNonScheduledAuditions() as $k => $audition) {
            $found = false;
            foreach ($auditions as $a) {
                if ($audition->getId() == $a->getId()) {
                    $this->auditions[$k] = $a;
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $this->auditions->remove($k);
            }
        }

        foreach ($auditions as &$audition) {
            if (!$audition->getId()) {
                $audition->setShow($this);
                $audition->setNonScheduled(true);
                $this->addAudition($audition);
            }
        }

        return $this;
    }

    /**
     * Add applications
     *
     * @param \Acts\CamdramBundle\Entity\Application $applications
     *
     * @return Show
     */
    public function addApplication(\Acts\CamdramBundle\Entity\Application $applications)
    {
        $this->applications[] = $applications;

        return $this;
    }

    /**
     * Remove applications
     *
     * @param \Acts\CamdramBundle\Entity\Application $applications
     */
    public function removeApplication(\Acts\CamdramBundle\Entity\Application $applications)
    {
        $this->applications->removeElement($applications);
    }

    /**
     * Get applications
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getApplications()
    {
        return $this->applications;
    }

    /**
     * Get active applications
     *
     * @return Application
     * @Api\Link(route="get_application", name="application", params={"identifier": "object.getSlug()"},
     *      targetType="application")
     */
    public function getActiveApplication()
    {
        $now = new \DateTime();
        $today = new \DateTime($now->format('Y-m-d'));
        $criteria = Criteria::create()
            ->where(Criteria::expr()->gt('deadlineDate', $today))
            ->orWhere(Criteria::expr()->andX(
                Criteria::expr()->gte('deadlineDate', $today),
                Criteria::expr()->gt('deadlineTime', $now)
            ));

        return $this->applications->matching($criteria)->first();
    }

    public function hasVacancies()
    {
        return $this->getActiveTechieAdvert()
                || count($this->getAuditions()) > 0
                || $this->getActiveApplication();
    }

    /**
     * Set authorised_by
     *
     * @param \Acts\CamdramSecurityBundle\Entity\User $authorisedBy
     *
     * @return Show
     */
    public function setAuthorisedBy(User $authorisedBy = null)
    {
        $this->authorised_by = $authorisedBy;

        return $this;
    }

    /**
     * Get authorised_by
     *
     * @return \Acts\CamdramSecurityBundle\Entity\User
     */
    public function getAuthorisedBy()
    {
        return $this->authorised_by;
    }

    public function isAuthorised()
    {
        return $this->getAuthorisedBy() !== null;
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
     * @return Show
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
     * Set description
     *
     * @param string $description
     *
     * @return Show
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
     * Set facebook_id
     *
     * @param string $facebookId
     *
     * @return Show
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
     * @return Show
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
     * @return Show
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
     * Set slug
     *
     * @param string $slug
     *
     * @return Show
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
     * Set facebook_url
     *
     * @param string $facebookUrl
     *
     * @return Show
     */
    public function setFacebookUrl($facebookUrl)
    {
        $this->facebook_url = $facebookUrl;

        return $this;
    }

    public function getFacebookUrl()
    {
        return 'http://www.facebook.com/'.$this->getFacebookId();
    }

    public function getTwitterUrl()
    {
        return 'https://twitter.com/intent/user?user_id='.$this->getTwitterId();
    }

    /**
     * Set other_url
     *
     * @param string $otherUrl
     *
     * @return Show
     */
    public function setOtherUrl($otherUrl)
    {
        $this->other_url = $otherUrl;

        return $this;
    }

    /**
     * Get other_url
     *
     * @return string
     */
    public function getOtherUrl()
    {
        return $this->other_url;
    }

    /**
     * Set online_booking_url
     *
     * @param string $onlineBookingUrl
     *
     * @return Show
     */
    public function setOnlineBookingUrl($onlineBookingUrl)
    {
        $this->online_booking_url = $onlineBookingUrl;

        return $this;
    }

    /**
     * Get online_booking_url
     *
     * @return string
     */
    public function getOnlineBookingUrl()
    {
        return $this->online_booking_url;
    }

    /**

     */
    public function getAllPerformances()
    {
        $ret = array();
        foreach ($this->getPerformances() as $performance) {
            $current_day = clone $performance->getStartDate(); //ate'] . " " . $perf['time']);
            $end_day = $performance->getEndDate(); //ate'] . " " . $perf['time']);
            $exclude = $performance->getExcludeDate();
            $time = $performance->getTime();
            if ($performance->getVenue() != null) {
                $venue = $performance->getVenue()->getName();
            } else {
                $venue = $performance->getOtherVenue();
            }
            while ($current_day <= $end_day) {
                if ($current_day != $exclude) {
                    $datetime = clone $current_day;

                    $datetime->setTime($time->format('G'), $time->format('i'), $time->format('s')); //  Eugh. PHP doesn't seem to give a better way
                    array_push($ret, array('date' => $current_day, 'time' => $time, 'datetime' => $datetime, 'venue' => $venue));
                }
                $current_day = clone $current_day;
                $current_day->modify('+1 day');
            }
        }
        usort($ret, array($this, 'cmpPerformances'));

        return $ret;
    }

    /**
     * compare two performance objects, returning -1 if $a is before $b, 1 if
     * it's after, or 0 if they're at the same time.
     * Used by getAllPerformances()
     */
    private function cmpPerformances($a, $b)
    {
        if ($a['date'] < $b['date']) {
            return -1;
        } elseif ($a['date'] > $b['date']) {
            return 1;
        } elseif ($a['time'] < $b['time']) {
            return -1;
        } elseif ($a['time'] > $b['time']) {
            return 1;
        } else {
            return 0;
        }
    }

    public static function getAceType()
    {
        return 'show';
    }

    public function getShortName()
    {
        return '';
    }

    /**
     * Required for RSS feed
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->getTimestamp();
    }

    /**
     * Required for RSS feed
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->getTimestamp();
    }

    public function hasFuturePerformances()
    {
        $future = false;
        $now = new \DateTime();
        foreach ($this->getPerformances() as $performance) {
            if ($performance->getEndDate() >= $now) {
                $future = true;
                break;
            }
        }

        return $future;
    }

    public function isArchived()
    {
        $archived = true;
        $now = new \DateTime();
        foreach ($this->getPerformances() as $performance) {
            if ($performance->getStartDate()->modify('+1 year') >= $now) {
                $archived = false;
                break;
            }
        }

        return $archived;
    }

    public function getWeeks()
    {
        return $this->weeks;
    }

    public function setWeeks($weeks)
    {
        $this->weeks = $weeks;
    }
}
