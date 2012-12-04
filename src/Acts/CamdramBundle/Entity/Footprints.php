<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Footprints
 *
 * @ORM\Table(name="footprints")
 * @ORM\Entity
 */
class Footprints
{
    /**
     * @var integer
     *
     * @ORM\Column(name="from", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $from;

    /**
     * @var integer
     *
     * @ORM\Column(name="to", type="integer", nullable=false)
     */
    private $to;

    /**
     * @var integer
     *
     * @ORM\Column(name="time", type="integer", nullable=false)
     */
    private $time;


}
