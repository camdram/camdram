<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ActsSocieties
 *
 * @ORM\Table(name="acts_societies")
 * @ORM\Entity
 */
class ActsSocieties
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
     * @ORM\Column(name="description", type="text", nullable=false)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="shortname", type="text", nullable=false)
     */
    private $shortname;

    /**
     * @var string
     *
     * @ORM\Column(name="college", type="text", nullable=true)
     */
    private $college;

    /**
     * @var boolean
     *
     * @ORM\Column(name="type", type="boolean", nullable=false)
     */
    private $type;

    /**
     * @var boolean
     *
     * @ORM\Column(name="affiliate", type="boolean", nullable=false)
     */
    private $affiliate;

    /**
     * @var string
     *
     * @ORM\Column(name="logourl", type="text", nullable=true)
     */
    private $logourl;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="expires", type="date", nullable=false)
     */
    private $expires;


}
