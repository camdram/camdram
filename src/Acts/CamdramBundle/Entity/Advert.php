<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

use Acts\CamdramBundle\Validator\Constraints\AdvertExpiry;
use Acts\CamdramBundle\Service\Time;
use Acts\CamdramApiBundle\Configuration\Annotation as Api;
use Acts\CamdramSecurityBundle\Security\OwnableInterface;

/**
 * Advert
 *
 * @ORM\Table(name="acts_adverts")
 * @ORM\Entity(repositoryClass="AdvertRepository")
 * @ORM\HasLifecycleCallbacks()
 * @Api\Feed(name="Camdram.net - Vacancies", titleField="feed_title",
 *     description="Vacancies advertised for shows in Cambridge",
 *     template="advert/rss.html.twig")
 * @Serializer\XmlRoot("advert")
 * @Serializer\ExclusionPolicy("all")
 * @AdvertExpiry()
 * @Gedmo\Loggable
 * @Api\Link(route="get_advert", params={"identifier": "object.getId()"})
 */
class Advert implements OwnableInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Serializer\Expose
     * @Serializer\XmlAttribute
     */
    private $id;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @ORM\Column(name="title", type="string", length=255, nullable=false)
     * @Serializer\Expose
     * @Gedmo\Versioned
     */
    private $name;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @ORM\Column(name="type", type="string", length=255, nullable=false)
     * @Serializer\Expose
     */
    private $type;

    const TYPE_ACTORS = 'actors';
    const TYPE_TECHNICAL = 'technical';
    const TYPE_DESIGN = 'design';
    const TYPE_APPLICATION = 'application';
    const TYPE_OTHER = 'other';

    /**
     * @var \DateTime
     *
     * @Assert\NotBlank()
     * @ORM\Column(name="expires_at", type="datetime", nullable=false)
     * @Serializer\Expose
     * @Gedmo\Versioned
     */
    private $expiresAt;

    /**
     * @var bool
     *
     * @ORM\Column(name="display", type="boolean", nullable=false)
     * @Serializer\Expose
     * @Gedmo\Versioned
     */
    private $display;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @ORM\Column(name="summary", type="text", nullable=false)
     * @Serializer\Expose
     * @Gedmo\Versioned
     */
    private $summary;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @ORM\Column(name="body", type="text", nullable=false)
     * @Serializer\Expose
     * @Gedmo\Versioned
     */
    private $body;

    /**
     * @var \DateTimeInterface
     *
     * @Assert\NotBlank()
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     * @Gedmo\Timestampable(on="create")
     * @Serializer\Expose
     * @Gedmo\Versioned
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @Assert\NotBlank()
     * @ORM\Column(name="updated_at", type="datetime", nullable=false)
     * @Gedmo\Timestampable(on="update")
     * @Serializer\Expose
     * @Gedmo\Versioned
     */
    private $updatedAt;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @ORM\Column(name="contact_details", type="string", length=255, nullable=false)
     * @Serializer\Expose
     * @Gedmo\Versioned
     */
    private $contactDetails;

    /**
     * @var ?Show
     *
     * @ORM\ManyToOne(targetEntity="Show", inversedBy="adverts")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="show_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)
     * })
     * @Gedmo\Versioned
     * @Api\Link(embed=true, route="get_show", params={"identifier": "object.getShow().getSlug()"})
     */
    private $show;

    /**
     * @var ?Society
     *
     * @ORM\ManyToOne(targetEntity="Society", inversedBy="adverts")
     * @ORM\JoinColumn(name="society_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)
     * @Gedmo\Versioned
     * @Api\Link(embed=true, route="get_society", params={"identifier": "object.getSociety().getSlug()"})
     */
    private $society;

    /**
     * @var ?Venue
     *
     * @ORM\ManyToOne(targetEntity="Venue", inversedBy="adverts")
     * @ORM\JoinColumn(name="venue_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)
     * @Gedmo\Versioned
     * @Api\Link(embed=true, route="get_venue", params={"identifier": "object.getVenue().getSlug()"})
     */
    private $venue;

     /**
     * @var Collection<Audition>
     *
     * @ORM\OneToMany(targetEntity="Audition", mappedBy="advert", cascade={"all"}, orphanRemoval=true)
     * @Serializer\Expose
     * @Serializer\XmlList(inline = true, entry = "audition")
     */
    private $auditions;

    public function __construct()
    {
        $this->auditions = new ArrayCollection();
        $this->display = true;
        $this->expiresAt = (new \DateTime('+2 weeks'))->setTime(0, 0, 0);
        $this->type = self::TYPE_ACTORS;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getExpiresAt(): ?\DateTime
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(?\DateTime $expiresAt): self
    {
        $this->expiresAt = $expiresAt;

        return $this;
    }

    public function isExpired() : bool
    {
        return $this->expiresAt <= Time::now();
    }

    public function getDisplay(): ?bool
    {
        return $this->display;
    }

    public function setDisplay(bool $display): self
    {
        $this->display = $display;

        return $this;
    }

    public function isVisible() : bool
    {
        return $this->getDisplay() && !$this->isExpired();
    }

    public function getSummary(): ?string
    {
        return $this->summary;
    }

    public function setSummary(?string $summary): self
    {
        $this->summary = $summary;

        return $this;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(?string $body): self
    {
        $this->body = $body;

        return $this;
    }

    public function getShow(): ?Show
    {
        return $this->show;
    }

    public function setShow(?Show $show): self
    {
        $this->show = $show;

        return $this;
    }

    /**
     * @return Collection|Audition[]
     */
    public function getAuditions(): Collection
    {
        return $this->auditions;
    }

    public function addAudition(Audition $audition): self
    {
        if (!$this->auditions->contains($audition)) {
            $this->auditions[] = $audition;
            $audition->setAdvert($this);
        }

        return $this;
    }

    public function removeAudition(Audition $audition): self
    {
        if ($this->auditions->contains($audition)) {
            $this->auditions->removeElement($audition);
            // set the owning side to null (unless already changed)
            if ($audition->getAdvert() === $this) {
                $audition->setAdvert(null);
            }
        }

        return $this;
    }

    public function getContactDetails(): ?string
    {
        return $this->contactDetails;
    }

    public function setContactDetails(?string $contactDetails): self
    {
        $this->contactDetails = $contactDetails;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getFeedTitle()
    {
        return $this->getName(). ' - last updated '.$this->getUpdatedAt()->format('D, j M Y H:i T');
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getSociety(): ?Society
    {
        return $this->society;
    }

    public function setSociety(?Society $society): self
    {
        $this->society = $society;

        return $this;
    }

    public function getVenue(): ?Venue
    {
        return $this->venue;
    }

    public function setVenue(?Venue $venue): self
    {
        $this->venue = $venue;

        return $this;
    }

    /** @return Show|Society|Venue|null */
    public function getParentEntity()
    {
        return $this->show ?: $this->society ?: $this->venue;
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function updateAuditions()
    {
        if ($this->getType() != self::TYPE_ACTORS) {
            $this->auditions->clear();
        }
    }

    public static function getAceType(): string
    {
        return 'advert';
    }

}
