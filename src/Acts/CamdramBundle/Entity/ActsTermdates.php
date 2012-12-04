<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ActsTermdates
 *
 * @ORM\Table(name="acts_termdates")
 * @ORM\Entity
 */
class ActsTermdates
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
     * @ORM\Column(name="friendlyname", type="text", nullable=false)
     */
    private $friendlyname;

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
     * @var boolean
     *
     * @ORM\Column(name="firstweek", type="boolean", nullable=false)
     */
    private $firstweek;

    /**
     * @var boolean
     *
     * @ORM\Column(name="lastweek", type="boolean", nullable=false)
     */
    private $lastweek;

    /**
     * @var boolean
     *
     * @ORM\Column(name="displayweek", type="boolean", nullable=false)
     */
    private $displayweek;

    /**
     * @var string
     *
     * @ORM\Column(name="vacation", type="text", nullable=false)
     */
    private $vacation;


}
