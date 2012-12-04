<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ActsTechiesPositions
 *
 * @ORM\Table(name="acts_techies_positions")
 * @ORM\Entity
 */
class ActsTechiesPositions
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
     * @ORM\Column(name="position", type="text", nullable=false)
     */
    private $position;

    /**
     * @var float
     *
     * @ORM\Column(name="orderid", type="float", nullable=false)
     */
    private $orderid;


}
