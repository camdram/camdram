<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ActsForumsMessages
 *
 * @ORM\Table(name="acts_forums_messages")
 * @ORM\Entity
 */
class ForumMessage
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



    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set replyid
     *
     * @param integer $replyid
     * @return ActsForumsMessages
     */
    public function setReplyid($replyid)
    {
        $this->replyid = $replyid;
    
        return $this;
    }

    /**
     * Get replyid
     *
     * @return integer 
     */
    public function getReplyid()
    {
        return $this->replyid;
    }

    /**
     * Set forumid
     *
     * @param integer $forumid
     * @return ActsForumsMessages
     */
    public function setForumid($forumid)
    {
        $this->forumid = $forumid;
    
        return $this;
    }

    /**
     * Get forumid
     *
     * @return integer 
     */
    public function getForumid()
    {
        return $this->forumid;
    }

    /**
     * Set uid
     *
     * @param integer $uid
     * @return ActsForumsMessages
     */
    public function setUid($uid)
    {
        $this->uid = $uid;
    
        return $this;
    }

    /**
     * Get uid
     *
     * @return integer 
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * Set datetime
     *
     * @param \DateTime $datetime
     * @return ActsForumsMessages
     */
    public function setDatetime($datetime)
    {
        $this->datetime = $datetime;
    
        return $this;
    }

    /**
     * Get datetime
     *
     * @return \DateTime 
     */
    public function getDatetime()
    {
        return $this->datetime;
    }

    /**
     * Set subject
     *
     * @param string $subject
     * @return ActsForumsMessages
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    
        return $this;
    }

    /**
     * Get subject
     *
     * @return string 
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set message
     *
     * @param string $message
     * @return ActsForumsMessages
     */
    public function setMessage($message)
    {
        $this->message = $message;
    
        return $this;
    }

    /**
     * Get message
     *
     * @return string 
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set resourceid
     *
     * @param integer $resourceid
     * @return ActsForumsMessages
     */
    public function setResourceid($resourceid)
    {
        $this->resourceid = $resourceid;
    
        return $this;
    }

    /**
     * Get resourceid
     *
     * @return integer 
     */
    public function getResourceid()
    {
        return $this->resourceid;
    }

    /**
     * Set ancestorid
     *
     * @param integer $ancestorid
     * @return ActsForumsMessages
     */
    public function setAncestorid($ancestorid)
    {
        $this->ancestorid = $ancestorid;
    
        return $this;
    }

    /**
     * Get ancestorid
     *
     * @return integer 
     */
    public function getAncestorid()
    {
        return $this->ancestorid;
    }

    /**
     * Set lastpost
     *
     * @param \DateTime $lastpost
     * @return ActsForumsMessages
     */
    public function setLastpost($lastpost)
    {
        $this->lastpost = $lastpost;
    
        return $this;
    }

    /**
     * Get lastpost
     *
     * @return \DateTime 
     */
    public function getLastpost()
    {
        return $this->lastpost;
    }
}