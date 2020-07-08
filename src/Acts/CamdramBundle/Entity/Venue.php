<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Gedmo\Mapping\Annotation as Gedmo;
use Acts\CamdramApiBundle\Configuration\Annotation as Api;
use Acts\DiaryBundle\Model\VenueInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="acts_venues", uniqueConstraints={@ORM\UniqueConstraint(name="ven_slugs",columns={"slug"})},
 *      indexes={@ORM\Index(name="idx_venue_fulltext", columns={"name", "shortname", "slug"}, flags={"fulltext"})})
 * @ORM\Entity(repositoryClass="VenueRepository")
 * @Gedmo\Loggable
 * @Serializer\ExclusionPolicy("all")
 * @Serializer\XmlRoot("venue")
 */
class Venue extends Organisation implements VenueInterface
{
    /**
     * @var ?string
     *
     * @ORM\Column(name="address", type="text", nullable=true)
     * @Serializer\Expose
     * @Gedmo\Versioned
     */
    private $address;

    /**
     * @var ?float
     *
     * @ORM\Column(name="latitude", type="float", nullable=true)
     * @Serializer\Expose
     * @Gedmo\Versioned
     */
    private $latitude;

    /**
     * @var ?float
     *
     * @ORM\Column(name="longitude", type="float", nullable=true)
     * @Serializer\Expose
     * @Gedmo\Versioned
     */
    private $longitude;

    /**
     * @ORM\OneToMany(targetEntity="Performance", mappedBy="venue")
     */
    private $performances;

    private $entity_type = 'venue';

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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     * @Assert\NotBlank()
     * @Serializer\Expose
     * @Gedmo\Versioned
     * @Serializer\XmlElement(cdata=false)
     */
    private $name;

    /**
     * @var ?string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     * @Serializer\Expose
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
     * @var ?string
     *
     * @ORM\Column(name="facebook_id", type="string", length=50, nullable=true)
     * @Serializer\Expose
     * @Gedmo\Versioned
     * @Serializer\XmlElement(cdata=false)
     */
    private $facebook_id;

    /**
     * @var ?string
     *
     * @ORM\Column(name="twitter_id", type="string", length=50, nullable=true)
     * @Serializer\Expose
     * @Gedmo\Versioned
     * @Serializer\XmlElement(cdata=false)
     */
    private $twitter_id;

    /**
     * @var ?string
     *
     * @ORM\Column(name="shortname", type="string", length=100, nullable=true)
     * @Serializer\Expose
     * @Gedmo\Versioned
     * @Serializer\XmlElement(cdata=false)
     */
    private $short_name;

    /**
     * @var ?string
     *
     * @ORM\Column(name="college", type="string", length=100, nullable=true)
     * @Serializer\Expose
     * @Gedmo\Versioned
     * @Serializer\XmlElement(cdata=false)
     */
    private $college;

    /**
     * @var ?string
     *
     * @ORM\Column(name="logourl", type="string", length=255, nullable=true)
     */
    private $logo_url;

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
     * @ORM\OneToMany(targetEntity="News", mappedBy="venue")
     * @Api\Link(route="acts_camdram_venue_getnews", params={"identifier": "object.getSlug()"})
     */
    private $news;

    /**
     * @var \Doctrine\Common\Collections\Collection<Application>
     *
     * @ORM\OneToMany(targetEntity="Application", mappedBy="venue")
     */
    private $applications;

    public function setShortName(?string $shortName): self
    {
        $this->short_name = $shortName;

        return $this;
    }

    public function getShortName(): ?string
    {
        return $this->short_name;
    }

    public function setCollege(?string $college): self
    {
        $this->college = $college;

        return $this;
    }

    public function getCollege(): ?string
    {
        return $this->college;
    }

    public function setLogoUrl(?string $logoUrl): self
    {
        $this->logo_url = $logoUrl;

        return $this;
    }

    public function getLogoUrl(): ?string
    {
        return $this->logo_url;
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
     * @return Organisation
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
     * @return Organisation
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
     * @return Organisation
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

    public function setTwitterId(?string $twitterId): self
    {
        $this->twitter_id = $twitterId;

        return $this;
    }

    public function getTwitterId(): ?string
    {
        return $this->twitter_id;
    }

    public function setImage(Image $image = null): ?self
    {
        $this->image = $image;

        return $this;
    }

    public function getImage(): ?Image
    {
        return $this->image;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function getRank()
    {
        return PHP_INT_MAX;
    }


    public function addNew(\Acts\CamdramBundle\Entity\News $news): self
    {
        $this->news[] = $news;

        return $this;
    }

    /**
     * Remove news
     *
     * @param \Acts\CamdramBundle\Entity\News $news
     */
    public function removeNew(\Acts\CamdramBundle\Entity\News $news)
    {
        $this->news->removeElement($news);
    }

    /**
     * Get news
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getNews()
    {
        return $this->news;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->news = new \Doctrine\Common\Collections\ArrayCollection();
        $this->applications = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function addNews(\Acts\CamdramBundle\Entity\News $news): self
    {
        $this->news[] = $news;

        return $this;
    }

    /**
     * Remove news
     *
     * @param \Acts\CamdramBundle\Entity\News $news
     */
    public function removeNews(\Acts\CamdramBundle\Entity\News $news)
    {
        $this->news->removeElement($news);
    }

    public function addApplication(\Acts\CamdramBundle\Entity\Application $applications): self
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

    public static function getAceType(): string
    {
        return 'venue';
    }

    public function getEntityType(): string
    {
        return $this->entity_type;
    }

    /**
     * Set latitude
     *
     * @param float $latitude
     *
     * @return Venue
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * Get latitude
     *
     * @return float
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set longitude
     *
     * @param float $longitude
     *
     * @return Venue
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * Get longitude
     *
     * @return float
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Set address
     *
     * @param string $address
     *
     * @return Venue
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param MapLocation|null $location
     */
    public function setLocation($location)
    {
        if ($location instanceof MapLocation) {
            $this->latitude = $location->getLatitude();
            $this->longitude = $location->getLongitude();
        }
    }

    /**
     * @return MapLocation|null
     * @Assert\Valid()
     */
    public function getLocation()
    {
        return new MapLocation($this->latitude, $this->longitude);
    }

    public function getIndexDate()
    {
        return null;
    }

    /**
     * Add performances
     *
     * @param \Acts\CamdramBundle\Entity\Performance $performances
     *
     * @return Venue
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

    public function getOrganisationType()
    {
        return 'venue';
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
}
