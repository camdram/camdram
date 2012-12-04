<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ActsTechies
 *
 * @ORM\Table(name="acts_techies")
 * @ORM\Entity
 */
class ActsTechies
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
     * @var integer
     *
     * @ORM\Column(name="showid", type="integer", nullable=false)
     */
    private $showid;

    /**
     * @var string
     *
     * @ORM\Column(name="positions", type="text", nullable=false)
     */
    private $positions;

    /**
     * @var string
     *
     * @ORM\Column(name="contact", type="text", nullable=false)
     */
    private $contact;

    /**
     * @var boolean
     *
     * @ORM\Column(name="deadline", type="boolean", nullable=false)
     */
    private $deadline;

    /**
     * @var string
     *
     * @ORM\Column(name="deadlinetime", type="text", nullable=false)
     */
    private $deadlinetime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="expiry", type="date", nullable=false)
     */
    private $expiry;

    /**
     * @var boolean
     *
     * @ORM\Column(name="display", type="boolean", nullable=false)
     */
    private $display;

    /**
     * @var boolean
     *
     * @ORM\Column(name="remindersent", type="boolean", nullable=false)
     */
    private $remindersent;

    /**
     * @var string
     *
     * @ORM\Column(name="techextra", type="text", nullable=false)
     */
    private $techextra;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lastupdated", type="datetime", nullable=false)
     */
    private $lastupdated;


}
