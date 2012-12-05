<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ActsPages
 *
 * @ORM\Table(name="acts_pages")
 * @ORM\Entity
 */
class Page
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
     * @ORM\Column(name="title", type="text", nullable=false)
     */
    private $title;

    /**
     * @var integer
     *
     * @ORM\Column(name="parentid", type="integer", nullable=false)
     */
    private $parentid;

    /**
     * @var integer
     *
     * @ORM\Column(name="sortcode", type="integer", nullable=false)
     */
    private $sortcode;

    /**
     * @var string
     *
     * @ORM\Column(name="fulltitle", type="text", nullable=false)
     */
    private $fulltitle;

    /**
     * @var boolean
     *
     * @ORM\Column(name="secure", type="boolean", nullable=false)
     */
    private $secure;

    /**
     * @var boolean
     *
     * @ORM\Column(name="micro", type="boolean", nullable=false)
     */
    private $micro;

    /**
     * @var string
     *
     * @ORM\Column(name="help", type="text", nullable=false)
     */
    private $help;

    /**
     * @var boolean
     *
     * @ORM\Column(name="ghost", type="boolean", nullable=false)
     */
    private $ghost;

    /**
     * @var string
     *
     * @ORM\Column(name="mode", type="string", nullable=true)
     */
    private $mode;

    /**
     * @var integer
     *
     * @ORM\Column(name="allowsubpage", type="integer", nullable=false)
     */
    private $allowsubpage;

    /**
     * @var string
     *
     * @ORM\Column(name="intertitle", type="text", nullable=false)
     */
    private $intertitle;

    /**
     * @var boolean
     *
     * @ORM\Column(name="knowledgebase", type="boolean", nullable=false)
     */
    private $knowledgebase;

    /**
     * @var string
     *
     * @ORM\Column(name="getvars", type="text", nullable=false)
     */
    private $getvars;

    /**
     * @var string
     *
     * @ORM\Column(name="postvars", type="text", nullable=false)
     */
    private $postvars;

    /**
     * @var string
     *
     * @ORM\Column(name="usepage", type="text", nullable=false)
     */
    private $usepage;

    /**
     * @var integer
     *
     * @ORM\Column(name="kbid", type="integer", nullable=false)
     */
    private $kbid;

    /**
     * @var string
     *
     * @ORM\Column(name="rssfeeds", type="text", nullable=false)
     */
    private $rssfeeds;

    /**
     * @var boolean
     *
     * @ORM\Column(name="locked", type="boolean", nullable=false)
     */
    private $locked;

    /**
     * @var boolean
     *
     * @ORM\Column(name="virtual", type="boolean", nullable=false)
     */
    private $virtual;

    /**
     * @var boolean
     *
     * @ORM\Column(name="param_parser", type="boolean", nullable=false)
     */
    private $paramParser;

    /**
     * @var string
     *
     * @ORM\Column(name="access_php", type="text", nullable=false)
     */
    private $accessPhp;

    /**
     * @var string
     *
     * @ORM\Column(name="subpagetemplate", type="text", nullable=false)
     */
    private $subpagetemplate;

    /**
     * @var boolean
     *
     * @ORM\Column(name="searchable", type="boolean", nullable=false)
     */
    private $searchable;



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
     * Set title
     *
     * @param string $title
     * @return ActsPages
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
     * Set parentid
     *
     * @param integer $parentid
     * @return ActsPages
     */
    public function setParentid($parentid)
    {
        $this->parentid = $parentid;
    
        return $this;
    }

    /**
     * Get parentid
     *
     * @return integer 
     */
    public function getParentid()
    {
        return $this->parentid;
    }

    /**
     * Set sortcode
     *
     * @param integer $sortcode
     * @return ActsPages
     */
    public function setSortcode($sortcode)
    {
        $this->sortcode = $sortcode;
    
        return $this;
    }

    /**
     * Get sortcode
     *
     * @return integer 
     */
    public function getSortcode()
    {
        return $this->sortcode;
    }

    /**
     * Set fulltitle
     *
     * @param string $fulltitle
     * @return ActsPages
     */
    public function setFulltitle($fulltitle)
    {
        $this->fulltitle = $fulltitle;
    
        return $this;
    }

    /**
     * Get fulltitle
     *
     * @return string 
     */
    public function getFulltitle()
    {
        return $this->fulltitle;
    }

    /**
     * Set secure
     *
     * @param boolean $secure
     * @return ActsPages
     */
    public function setSecure($secure)
    {
        $this->secure = $secure;
    
        return $this;
    }

    /**
     * Get secure
     *
     * @return boolean 
     */
    public function getSecure()
    {
        return $this->secure;
    }

    /**
     * Set micro
     *
     * @param boolean $micro
     * @return ActsPages
     */
    public function setMicro($micro)
    {
        $this->micro = $micro;
    
        return $this;
    }

    /**
     * Get micro
     *
     * @return boolean 
     */
    public function getMicro()
    {
        return $this->micro;
    }

    /**
     * Set help
     *
     * @param string $help
     * @return ActsPages
     */
    public function setHelp($help)
    {
        $this->help = $help;
    
        return $this;
    }

    /**
     * Get help
     *
     * @return string 
     */
    public function getHelp()
    {
        return $this->help;
    }

    /**
     * Set ghost
     *
     * @param boolean $ghost
     * @return ActsPages
     */
    public function setGhost($ghost)
    {
        $this->ghost = $ghost;
    
        return $this;
    }

    /**
     * Get ghost
     *
     * @return boolean 
     */
    public function getGhost()
    {
        return $this->ghost;
    }

    /**
     * Set mode
     *
     * @param string $mode
     * @return ActsPages
     */
    public function setMode($mode)
    {
        $this->mode = $mode;
    
        return $this;
    }

    /**
     * Get mode
     *
     * @return string 
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * Set allowsubpage
     *
     * @param integer $allowsubpage
     * @return ActsPages
     */
    public function setAllowsubpage($allowsubpage)
    {
        $this->allowsubpage = $allowsubpage;
    
        return $this;
    }

    /**
     * Get allowsubpage
     *
     * @return integer 
     */
    public function getAllowsubpage()
    {
        return $this->allowsubpage;
    }

    /**
     * Set intertitle
     *
     * @param string $intertitle
     * @return ActsPages
     */
    public function setIntertitle($intertitle)
    {
        $this->intertitle = $intertitle;
    
        return $this;
    }

    /**
     * Get intertitle
     *
     * @return string 
     */
    public function getIntertitle()
    {
        return $this->intertitle;
    }

    /**
     * Set knowledgebase
     *
     * @param boolean $knowledgebase
     * @return ActsPages
     */
    public function setKnowledgebase($knowledgebase)
    {
        $this->knowledgebase = $knowledgebase;
    
        return $this;
    }

    /**
     * Get knowledgebase
     *
     * @return boolean 
     */
    public function getKnowledgebase()
    {
        return $this->knowledgebase;
    }

    /**
     * Set getvars
     *
     * @param string $getvars
     * @return ActsPages
     */
    public function setGetvars($getvars)
    {
        $this->getvars = $getvars;
    
        return $this;
    }

    /**
     * Get getvars
     *
     * @return string 
     */
    public function getGetvars()
    {
        return $this->getvars;
    }

    /**
     * Set postvars
     *
     * @param string $postvars
     * @return ActsPages
     */
    public function setPostvars($postvars)
    {
        $this->postvars = $postvars;
    
        return $this;
    }

    /**
     * Get postvars
     *
     * @return string 
     */
    public function getPostvars()
    {
        return $this->postvars;
    }

    /**
     * Set usepage
     *
     * @param string $usepage
     * @return ActsPages
     */
    public function setUsepage($usepage)
    {
        $this->usepage = $usepage;
    
        return $this;
    }

    /**
     * Get usepage
     *
     * @return string 
     */
    public function getUsepage()
    {
        return $this->usepage;
    }

    /**
     * Set kbid
     *
     * @param integer $kbid
     * @return ActsPages
     */
    public function setKbid($kbid)
    {
        $this->kbid = $kbid;
    
        return $this;
    }

    /**
     * Get kbid
     *
     * @return integer 
     */
    public function getKbid()
    {
        return $this->kbid;
    }

    /**
     * Set rssfeeds
     *
     * @param string $rssfeeds
     * @return ActsPages
     */
    public function setRssfeeds($rssfeeds)
    {
        $this->rssfeeds = $rssfeeds;
    
        return $this;
    }

    /**
     * Get rssfeeds
     *
     * @return string 
     */
    public function getRssfeeds()
    {
        return $this->rssfeeds;
    }

    /**
     * Set locked
     *
     * @param boolean $locked
     * @return ActsPages
     */
    public function setLocked($locked)
    {
        $this->locked = $locked;
    
        return $this;
    }

    /**
     * Get locked
     *
     * @return boolean 
     */
    public function getLocked()
    {
        return $this->locked;
    }

    /**
     * Set virtual
     *
     * @param boolean $virtual
     * @return ActsPages
     */
    public function setVirtual($virtual)
    {
        $this->virtual = $virtual;
    
        return $this;
    }

    /**
     * Get virtual
     *
     * @return boolean 
     */
    public function getVirtual()
    {
        return $this->virtual;
    }

    /**
     * Set paramParser
     *
     * @param boolean $paramParser
     * @return ActsPages
     */
    public function setParamParser($paramParser)
    {
        $this->paramParser = $paramParser;
    
        return $this;
    }

    /**
     * Get paramParser
     *
     * @return boolean 
     */
    public function getParamParser()
    {
        return $this->paramParser;
    }

    /**
     * Set accessPhp
     *
     * @param string $accessPhp
     * @return ActsPages
     */
    public function setAccessPhp($accessPhp)
    {
        $this->accessPhp = $accessPhp;
    
        return $this;
    }

    /**
     * Get accessPhp
     *
     * @return string 
     */
    public function getAccessPhp()
    {
        return $this->accessPhp;
    }

    /**
     * Set subpagetemplate
     *
     * @param string $subpagetemplate
     * @return ActsPages
     */
    public function setSubpagetemplate($subpagetemplate)
    {
        $this->subpagetemplate = $subpagetemplate;
    
        return $this;
    }

    /**
     * Get subpagetemplate
     *
     * @return string 
     */
    public function getSubpagetemplate()
    {
        return $this->subpagetemplate;
    }

    /**
     * Set searchable
     *
     * @param boolean $searchable
     * @return ActsPages
     */
    public function setSearchable($searchable)
    {
        $this->searchable = $searchable;
    
        return $this;
    }

    /**
     * Get searchable
     *
     * @return boolean 
     */
    public function getSearchable()
    {
        return $this->searchable;
    }
}