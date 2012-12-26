<?php
namespace Acts\CamdramBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ORM\Table(name="acts_entities", uniqueConstraints={@ORM\UniqueConstraint(name="slugs",columns={"entity_type"})})
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="entity_type", type="string")
 * @ORM\DiscriminatorMap({"person" = "Person", "show" = "Show", "society" = "Society", "venue" = "Venue"})
 */
abstract class Entity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=false)
     */
    private $description;

    /**
     * @var int
     *
     * @ORM\Column(name="facebook_id", type="string", length=50, nullable=true)
     */
    private $facebook_id;

    /**
     * @var int
     *
     * @ORM\Column(name="twitter_id", type="string", length=50, nullable=true)
     */
    private $twitter_id;

    /**
     * @var bool
     *
     * @ORM\Column(name="public", type="boolean", nullable=false)
     */
    private $public;


    /**
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(name="slug", type="string", length=128, nullable=true)
     */
    protected $slug;

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
     * Set name
     *
     * @param string $name
     * @return Entity
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
     * @return Entity
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
     * @return Entity
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
     * @return Entity
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

    public function getSocialId($service_name)
    {
        return call_user_func(array($this, 'get'.ucfirst($service_name).'Id'));
    }

    public function setSocialId($service_name, $value)
    {
        return call_user_func(array($this, 'set'.ucfirst($service_name).'Id'), $value);
    }

    /**
     * Set public
     *
     * @param boolean $public
     * @return Entity
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
     * Set slug
     *
     * @param string $slug
     * @return Entity
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
}