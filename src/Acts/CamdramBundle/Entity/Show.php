<?php

namespace Acts\CamdramBundle\Entity;

use Acts\CamdramSecurityBundle\Security\OwnableInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
 * @ORM\Table(name="acts_shows", uniqueConstraints={@ORM\UniqueConstraint(name="show_slugs",columns={"slug"})},
 *      indexes={@ORM\Index(name="idx_show_fulltext", columns={"title", "slug"}, flags={"fulltext"})})
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
class Show extends BaseEntity implements OwnableInterface
{
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
     * @var ?string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     * @Gedmo\Versioned
     * @Serializer\Expose()
     * @Serializer\Type("string")
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
     * @var ?string
     *
     * @ORM\Column(name="facebook_id", type="string", length=50, nullable=true)
     * @Gedmo\Versioned
     */
    private $facebook_id;

    /**
     * @var ?string
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
     * @var ?string
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
     * @var ?string
     *
     * @ORM\Column(name="prices", type="string", length=255, nullable=true)
     * @Gedmo\Versioned
     * @Serializer\Expose()
     * @Serializer\Type("string")
     * @Serializer\XmlElement(cdata=false)
     */
    private $prices = '';

    /**
     * @var ?string
     *
     * @ORM\Column(name="photourl", type="text", nullable=true)
     */
    private $photo_url = '';

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
     * All the registered scieties involved with this show.
     * @ORM\ManyToMany(targetEntity="Society", inversedBy="shows")
     * @ORM\JoinTable(name="acts_show_soc_link")
     */
    private $societies;

    /**
     * @var bool
     *
     * @ORM\Column(name="authorised", type="boolean")
     * @Gedmo\Versioned
     */
    private $authorised = false;

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
     * @ORM\OneToMany(targetEntity="Advert", mappedBy="show", cascade={"all"}, orphanRemoval=true)
     */
    private $adverts;

    /**
     * @ORM\OneToMany(targetEntity="Role", mappedBy="show")
     * @ORM\OrderBy({"type" = "ASC", "order" = "ASC"})
     * @Api\Link(route="get_show_roles", params={"identifier": "object.getSlug()"})
     */
    private $roles;

    /**
     * @Assert\Valid(traverse=true)
     * @ORM\OneToMany(targetEntity="Performance", mappedBy="show", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\OrderBy({"start_at" = "ASC"})
     * @Serializer\Expose()
     * @Serializer\XmlList(entry = "performance")
     */
    private $performances;

    /**
     * @var ?string
     * @ORM\Column(name="facebookurl", type="string", length=2083, nullable=true)
     */
    private $facebook_url;
    /**
     * @var ?string
     * @ORM\Column(name="otherurl", type="string", length=2083, nullable=true)
     */
    private $other_url;

    /**
     * Should be in #f21343 notation, if it isn't the form is broken. (It uses JS to enforce the notation.)
     * @var ?string
     * @Assert\Regex("/^#[0-9A-Fa-f]{6}$/",
     *     message="The provided colour must be in six-digit hex notation. If this isn't working leave it blank and contact support.")
     * @ORM\Column(name="colour", type="string", length=7, nullable=true)
     * @Serializer\Expose
     * @Serializer\Type("string")
     */
    private $theme_color;

    /**
     * A URL from which tickets for the show can be bought
     *
     * @var ?string
     * @Assert\Url()
     * @Gedmo\Versioned
     * @Serializer\Expose
     * @ORM\Column(name="onlinebookingurl", type="string", length=2083, nullable=true)
     * @Serializer\Expose
     * @Serializer\Type("string")
     * @Serializer\XmlElement(cdata=false)
     */
    private $online_booking_url;

    private $weeks = "";
    private $weekManager;

    private $entity_type = 'show';

    public function getEntityType(): string
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
     * It's advisable to use getPrettySocData instead as it has the definitive
     * handling of inconsistencies between societies (i.e. the join table) and
     * this.
     */
    public function getSocietiesDisplayList()
    {
        return $this->societies_display_list;
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
     * @param array $societiesList
     */
    public function setSocietiesDisplayList($societiesList): self
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
    public function getPrettySocData(): array {
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
     * Gets all registered venues referenced in this show's performances.
     */
    public function getVenues(): array
    {
        $venids = []; // Not returned, used to check for duplication.
        $venues = []; // Returned
        foreach ($this->performances as $p) {
            if (($v = $p->getVenue()) && !in_array($v->getId(), $venids, true)) {
                $venues[] = $v;
                $venids[] = $v->getId();
            }
        }
        return $venues;
    }

    /**
     * Returns a list of all venue names for this show.
     */
    public function getVenueNames(): array
    {
        return array_unique($this->performances->map(function($p) {
            return $p->getVenueName();
        })->toArray());
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

    public function addPerformance(\Acts\CamdramBundle\Entity\Performance $performance): self
    {
        $this->performances->add($performance);
        $performance->setShow($this);

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
        $this->adverts      = new ArrayCollection();  
        $this->performances = new ArrayCollection();
        $this->roles        = new ArrayCollection();
        $this->societies    = new ArrayCollection();
        $this->timestamp    = new \DateTime();

        $this->setSocietiesDisplayList([]);
    }

    /**
     * Get first performance time
     *
     * @return ?\DateTime
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
     * @return ?\DateTime
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
        if (count($this->getPerformances()) == 0) {
            return 'single';
        }

        $venue = null;
        foreach ($this->getPerformances() as $performance) {
            if ($performance->getVenue()) {
                $cur_venue = $performance->getVenue()->getName();
            } else {
                $cur_venue = $performance->getVenueName();
            }
            if ($venue == null) {
                $venue = $cur_venue;
            } elseif ($venue != $cur_venue) {
                return 'multi';
            }
        }

        return 'single';
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
     * @return Collection|Advert[]
     */
    public function getAdverts(): Collection
    {
        return $this->adverts;
    }

    /**
     * Add advert
     *
     * @param \Acts\CamdramBundle\Entity\Advert $advert
     *
     * @return Show
     */
    public function addAdvert(\Acts\CamdramBundle\Entity\Advert $advert)
    {
        $this->adverts[] = $advert;

        return $this;
    }

    public function removeAdvert(\Acts\CamdramBundle\Entity\Advert $advert)
    {
        $this->adverts->removeElement($advert);
    }

    /**
     * Get auditions that are upcoming.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getActiveAdverts()
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('display', true))
            ->andWhere(Criteria::expr()->gte('expiresAt', new \DateTime()));

        return $this->adverts->matching($criteria);
    }

    public function hasActiveAdverts()
    {
        return count($this->getActiveAdverts()) > 0;
    }

    public function setAuthorised(bool $authorised): self
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

    public function getId(): ?int
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

    public function getName(): ?string
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

    public function getSlug(): ?string
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

    public function setThemeColor(?string $theme_color): self
    {
        $this->theme_color = $theme_color;

        return $this;
    }

    public function getThemeColor(): ?string
    {
        return $this->theme_color;
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

    public static function getAceType(): string
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
            if ($performance->getFinalDateTime() >= $now) {
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
        if (!$this->weeks && $this->getStartAt() && $this->weekManager) {
            $this->weeks = $this->weekManager->getPerformancesWeeksAsString($this->getStartAt(), $this->getEndAt());
        }
        return $this->weeks;
    }

    public function setWeekManager($manager)
    {
        $this->weekManager = $manager;
    }

    public function addSociety(Society $society): self
    {
        if (!$this->societies->contains($society)) {
            $this->societies[] = $society;
        }

        return $this;
    }

    public function removeSociety(Society $society): self
    {
        if ($this->societies->contains($society)) {
            $this->societies->removeElement($society);
        }

        return $this;
    }

}
