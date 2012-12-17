<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * News
 *
 * @ORM\Table(name="acts_news")
 * @ORM\Entity
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
}