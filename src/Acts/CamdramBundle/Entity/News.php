<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Acts\CamdramApiBundle\Configuration\Annotation as Api;

/**
 * News
 *
 * @ORM\Table(name="acts_news")
 * @ORM\Entity(repositoryClass="NewsRepository")
 * @Serializer\ExclusionPolicy("all")
 * @Serializer\XmlRoot("news")
 */
class News
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Serializer\XmlAttribute
     * @Serializer\Expose()
     * @Serializer\Type("integer")
     */
    private $id;

    /** @var Society|null
     * @ORM\ManyToOne(targetEntity="Society", inversedBy="news")
     * @ORM\JoinColumn(name="society_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)
     * @Api\Link(embed=true, route="get_society", params={"identifier": "object.getSociety().getSlug()"})
     */
    private $society;

    /** @var Venue|null
     * @ORM\ManyToOne(targetEntity="Venue", inversedBy="news")
     * @ORM\JoinColumn(name="venue_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)
     * @Api\Link(embed=true, route="get_venue", params={"identifier": "object.getVenue().getSlug()"})
     */
    private $venue;

    /**
     * @var ?string
     *
     * @ORM\Column(name="remote_id", type="string", length=255, nullable=true)
     * @Serializer\Expose
     */
    private $remote_id;

    /**
     * @var string
     *
     * @ORM\Column(name="source", type="string", length=20, nullable=false)
     */
    private $source;

    /**
     * @var ?string
     *
     * @ORM\Column(name="picture", type="string", length=255, nullable=true)
     * @Serializer\Expose
     */
    private $picture;

    /**
     * @var string
     *
     * @ORM\Column(name="body", type="text")
     * @Serializer\Expose
     */
    private $body;

    /**
     * @var \DateTime
     * @ORM\Column(name="posted_at", type="datetime", nullable=false)
     * @Serializer\Expose
     */
    private $posted_at;

    /** 
     * @var \DateTime
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     * @Serializer\Expose
     */
    private $created_at;

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
     * @param string $remoteId
     */
    public function setRemoteId($remoteId): self
    {
        $this->remote_id = (string) $remoteId;

        return $this;
    }

    /**
     * @return string
     */
    public function getRemoteId()
    {
        return $this->remote_id;
    }

    /**
     * Set source
     *
     * @param string $source
     *
     * @return News
     */
    public function setSource($source)
    {
        $this->source = $source;

        return $this;
    }

    /**
     * Get source
     *
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Set body
     *
     * @param string $body
     *
     * @return News
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Get body
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set entity
     *
     * @param \Acts\CamdramBundle\Entity\Organisation $entity
     *
     * @return News
     */
    public function setEntity(\Acts\CamdramBundle\Entity\Organisation $entity = null)
    {
        if ($entity instanceof Society) {
            $this->society = $entity;
        } else if ($entity instanceof Venue) {
            $this->venue = $entity;
        } else {
            throw new \Exception('Expected Society or Venue.');
        }

        return $this;
    }

    /**
     * Get entity
     *
     * @return \Acts\CamdramBundle\Entity\Organisation
     */
    public function getEntity()
    {
        return $this->society ?: $this->venue;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->created_at = new \DateTime();
    }

    /**
     * Set posted_at
     *
     * @param \DateTime $postedAt
     *
     * @return News
     */
    public function setPostedAt($postedAt)
    {
        $this->posted_at = $postedAt;

        return $this;
    }

    /**
     * Get posted_at
     *
     * @return \DateTime
     */
    public function getPostedAt()
    {
        return $this->posted_at;
    }

    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     *
     * @return News
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;

        return $this;
    }

    /**
     * Get created_at
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Set picture
     *
     * @param string $picture
     *
     * @return News
     */
    public function setPicture($picture)
    {
        $this->picture = $picture;

        return $this;
    }

    /**
     * Get picture
     *
     * @return string
     */
    public function getPicture()
    {
        return $this->picture;
    }

    /** @return string */
    public function getLink()
    {
        switch ($this->getSource()) {
            case 'facebook': return 'http://www.facebook.com/'.$this->getRemoteId();
            case 'twitter': return 'http://www.twitter.com/redirect/status/'.$this->getRemoteId();
        }
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
}
