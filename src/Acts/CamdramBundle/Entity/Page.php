<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Page
 *
 * The page table is deprecated in Camdram 2.0, as page information is stored
 * using the PHPCR. This class only defines 'getters' as a consequence.

 * @ORM\Table(name="acts_pages")
 * @ORM\Entity(repositoryClass="Acts\CamdramBundle\Entity\PageRepository")
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
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
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
     * Get sort_code
     *
     * @return integer 
     */
    public function getSortCode()
    {
        return $this->sort_code;
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
     * Get secure
     *
     * @return boolean 
     */
    public function getSecure()
    {
        return $this->secure;
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
     * Get help
     *
     * @return string 
     */
    public function getHelp()
    {
        return $this->help;
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
     * Get mode
     *
     * @return string 
     */
    public function getMode()
    {
        return $this->mode;
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
     * Get inter_title
     *
     * @return string 
     */
    public function getInterTitle()
    {
        return $this->inter_title;
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
     * Get get_vars
     *
     * @return string 
     */
    public function getGetVars()
    {
        return $this->get_vars;
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
     * Get use_page
     *
     * @return string 
     */
    public function getUsePage()
    {
        return $this->use_page;
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
     * Get rss_feeds
     *
     * @return string 
     */
    public function getRssFeeds()
    {
        return $this->rss_feeds;
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
     * Get virtual
     *
     * @return boolean 
     */
    public function getVirtual()
    {
        return $this->virtual;
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
     * Get access_php
     *
     * @return string 
     */
    public function getAccessPhp()
    {
        return $this->access_php;
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
     * Get searchable
     *
     * @return boolean 
     */
    public function getSearchable()
    {
        return $this->searchable;
    }
}

