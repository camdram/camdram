<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * News
 *
 * @ORM\Table(name="acts_news")
 * @ORM\Entity(repositoryClass="NewsRepository")
 */
class News
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /** @var Entity
     * @ORM\ManyToOne(targetEntity="Society", inversedBy="news")
     * @ORM\JoinColumn(name="socid", referencedColumnName="id", onDelete="CASCADE", nullable=true)
     */
    private $society;

    /** @var Entity
     * @ORM\ManyToOne(targetEntity="Venue", inversedBy="news")
     * @ORM\JoinColumn(name="venid", referencedColumnName="id", onDelete="CASCADE", nullable=true)
     */
    private $venue;

    /**
     * @var int
     *
     * @ORM\Column(name="remote_id", type="string", length=255, nullable=true)
     */
    private $remote_id;

    /**
     * @var string
     *
     * @ORM\Column(name="source", type="string", length=20, nullable=false)
     */
    private $source;

    /**
     * @var string
     *
     * @ORM\Column(name="picture", type="string", length=255, nullable=true)
     */
    private $picture;

    /**
     * @var Name
     *
     * @ORM\Column(name="body", type="text")
     */
    private $body;

    /** @var \DateTime
     * @ORM\Column(name="posted_at", type="datetime", nullable=false)
     */
    private $posted_at;

    /** @var \DateTime
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
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
     * Set remote_id
     *
     * @param string $remoteId
     *
     * @return News
     */
    public function setRemoteId($remoteId)
    {
        $this->remote_id = (string) $remoteId;

        return $this;
    }

    /**
     * Get remote_id
     *
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
            throw new Exception('Expected Society or Venue.');
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

    public function getLink()
    {
        switch ($this->getSource()) {
            case 'facebook': return 'http://www.facebook.com/'.$this->getRemoteId(); break;
            case 'twitter': return 'http://www.twitter.com/redirect/status/'.$this->getRemoteId(); break;
        }
    }
}
