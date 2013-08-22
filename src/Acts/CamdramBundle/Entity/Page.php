<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Page
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
     * @ORM\Column(name="title", type="string", length=255, nullable=false)
     */
    private $title;

    /**
     * @var integer
     *
     * @ORM\Column(name="parentid", type="integer", nullable=false)
     */
    private $parent_id;

    /**
     * @var integer
     *
     * @ORM\Column(name="sortcode", type="integer", nullable=false)
     */
    private $sort_code;

    /**
     * @var string
     *
     * @ORM\Column(name="fulltitle", type="string", length=255, nullable=false)
     */
    private $full_title;

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
     * @ORM\Column(name="mode", type="string", length=50, nullable=true)
     */
    private $mode;

    /**
     * @var integer
     *
     * @ORM\Column(name="allowsubpage", type="boolean", nullable=false)
     */
    private $allow_sub_page;

    /**
     * @var string
     *
     * @ORM\Column(name="intertitle", type="text", nullable=false)
     */
    private $inter_title;

    /**
     * @var boolean
     *
     * @ORM\Column(name="knowledgebase", type="boolean", nullable=false)
     */
    private $knowledge_base;

    /**
     * @var string
     *
     * @ORM\Column(name="getvars", type="text", nullable=false)
     */
    private $get_vars;

    /**
     * @var string
     *
     * @ORM\Column(name="postvars", type="text", nullable=false)
     */
    private $post_vars;

    /**
     * @var string
     *
     * @ORM\Column(name="usepage", type="string", length=255, nullable=false)
     */
    private $use_page;

    /**
     * @var integer
     *
     * @ORM\Column(name="kbid", type="integer", nullable=false)
     */
    private $kb_id;

    /**
     * @var string
     *
     * @ORM\Column(name="rssfeeds", type="text", nullable=false)
     */
    private $rss_feeds;

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
    private $param_parser;

    /**
     * @var string
     *
     * @ORM\Column(name="access_php", type="text", nullable=false)
     */
    private $access_php;

    /**
     * @var string
     *
     * @ORM\Column(name="subpagetemplate", type="text", nullable=false)
     */
    private $subpage_template;

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
     * @return Page
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
     * Set parent_id
     *
     * @param integer $parentId
     * @return Page
     */
    public function setParentId($parentId)
    {
        $this->parent_id = $parentId;
    
        return $this;
    }

    /**
     * Get parent_id
     *
     * @return integer 
     */
    public function getParentId()
    {
        return $this->parent_id;
    }

    /**
     * Set sort_code
     *
     * @param integer $sortCode
     * @return Page
     */
    public function setSortCode($sortCode)
    {
        $this->sort_code = $sortCode;
    
        return $this;
    }

    /**
     * Get sort_code
     *
     * @return integer 
     */
    public function getSortCode()
    {
        return $this->sort_code;
    }

    /**
     * Set full_title
     *
     * @param string $fullTitle
     * @return Page
     */
    public function setFullTitle($fullTitle)
    {
        $this->full_title = $fullTitle;
    
        return $this;
    }

    /**
     * Get full_title
     *
     * @return string 
     */
    public function getFullTitle()
    {
        return $this->full_title;
    }

    /**
     * Set secure
     *
     * @param boolean $secure
     * @return Page
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
     * @return Page
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
     * @return Page
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
     * @return Page
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
     * @return Page
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
     * Set allow_sub_page
     *
     * @param integer $allowSubPage
     * @return Page
     */
    public function setAllowSubPage($allowSubPage)
    {
        $this->allow_sub_page = $allowSubPage;
    
        return $this;
    }

    /**
     * Get allow_sub_page
     *
     * @return integer 
     */
    public function getAllowSubPage()
    {
        return $this->allow_sub_page;
    }

    /**
     * Set inter_title
     *
     * @param string $interTitle
     * @return Page
     */
    public function setInterTitle($interTitle)
    {
        $this->inter_title = $interTitle;
    
        return $this;
    }

    /**
     * Get inter_title
     *
     * @return string 
     */
    public function getInterTitle()
    {
        return $this->inter_title;
    }

    /**
     * Set knowledge_base
     *
     * @param boolean $knowledgeBase
     * @return Page
     */
    public function setKnowledgeBase($knowledgeBase)
    {
        $this->knowledge_base = $knowledgeBase;
    
        return $this;
    }

    /**
     * Get knowledge_base
     *
     * @return boolean 
     */
    public function getKnowledgeBase()
    {
        return $this->knowledge_base;
    }

    /**
     * Set get_vars
     *
     * @param string $getVars
     * @return Page
     */
    public function setGetVars($getVars)
    {
        $this->get_vars = $getVars;
    
        return $this;
    }

    /**
     * Get get_vars
     *
     * @return string 
     */
    public function getGetVars()
    {
        return $this->get_vars;
    }

    /**
     * Set post_vars
     *
     * @param string $postVars
     * @return Page
     */
    public function setPostVars($postVars)
    {
        $this->post_vars = $postVars;
    
        return $this;
    }

    /**
     * Get post_vars
     *
     * @return string 
     */
    public function getPostVars()
    {
        return $this->post_vars;
    }

    /**
     * Set use_page
     *
     * @param string $usePage
     * @return Page
     */
    public function setUsePage($usePage)
    {
        $this->use_page = $usePage;
    
        return $this;
    }

    /**
     * Get use_page
     *
     * @return string 
     */
    public function getUsePage()
    {
        return $this->use_page;
    }

    /**
     * Set kb_id
     *
     * @param integer $kbId
     * @return Page
     */
    public function setKbId($kbId)
    {
        $this->kb_id = $kbId;
    
        return $this;
    }

    /**
     * Get kb_id
     *
     * @return integer 
     */
    public function getKbId()
    {
        return $this->kb_id;
    }

    /**
     * Set rss_feeds
     *
     * @param string $rssFeeds
     * @return Page
     */
    public function setRssFeeds($rssFeeds)
    {
        $this->rss_feeds = $rssFeeds;
    
        return $this;
    }

    /**
     * Get rss_feeds
     *
     * @return string 
     */
    public function getRssFeeds()
    {
        return $this->rss_feeds;
    }

    /**
     * Set locked
     *
     * @param boolean $locked
     * @return Page
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
     * @return Page
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
     * Set param_parser
     *
     * @param boolean $paramParser
     * @return Page
     */
    public function setParamParser($paramParser)
    {
        $this->param_parser = $paramParser;
    
        return $this;
    }

    /**
     * Get param_parser
     *
     * @return boolean 
     */
    public function getParamParser()
    {
        return $this->param_parser;
    }

    /**
     * Set access_php
     *
     * @param string $accessPhp
     * @return Page
     */
    public function setAccessPhp($accessPhp)
    {
        $this->access_php = $accessPhp;
    
        return $this;
    }

    /**
     * Get access_php
     *
     * @return string 
     */
    public function getAccessPhp()
    {
        return $this->access_php;
    }

    /**
     * Set subpage_template
     *
     * @param string $subpageTemplate
     * @return Page
     */
    public function setSubpageTemplate($subpageTemplate)
    {
        $this->subpage_template = $subpageTemplate;
    
        return $this;
    }

    /**
     * Get subpage_template
     *
     * @return string 
     */
    public function getSubpageTemplate()
    {
        return $this->subpage_template;
    }

    /**
     * Set searchable
     *
     * @param boolean $searchable
     * @return Page
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