<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Acts\CamdramBundle\Entity\Show;

/**
 * EmailBuilder
 * @ORM\Table(name="acts_emailbuilder", uniqueConstraints={@ORM\UniqueConstraint(name="email_builder_slugs",columns={"slug"})})
 * @ORM\Entity(repositoryClass="Acts\CamdramBundle\Entity\EmailBuilderRepository")
 */
class EmailBuilder
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="ToAddress", type="string", length=255)
     */
    private $toAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="FromAddress", type="string", length=255)
     */
    private $fromAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="Subject", type="string", length=255)
     */
    private $subject;
    
    /**
     * @var string
     *
     * @ORM\Column(name="Title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="Introduction", type="text")
     */
    private $introduction;

    /**
     * @var boolean
     *
     * @ORM\Column(name="IncludeTechieAdverts", type="boolean")
     */
    private $includeTechieAdverts;

    /**
     * @var boolean
     *
     * @ORM\Column(name="IncludeAuditions", type="boolean")
     */
    private $includeAuditions;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="IncludeApplications", type="boolean")
     */
    private $includeApplications;

    /**
     * @Gedmo\Slug(fields={"name"})
     * @var string    
     * @ORM\Column(name="Slug", type="string")
     */
          
    private $slug;
    
    
    /**
     * @var string    
     * @ORM\Column(name="Name", type="string")
     */
    
    private $name;
    
    /** 
     * @var array
     * @ORM\ManyToMany(targetEntity="Show")
     * @ORM\JoinTable(
     *     name="acts_EmailBuilderShows", 
     *     joinColumns={@ORM\JoinColumn(name="EmailBuilderId", referencedColumnName="id", nullable=false)}, 
     *     inverseJoinColumns={@ORM\JoinColumn(name="Showid", referencedColumnName="id", nullable=false)}
     * )
     */
    private $showFilter;
    
    /**
     * @var integer    
     * @ORM\Column(name="ShowFilterMode", type="integer")
     */
    
    private $showFilterMode;
    
    const FILTERMODEALL = 0;
    const FILTERMODEINCLUDE = 1;
    const FILTERMODEEXCLUDE = 2;
   

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
     * Set toAddress
     *
     * @param string $toAddress
     * @return EmailBuilder
     */
    public function setToAddress($toAddress)
    {
        $this->toAddress = $toAddress;

        return $this;
    }

    /**
     * Get toAddress
     *
     * @return string 
     */
    public function getToAddress()
    {
        return $this->toAddress;
    }

    /**
     * Set fromAddress
     *
     * @param string $fromAddress
     * @return EmailBuilder
     */
    public function setFromAddress($fromAddress)
    {
        $this->fromAddress = $fromAddress;

        return $this;
    }

    /**
     * Get fromAddress
     *
     * @return string 
     */
    public function getFromAddress()
    {
        return $this->fromAddress;
    }

    /**
     * Set subject
     *
     * @param string $subject
     * @return EmailBuilder
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get subject
     *
     * @return string 
     */
    public function getSubject()
    {
        return $this->subject;
    }
    
    /**
     * Set introduction
     *
     * @param string $introduction
     * @return EmailBuilder
     */
    public function setIntroduction($introduction)
    {
        $this->introduction = $introduction;

        return $this;
    }

    /**
     * Get introduction
     *
     * @return string 
     */
    public function getIntroduction()
    {
        return $this->introduction;
    }

    /**
     * Set includeTechieAdverts
     *
     * @param boolean $includeTechieAdverts
     * @return EmailBuilder
     */
    public function setIncludeTechieAdverts($includeTechieAdverts)
    {
        $this->includeTechieAdverts = $includeTechieAdverts;

        return $this;
    }

    /**
     * Get includeTechieAdverts
     *
     * @return boolean 
     */
    public function getIncludeTechieAdverts()
    {
        return $this->includeTechieAdverts;
    }

    /**
     * Set includeAuditions
     *
     * @param boolean $includeAuditions
     * @return EmailBuilder
     */
    public function setIncludeAuditions($includeAuditions)
    {
        $this->includeAuditions = $includeAuditions;

        return $this;
    }

    /**
     * Get includeAuditions
     *
     * @return boolean 
     */
    public function getIncludeAuditions()
    {
        return $this->includeAuditions;
    }

    /**
     * Set includeApplications
     *
     * @param boolean $includeApplications
     * @return EmailBuilder
     */
    public function setIncludeApplications($includeApplications)
    {
        $this->includeApplications = $includeApplications;

        return $this;
    }

    /**
     * Get includeApplications
     *
     * @return boolean 
     */
    public function getIncludeApplications()
    {
        return $this->includeApplications;
    }

    /**
     * Set slug
     *
     * @param \slug $slug
     * @return EmailBuilder
     */
    public function setSlug(\slug $slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return \slug 
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return EmailBuilder
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
     * Set title
     *
     * @param string $title
     * @return EmailBuilder
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->showFilter = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set showFilterMode
     *
     * @param integer $showFilterMode
     * @return EmailBuilder
     */
    public function setShowFilterMode($showFilterMode)
    {
        $this->showFilterMode = $showFilterMode;

        return $this;
    }

    /**
     * Get showFilterMode
     *
     * @return integer 
     */
    public function getShowFilterMode()
    {
        return $this->showFilterMode;
    }

    /**
     * Add showFilter
     *
     * @param \Acts\CamdramBundle\Entity\Show $showFilter
     * @return EmailBuilder
     */
    public function addShowFilter(\Acts\CamdramBundle\Entity\Show $showFilter)
    {
        $this->showFilter[] = $showFilter;

        return $this;
    }

    /**
     * Remove showFilter
     *
     * @param \Acts\CamdramBundle\Entity\Show $showFilter
     */
    public function removeShowFilter(\Acts\CamdramBundle\Entity\Show $showFilter)
    {
        $this->showFilter->removeElement($showFilter);
    }

    /**
     * Get showFilter
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getShowFilter()
    {
        return $this->showFilter;
    }
}
