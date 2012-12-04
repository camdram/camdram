<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ActsPendingaccess
 *
 * @ORM\Table(name="acts_pendingaccess")
 * @ORM\Entity
 */
class ActsPendingaccess
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
     * @var string
     *
     * @ORM\Column(name="email", type="text", nullable=false)
     */
    private $email;

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


}
