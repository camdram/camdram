<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ActsPerformances
 *
 * @ORM\Table(name="acts_performances")
 * @ORM\Entity
 */
class ActsPerformances
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
     * @var \ActsShows
     *
     * @ORM\ManyToOne(targetEntity="ActsShows")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sid", referencedColumnName="id")
     * })
     */
    private $sid;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="startdate", type="date", nullable=false)
     */
    private $startdate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="enddate", type="date", nullable=false)
     */
    private $enddate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="excludedate", type="date", nullable=false)
     */
    private $excludedate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="time", type="time", nullable=false)
     */
    private $time;

    /**
     * @var \ActsSocieties
     *
     * @ORM\ManyToOne(targetEntity="ActsSocieties")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="venid", referencedColumnName="id")
     * })
     */
    private $venid;

    /**
     * @var string
     *
     * @ORM\Column(name="venue", type="text", nullable=false)
     */
    private $venue;


}
