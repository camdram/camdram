<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ActsSocieties
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
     * @ORM\Column(name="name", type="text", nullable=false)
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
     * @ORM\Column(name="shortname", type="text", nullable=false)
     */
    private $shortname;

    /**
     * @var string
     *
     * @ORM\Column(name="college", type="text", nullable=true)
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
     * @ORM\Column(name="logourl", type="text", nullable=true)
     */
    private $logourl;

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
     * @return ActsSocieties
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
     * @return ActsSocieties
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
     * Set shortname
     *
     * @param string $shortname
     * @return ActsSocieties
     */
    public function setShortname($shortname)
    {
        $this->shortname = $shortname;
    
        return $this;
    }

    /**
     * Get shortname
     *
     * @return string 
     */
    public function getShortname()
    {
        return $this->shortname;
    }

    /**
     * Set college
     *
     * @param string $college
     * @return ActsSocieties
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
     * @return ActsSocieties
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
     * @return ActsSocieties
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
     * Set logourl
     *
     * @param string $logourl
     * @return ActsSocieties
     */
    public function setLogourl($logourl)
    {
        $this->logourl = $logourl;
    
        return $this;
    }

    /**
     * Get logourl
     *
     * @return string 
     */
    public function getLogourl()
    {
        return $this->logourl;
    }

    /**
     * Set expires
     *
     * @param \DateTime $expires
     * @return ActsSocieties
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