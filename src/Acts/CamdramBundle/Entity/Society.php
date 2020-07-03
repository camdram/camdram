<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use Acts\CamdramApiBundle\Configuration\Annotation as Api;

/**
 * Society
 *
 * @ORM\Table(name="acts_societies", uniqueConstraints={@ORM\UniqueConstraint(name="org_slugs",columns={"slug"})},
 *      indexes={@ORM\Index(name="idx_society_fulltext", columns={"name", "shortname", "slug"}, flags={"fulltext"})})
 * @ORM\Entity(repositoryClass="Acts\CamdramBundle\Entity\SocietyRepository")
 * @Gedmo\Loggable
 * @Serializer\ExclusionPolicy("all")
 * @Serializer\XmlRoot("society")
 */
class Society extends Organisation
{
    /**
     * @ORM\ManyToMany(targetEntity="Show", mappedBy="societies")
     * @Api\Link(route="acts_camdram_society_getshows", params={"identifier": "object.getSlug()"})
     */
    private $shows;

    private $entity_type = 'society';

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
     * @ORM\OneToMany(targetEntity="News", mappedBy="society")
     */
    private $news;

    /**
     * @ORM\OneToMany(targetEntity="Application", mappedBy="society")
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

    public function setName(?string $name): self
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

    public function getSlug(): ?string
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
        $this->shows = new \Doctrine\Common\Collections\ArrayCollection();
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

    public function removeApplication(\Acts\CamdramBundle\Entity\Application $application)
    {
        $this->applications->removeElement($application);
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
        return 'society';
    }

    public function getEntityType(): string
    {
        return $this->entity_type;
    }

    /**
     * Add shows
     *
     * @param \Acts\CamdramBundle\Entity\Show $shows
     *
     * @return Society
     */
    public function addShow(\Acts\CamdramBundle\Entity\Show $shows)
    {
        $this->shows[] = $shows;

        return $this;
    }

    /**
     * Remove shows
     *
     * @param \Acts\CamdramBundle\Entity\Show $shows
     */
    public function removeShow(\Acts\CamdramBundle\Entity\Show $shows)
    {
        $this->shows->removeElement($shows);
    }

    /**
     * Get shows
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getShows()
    {
        return $this->shows;
    }

    public function getIndexDate()
    {
        return null;
    }

    public function getOrganisationType()
    {
        return 'society';
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
