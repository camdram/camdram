<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ActsForumsMessages
 *
 * @ORM\Table(name="acts_forums_messages")
 * @ORM\Entity
 */
class ActsForumsMessages
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
     * @ORM\Column(name="replyid", type="integer", nullable=false)
     */
    private $replyid;

    /**
     * @var integer
     *
     * @ORM\Column(name="forumid", type="integer", nullable=false)
     */
    private $forumid;

    /**
     * @var integer
     *
     * @ORM\Column(name="uid", type="integer", nullable=false)
     */
    private $uid;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="datetime", type="datetime", nullable=false)
     */
    private $datetime;

    /**
     * @var string
     *
     * @ORM\Column(name="subject", type="string", length=255, nullable=false)
     */
    private $subject;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="text", nullable=false)
     */
    private $message;

    /**
     * @var integer
     *
     * @ORM\Column(name="resourceid", type="integer", nullable=false)
     */
    private $resourceid;

    /**
     * @var integer
     *
     * @ORM\Column(name="ancestorid", type="integer", nullable=false)
     */
    private $ancestorid;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lastpost", type="datetime", nullable=false)
     */
    private $lastpost;


}
