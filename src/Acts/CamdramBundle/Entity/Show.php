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
     * @Serializer\Expose()
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
     *      @Gedmo\SlugHandler(class="Acts\CamdramBundle\Service\SlugHandler", options={
     *          @Gedmo\SlugHandlerOption(name="dateField", value="start_at"),
     *      })
     * }, fields={"name"})
     * @ORM\Column(name="slug", type="string", length=128, nullable=false)
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
     * A JSON representation of how the show's societies should be displayed,
     * for the purpose of storing unregistered societies and how they are
     * ordered with registered societies.
     * NOT used for access control etc.
     * ["New Society", 12] might be rendered as
     *     New Society and Cambridge Footlights present...
     * assuming the Footlights have id 12.
     *
     * @var array
     *
     * @ORM\Column(name="socs_list", type="json_array", nullable=false)
     * @Gedmo\Versioned
     */
    private $societies_display_list = [];

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
     * All the registered scieties involved with this show.
     * @ORM\ManyToMany(targetEntity="Society", inversedBy="shows")
     * @ORM\JoinTable(name="acts_show_soc_link")
     */
    private $societies;

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
     * @var bool
     *
     * @ORM\Column(name="authorised", type="boolean")
     * @Gedmo\Versioned
     */
    private $authorised = false;

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
     * @ORM\OrderBy({"start_at" = "ASC"})
     * @Serializer\Expose()
     * @Serializer\XmlList(entry = "performance")
     */
    private $performances;

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

    /**
     * It's advisable to use getPrettySocData instead as it has the definitive
     * handling of inconsistencies between societies (i.e. the join table) and
     * this.
     */
    public function getSocietiesDisplayList()
    {
        return $this->societies_display_list;
    }

    /**
     * DEPRECATED and going away eventually. Returns the first society, if it
     * is registered.
     * @Api\Link(embed=true, name="society", route="get_society", params={"identifier": "object.getSocietyLegacy().getSlug()"})
     */
    public function getSocietyLegacy()
    {
        $data = $this->getPrettySocData();
        if (empty($data) || is_array($data[0])) { return; }
        return $data[0];
    }

    /**
     * DEPRECATED and going away eventually. Returns the first society name.
     * Historically afaict this could differ from getSociety()->getName();
     * as the other_society column has now been deleted, they now must be
     * consistent.
     * @Serializer\VirtualProperty()
     * @Serializer\Type("string")
     * @Serializer\XmlElement(cdata=false)
     * @Serializer\SerializedName("other_society")
     */
    public function getOtherSocietyLegacy()
    {
        $data = $this->getPrettySocData();
        return empty($data) ? NULL :
            (is_array($data[0]) ? $data[0]["name"] : $data[0]->getName());
    }

    /**
     * The correct way to access societies in the API.
     * @Serializer\VirtualProperty()
     * @Serializer\XmlKeyValuePairs()
     * @Serializer\SerializedName("societies")
     */
    public function getSocietiesForAPI()
    {
        $data = $this->getPrettySocData();
        return array_map(function($s) {
            return is_array($s) ? $s : ["id" => $s->getId(), "name" => $s->getName(), "slug" => $s->getSlug()];
        }, $data);
    }

    /**
     * Set societies_display_list
     *
     * @param string $societiesList
     *
     * @return Show
     */
    public function setSocietiesDisplayList($societiesList)
    {
        $this->societies_display_list = $societiesList;

        return $this;
    }

    /**
     * Gets all relevant data on societies ready for display to the user;
     * returns an array of arrays [ "name" => "Some Small Soc" ] or of Societies.
     * Uses societies_display_list for ordering but gives priority to the info
     * in societies.
     */
    public function getPrettySocData() {
        $data = $this->societies_display_list;
        $out = array();
        foreach ($data as $soc_basic) {
            if (is_string($soc_basic)) {
                $out[] = ["name" => $soc_basic];
            } else if (is_numeric($soc_basic)) {
                # is_numeric would return true for the STRING "1234", so the if
                # statements have to be in this order.
                foreach ($this->societies as $s) {
                    if ($s->getId() == $soc_basic) {
                        $out[] = $s;
                        break;
                    }
                }
            }
        }
        foreach ($this->societies as $society) {
            if (!in_array($society->getId(), $data, true)) {
                $out[] = $society;
            }
        }
        return $out;
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
     * Get societies
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSocieties()
    {
        return $this->societies;
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
        $this->applications = new \Doctrine\Common\Collections\ArrayCollection();
        $this->auditions    = new \Doctrine\Common\Collections\ArrayCollection();
        $this->performances = new \Doctrine\Common\Collections\ArrayCollection();
        $this->roles        = new \Doctrine\Common\Collections\ArrayCollection();
        $this->societies    = new \Doctrine\Common\Collections\ArrayCollection();
        $this->techie_adverts = new \Doctrine\Common\Collections\ArrayCollection();

        $this->entry_expiry = new \DateTime();
        $this->timestamp    = new \DateTime();

        $this->setSocietiesDisplayList([]);
    }

    /**
     * Get first performance time
     *
     * @return \DateTime
     */
    public function getStartAt()
    {
        $criteria = Criteria::create()
            ->orderBy(['start_at' => Criteria::ASC])
            ->setMaxResults(1)
            ;
        $performance = $this->performances->matching($criteria)->first();
        return $performance ? $performance->getStartAt() : null;
    }

    /**
     * Get last performance time
     *
     * @return \DateTime
     */
    public function getEndAt()
    {
        $criteria = Criteria::create()
            ->orderBy(['repeat_until' => Criteria::DESC])
            ->setMaxResults(1)
            ;
        $performance = $this->performances->matching($criteria)->first();
        return $performance ? $performance->getRepeatUntil() : null;
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
     * For shows, return the Ymd timestamp of the show's start date
     *
     * @return int
     */
    public function getRank()
    {
        $startAt = $this->getStartAt();
        return $startAt ? (int) $startAt->format('Ymd') : 0;
    }


    public function isIndexable()
    {
        return $this->getAuthorised();
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
        $criteria = Criteria::create()
            ->where(Criteria::expr()->gt('expiry', $now));

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
     * Get all auditions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAllAuditions() {
        return $this->auditions;
    }

    /**
     * Get auditions that are upcoming.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAuditions()
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('display', 0))
            ->andWhere(Criteria::expr()->orX(
                Criteria::expr()->gte('start_at', new \DateTime()),
                Criteria::expr()->gte('end_at', new \DateTime())
            ));

        return $this->auditions->matching($criteria);
    }

    public function getScheduledAuditions()
    {
        $criteria = Criteria::create()
            ->andWhere(Criteria::expr()->gte('end_at', new \DateTime()))
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
            ->andWhere(Criteria::expr()->eq('nonScheduled', true))
            ->andWhere(Criteria::expr()->gte('start_at', new \DateTime()))
            ;

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
     * Set authorised
     *
     * @param bool
     *
     * @return Show
     */
    public function setAuthorised(bool $authorised)
    {
        $this->authorised = $authorised;

        return $this;
    }

    /**
     * Get authorised
     *
     * @return bool
     */
    public function getAuthorised()
    {
        return $this->authorised;
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
     * Returns an array of arrays
     *   ["datetime" => a DateTime object, "venue" => string]
     * for every performance, i.e. 1 or many per Performance object.
     * null if there are too many (>1000). This is due to #617.
     */
    public function getAllPerformances(): ?array
    {
        // Time zone problems: we have to set all hours to be the same as the
        // first after converting to "Europe/London".
        // $current_day holds the current day of the iteration at noon, London time
        // $time is an array of [hour, minute, second], London time
        // $dateTimeOut is a combination of these and is what we actually return.
        $ret = array();
        foreach ($this->getPerformances() as $performance) {
            $first_day = clone $performance->getStartAt();
            $first_day->setTimezone(new \DateTimeZone("Europe/London"));
            $time = explode(':', $first_day->format('H:i:s'));
            $current_day = clone $first_day;
            $current_day->setTime(12, 0, 0);

            $end_day = clone $performance->getRepeatUntil();
            $end_day->setTime(23, 59, 59);
            if ($first_day->diff($end_day, true)->days > 1000) {
                return null;
            }
            if ($performance->getVenue() != null) {
                $venue = $performance->getVenue()->getName();
            } else {
                $venue = $performance->getOtherVenue();
            }
            while ($current_day <= $end_day) {
                $dateTimeOut = clone $current_day;
                $dateTimeOut->setTime(...$time);
                array_push($ret, ['datetime' => $dateTimeOut, 'venue' => $venue]);
                $current_day->modify('+1 day');
            }
        }
        usort($ret, function($a, $b) {
          return ($a['datetime']) <=> ($b['datetime']);
        });

        return $ret;
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
            if ($performance->getRepeatUntil() >= $now) {
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
            if ($performance->getStartAt()->modify('+1 year') >= $now) {
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
