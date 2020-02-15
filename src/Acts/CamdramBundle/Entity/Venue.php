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
 *      indexes={@ORM\Index(name="idx_venue_fulltext", columns={"name", "shortname"}, flags={"fulltext"})})
 * @ORM\Entity(repositoryClass="VenueRepository")
 * @Gedmo\Loggable
 * @Serializer\ExclusionPolicy("all")
 * @Serializer\XmlRoot("venue")
 */
class Venue extends Organisation implements VenueInterface
{
    /**
     * @var string
     *
     * @ORM\Column(name="address", type="text", nullable=true)
     * @Serializer\Expose
     * @Gedmo\Versioned
     */
    private $address;

    /**
     * @var float
     *
     * @ORM\Column(name="latitude", type="float", nullable=true)
     * @Serializer\Expose
     * @Gedmo\Versioned
     */
    private $latitude;

    /**
     * @var float
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
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Serializer\XmlAttribute
     * @Serializer\Expose
     */
    private $id;

    /**
     * Should be in #f21343 notation, if it isn't the form is broken. (It uses JS to enforce the notation.)
     * @var string
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
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     * @Serializer\Expose
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
     * @var int
     *
     * @ORM\Column(name="facebook_id", type="string", length=50, nullable=true)
     * @Serializer\Expose
     * @Gedmo\Versioned
     * @Serializer\XmlElement(cdata=false)
     */
    private $facebook_id;

    /**
     * @var int
     *
     * @ORM\Column(name="twitter_id", type="string", length=50, nullable=true)
     * @Serializer\Expose
     * @Gedmo\Versioned
     * @Serializer\XmlElement(cdata=false)
     */
    private $twitter_id;

    /**
     * @var string
     *
     * @ORM\Column(name="shortname", type="string", length=100, nullable=true)
     * @Serializer\Expose
     * @Gedmo\Versioned
     * @Serializer\XmlElement(cdata=false)
     */
    private $short_name;

    /**
     * @var string
     *
     * @ORM\Column(name="college", type="string", length=100, nullable=true)
     * @Serializer\Expose
     * @Gedmo\Versioned
     * @Serializer\XmlElement(cdata=false)
     */
    private $college;

    /**
     * @var string
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
     */
    private $news;

    /**
     * @var Application[]
     *
     * @ORM\OneToMany(targetEntity="Application", mappedBy="venue")
     */
    private $applications;

    /**
     * Set short_name
     *
     * @param string $shortName
     *
     * @return Society
     */
    public function setShortName($shortName)
    {
        $this->short_name = $shortName;

        return $this;
    }

    /**
     * Get short_name
     *
     * @return string
     */
    public function getShortName()
    {
        return $this->short_name;
    }

    /**
     * Set college
     *
     * @param string $college
     *
     * @return Society
     */
    public function setCollege($college)
    {
        $this->college = $college;

        return $this;
    }

    /**
     * Get college
     *
     * @return string
     */
    public function getCollege()
    {
        return $this->college;
    }

    /**
     * Set logo_url
     *
     * @param string $logoUrl
     *
     * @return Society
     */
    public function setLogoUrl($logoUrl)
    {
        $this->logo_url = $logoUrl;

        return $this;
    }

    /**
     * Get logo_url
     *
     * @return string
     */
    public function getLogoUrl()
    {
        return $this->logo_url;
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
     * @return Organisation
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

    /**
     * Set twitter_id
     *
     * @param string $twitterId
     *
     * @return Organisation
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
     * @return Organisation
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
     * @return Organisation
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

    public function getRank()
    {
        return PHP_INT_MAX;
    }

    /**
     * Add news
     *
     * @param \Acts\CamdramBundle\Entity\News $news
     *
     * @return Organisation
     */
    public function addNew(\Acts\CamdramBundle\Entity\News $news)
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

    /**
     * Add news
     *
     * @param \Acts\CamdramBundle\Entity\News $news
     *
     * @return Organisation
     */
    public function addNews(\Acts\CamdramBundle\Entity\News $news)
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

    /**
     * Add applications
     *
     * @param \Acts\CamdramBundle\Entity\Application $applications
     *
     * @return Organisation
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

    public static function getAceType()
    {
        return 'venue';
    }


    public function getEntityType()
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
