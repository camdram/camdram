<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ActsEmail
 *
 * @ORM\Table(name="acts_email")
 * @ORM\Entity
 */
class ActsEmail
{
    /**
     * @var integer
     *
     * @ORM\Column(name="emailid", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $emailid;

    /**
     * @var \ActsUsers
     *
     * @ORM\ManyToOne(targetEntity="ActsUsers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="userid", referencedColumnName="id")
     * })
     */
    private $userid;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="text", nullable=false)
     */
    private $title;

    /**
     * @var boolean
     *
     * @ORM\Column(name="public_add", type="boolean", nullable=false)
     */
    private $publicAdd;

    /**
     * @var string
     *
     * @ORM\Column(name="summary", type="text", nullable=false)
     */
    private $summary;

    /**
     * @var integer
     *
     * @ORM\Column(name="from", type="integer", nullable=false)
     */
    private $from;

    /**
     * @var string
     *
     * @ORM\Column(name="listid", type="text", nullable=false)
     */
    private $listid;

    /**
     * @var boolean
     *
     * @ORM\Column(name="deleteonsend", type="boolean", nullable=false)
     */
    private $deleteonsend;


}
