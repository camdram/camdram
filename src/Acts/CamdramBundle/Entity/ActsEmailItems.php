<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ActsEmailItems
 *
 * @ORM\Table(name="acts_email_items")
 * @ORM\Entity
 */
class ActsEmailItems
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
     * @ORM\Column(name="emailid", type="integer", nullable=false)
     */
    private $emailid;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="text", nullable=false)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="text", nullable=false)
     */
    private $text;

    /**
     * @var float
     *
     * @ORM\Column(name="orderid", type="float", nullable=false)
     */
    private $orderid;

    /**
     * @var integer
     *
     * @ORM\Column(name="creatorid", type="integer", nullable=false)
     */
    private $creatorid;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime", nullable=false)
     */
    private $created;

    /**
     * @var boolean
     *
     * @ORM\Column(name="protect", type="boolean", nullable=false)
     */
    private $protect;


}
