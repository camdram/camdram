<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ActsShows
 *
 * @ORM\Table(name="acts_shows")
 * @ORM\Entity
 */
class ActsShows
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
     * @ORM\Column(name="dates", type="text", nullable=false)
     */
    private $dates;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="text", nullable=false)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="author", type="text", nullable=false)
     */
    private $author;

    /**
     * @var string
     *
     * @ORM\Column(name="prices", type="text", nullable=false)
     */
    private $prices;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=false)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="photourl", type="text", nullable=true)
     */
    private $photourl;

    /**
     * @var string
     *
     * @ORM\Column(name="venue", type="text", nullable=false)
     */
    private $venue;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="excludedate", type="date", nullable=false)
     */
    private $excludedate;

    /**
     * @var string
     *
     * @ORM\Column(name="society", type="text", nullable=true)
     */
    private $society;

    /**
     * @var boolean
     *
     * @ORM\Column(name="techsend", type="boolean", nullable=false)
     */
    private $techsend;

    /**
     * @var boolean
     *
     * @ORM\Column(name="actorsend", type="boolean", nullable=false)
     */
    private $actorsend;

    /**
     * @var string
     *
     * @ORM\Column(name="audextra", type="text", nullable=true)
     */
    private $audextra;

    /**
     * @var integer
     *
     * @ORM\Column(name="socid", type="integer", nullable=false)
     */
    private $socid;

    /**
     * @var integer
     *
     * @ORM\Column(name="venid", type="integer", nullable=false)
     */
    private $venid;

    /**
     * @var integer
     *
     * @ORM\Column(name="authorizeid", type="integer", nullable=false)
     */
    private $authorizeid;

    /**
     * @var boolean
     *
     * @ORM\Column(name="entered", type="boolean", nullable=false)
     */
    private $entered;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="entryexpiry", type="date", nullable=false)
     */
    private $entryexpiry;

    /**
     * @var string
     *
     * @ORM\Column(name="category", type="string", nullable=false)
     */
    private $category;

    /**
     * @var string
     *
     * @ORM\Column(name="bookingcode", type="text", nullable=false)
     */
    private $bookingcode;

    /**
     * @var integer
     *
     * @ORM\Column(name="primaryref", type="integer", nullable=false)
     */
    private $primaryref;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="timestamp", type="datetime", nullable=false)
     */
    private $timestamp;


}
