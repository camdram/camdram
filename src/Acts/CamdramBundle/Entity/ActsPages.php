<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ActsPages
 *
 * @ORM\Table(name="acts_pages")
 * @ORM\Entity
 */
class ActsPages
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


}
