<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ActsAccess
 *
 * @ORM\Table(name="acts_access")
 * @ORM\Entity
 */
class ActsAccess
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
     * @ORM\Column(name="rid", type="integer", nullable=false)
     */
    private $rid;

    /**
     * @var integer
     *
     * @ORM\Column(name="uid", type="integer", nullable=false)
     */
    private $uid;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", nullable=false)
     */
    private $type;

    /**
     * @var integer
     *
     * @ORM\Column(name="issuerid", type="integer", nullable=false)
     */
    private $issuerid;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="creationdate", type="date", nullable=false)
     */
    private $creationdate;

    /**
     * @var integer
     *
     * @ORM\Column(name="revokeid", type="integer", nullable=false)
     */
    private $revokeid;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="revokedate", type="date", nullable=false)
     */
    private $revokedate;

    /**
     * @var boolean
     *
     * @ORM\Column(name="contact", type="boolean", nullable=false)
     */
    private $contact;


}
