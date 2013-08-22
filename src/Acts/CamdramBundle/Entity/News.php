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
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /** @var Entity
     *
     * @ORM\ManyToOne(targetEntity="Entity", inversedBy="news")
     */
    private $entity;

    /**
     * @var integer
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

    /**
     * @var integer
     *
     * @ORM\Column(name="num_comments", type="integer", nullable=true)
     */
    private $num_comments;

    /**
     * @var integer
     *
     * @ORM\Column(name="num_likes", type="integer", nullable=true)
     */
    private $num_likes;

    /**
     * @var boolean
     *
     * @ORM\Column(name="public", type="boolean", nullable=false)
     */
    private $public;

    /** @var array
     *
     * @ORM\OneToMany(targetEntity="NewsLink", mappedBy="news", cascade={"all"})
     */
    private $links;

    /** @var array
     *
     * @ORM\OneToMany(targetEntity="NewsMention", mappedBy="news", cascade={"all"})
     */
    private $mentions;

    /** @var \DateTime
     *
     * @ORM\Column(name="posted_at", type="datetime", nullable=false)
     */
    private $posted_at;

    /** @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    private $created_at;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set remote_id
     *
     * @param string $remoteId
     * @return News
     */
    public function setRemoteId($remoteId)
    {
        $this->remote_id = $remoteId;
    
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
     * Set num_comments
     *
     * @param integer $numComments
     * @return News
     */
    public function setNumComments($numComments)
    {
        $this->num_comments = $numComments;
    
        return $this;
    }

    /**
     * Get num_comments
     *
     * @return integer 
     */
    public function getNumComments()
    {
        return $this->num_comments;
    }

    /**
     * Set num_likes
     *
     * @param integer $numLikes
     * @return News
     */
    public function setNumLikes($numLikes)
    {
        $this->num_likes = $numLikes;
    
        return $this;
    }

    /**
     * Get num_likes
     *
     * @return integer 
     */
    public function getNumLikes()
    {
        return $this->num_likes;
    }

    /**
     * Set entity
     *
     * @param \Acts\CamdramBundle\Entity\Entity $entity
     * @return News
     */
    public function setEntity(\Acts\CamdramBundle\Entity\Entity $entity = null)
    {
        $this->entity = $entity;
    
        return $this;
    }

    /**
     * Get entity
     *
     * @return \Acts\CamdramBundle\Entity\Entity 
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Set public
     *
     * @param boolean $public
     * @return News
     */
    public function setPublic($public)
    {
        $this->public = $public;
    
        return $this;
    }

    /**
     * Get public
     *
     * @return boolean 
     */
    public function getPublic()
    {
        return $this->public;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->links = new \Doctrine\Common\Collections\ArrayCollection();
        $this->created_at = new \DateTime();
    }
    
    /**
     * Add links
     *
     * @param \Acts\CamdramBundle\Entity\NewsLink $links
     * @return News
     */
    public function addLink(\Acts\CamdramBundle\Entity\NewsLink $links)
    {
        $this->links[] = $links;
    
        return $this;
    }

    /**
     * Remove links
     *
     * @param \Acts\CamdramBundle\Entity\NewsLink $links
     */
    public function removeLink(\Acts\CamdramBundle\Entity\NewsLink $links)
    {
        $this->links->removeElement($links);
    }

    /**
     * Get links
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getLinks()
    {
        return $this->links;
    }

    /**
     * Set posted_at
     *
     * @param \DateTime $postedAt
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
     * Add mentions
     *
     * @param \Acts\CamdramBundle\Entity\NewsMention $mentions
     * @return News
     */
    public function addMention(\Acts\CamdramBundle\Entity\NewsMention $mentions)
    {
        $this->mentions[] = $mentions;
    
        return $this;
    }

    /**
     * Remove mentions
     *
     * @param \Acts\CamdramBundle\Entity\NewsMention $mentions
     */
    public function removeMention(\Acts\CamdramBundle\Entity\NewsMention $mentions)
    {
        $this->mentions->removeElement($mentions);
    }

    /**
     * Get mentions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMentions()
    {
        return $this->mentions;
    }

    /**
     * Set picture
     *
     * @param string $picture
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

}