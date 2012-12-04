<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ActsAuthtokens
 *
 * @ORM\Table(name="acts_authtokens")
 * @ORM\Entity
 */
class ActsAuthtokens
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
     * @ORM\Column(name="token", type="string", length=50, nullable=false)
     */
    private $token;

    /**
     * @var integer
     *
     * @ORM\Column(name="siteid", type="integer", nullable=false)
     */
    private $siteid;

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
     * @var \DateTime
     *
     * @ORM\Column(name="issued", type="datetime", nullable=false)
     */
    private $issued;


}
