<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ActsSupport
 *
 * @ORM\Table(name="acts_support")
 * @ORM\Entity
 */
class ActsSupport
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
     * @ORM\Column(name="supportid", type="integer", nullable=false)
     */
    private $supportid;

    /**
     * @var string
     *
     * @ORM\Column(name="from", type="text", nullable=false)
     */
    private $from;

    /**
     * @var string
     *
     * @ORM\Column(name="to", type="text", nullable=false)
     */
    private $to;

    /**
     * @var string
     *
     * @ORM\Column(name="cc", type="text", nullable=false)
     */
    private $cc;

    /**
     * @var string
     *
     * @ORM\Column(name="subject", type="text", nullable=false)
     */
    private $subject;

    /**
     * @var string
     *
     * @ORM\Column(name="body", type="text", nullable=false)
     */
    private $body;

    /**
     * @var integer
     *
     * @ORM\Column(name="ownerid", type="integer", nullable=false)
     */
    private $ownerid;

    /**
     * @var string
     *
     * @ORM\Column(name="state", type="string", nullable=false)
     */
    private $state;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="datetime", type="datetime", nullable=false)
     */
    private $datetime;


}
