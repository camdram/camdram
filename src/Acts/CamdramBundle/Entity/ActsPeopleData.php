<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ActsPeopleData
 *
 * @ORM\Table(name="acts_people_data")
 * @ORM\Entity
 */
class ActsPeopleData
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
     * @var integer
     *
     * @ORM\Column(name="mapto", type="integer", nullable=false)
     */
    private $mapto;

    /**
     * @var boolean
     *
     * @ORM\Column(name="norobots", type="boolean", nullable=false)
     */
    private $norobots;


}
