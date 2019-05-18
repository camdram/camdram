<?php

namespace Acts\CamdramBundle\Entity;

use Acts\CamdramBundle\Search\SearchableInterface;
use Acts\CamdramSecurityBundle\Security\OwnableInterface;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as Serializer;
use Acts\CamdramApiBundle\Configuration\Annotation as Api;

/**
 * Organisation
 *
 * @ORM\Table(name="acts_societies", uniqueConstraints={@ORM\UniqueConstraint(name="org_slugs",columns={"slug"})})
 * @ORM\Entity(repositoryClass="OrganisationRepository")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({0 = "Society", 1 = "Venue"})
 * @Gedmo\Loggable
 * @Serializer\ExclusionPolicy("all")
 */
abstract class Organisation implements OwnableInterface
{
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
     * @var \DateTime
     *
     * @ORM\Column(name="expires", type="date", nullable=true)
     */
    private $expires;

    /**
     * @ORM\OneToMany(targetEntity="News", mappedBy="entity")
     */
    private $news;

    /**
     * @var Application[]
     *
     * @ORM\OneToMany(targetEntity="Application", mappedBy="society")
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
     * Set expires
     *
     * @param \DateTime $expires
     *
     * @return Society
     */
    public function setExpires($expires)
    {
        $this->expires = $expires;

        return $this;
    }

    /**
     * Get expires
     *
     * @return \DateTime
     */
    public function getExpires()
    {
        return $this->expires;
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

    public function setSocialId($service, $id)
    {
        switch ($service) {
            case 'facebook': $this->setFacebookId($id); break;
            case 'twitter': $this->setTwitterId($id); break;
        }
    }

    public function getSocialId($service)
    {
        switch ($service) {
            case 'facebook': return $this->getFacebookId();
            case 'twitter': return $this->getTwitterId();
        }
    }

    public function getFacebookUrl()
    {
        return 'http://www.facebook.com/'.$this->getFacebookId();
    }

    public function getTwitterUrl()
    {
        return 'https://twitter.com/intent/user?user_id='.$this->getTwitterId();
    }

    public function getSocialUrl($service)
    {
        switch ($service) {
            case 'facebook': return $this->getFacebookUrl();
            case 'twitter': return $this->getTwitterUrl();
        }
    }

    public function hasSocialId()
    {
        return $this->getFacebookId() || $this->getTwitterId();
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
        return 'society';
    }

    abstract public function getOrganisationType();

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
