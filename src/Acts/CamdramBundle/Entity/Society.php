<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Society
 *
 * @ORM\Table(name="acts_societies")
 * @ORM\Entity
 */
class Society
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
     * @var string
     *
     * @ORM\Column(name="shortname", type="string", length=100, nullable=false)
     */
    private $short_name;

    /**
     * @var string
     *
     * @ORM\Column(name="college", type="string", length=100, nullable=true)
     */
    private $college;

    /**
     * @var boolean
     *
     * @ORM\Column(name="type", type="boolean", nullable=false)
     */
    private $type;

    /**
     * @var boolean
     *
     * @ORM\Column(name="affiliate", type="boolean", nullable=false)
     */
    private $affiliate;

    /**
     * @var string
     *
     * @ORM\Column(name="logourl", type="string", length=255, nullable=true)
     */
    private $logo_url;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="expires", type="date", nullable=false)
     */
    private $expires;


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
     * @return Society
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
     * @return Society
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
     * Set short_name
     *
     * @param string $shortName
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
     * Set type
     *
     * @param boolean $type
     * @return Society
     */
    public function setType($type)
    {
        $this->type = $type;
    
        return $this;
    }

    /**
     * Get type
     *
     * @return boolean 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set affiliate
     *
     * @param boolean $affiliate
     * @return Society
     */
    public function setAffiliate($affiliate)
    {
        $this->affiliate = $affiliate;
    
        return $this;
    }

    /**
     * Get affiliate
     *
     * @return boolean 
     */
    public function getAffiliate()
    {
        return $this->affiliate;
    }

    /**
     * Set logo_url
     *
     * @param string $logoUrl
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
}