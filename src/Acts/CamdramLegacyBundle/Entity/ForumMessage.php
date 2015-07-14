<?php

namespace Acts\CamdramLegacyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ForumsMessage
 *
 * @ORM\Table(name="acts_forums_messages")
 * @ORM\Entity
 */
class ForumMessage
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="replyid", type="integer", nullable=false)
     */
    private $reply_id;

    /**
     * @var int
     *
     * @ORM\Column(name="forumid", type="integer", nullable=false)
     */
    private $forum_id;

    /**
     * @var int
     *
     * @ORM\Column(name="uid", type="integer", nullable=false)
     */
    private $user_id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="datetime", type="datetime", nullable=false)
     */
    private $date_time;

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
     * @var int
     *
     * @ORM\Column(name="resourceid", type="integer", nullable=false)
     */
    private $resource_id;

    /**
     * @var int
     *
     * @ORM\Column(name="ancestorid", type="integer", nullable=false)
     */
    private $ancestor_id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lastpost", type="datetime", nullable=false)
     */
    private $last_post;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set reply_id
     *
     * @param int $replyId
     *
     * @return ForumMessage
     */
    public function setReplyId($replyId)
    {
        $this->reply_id = $replyId;

        return $this;
    }

    /**
     * Get reply_id
     *
     * @return int
     */
    public function getReplyId()
    {
        return $this->reply_id;
    }

    /**
     * Set forum_id
     *
     * @param int $forumId
     *
     * @return ForumMessage
     */
    public function setForumId($forumId)
    {
        $this->forum_id = $forumId;

        return $this;
    }

    /**
     * Get forum_id
     *
     * @return int
     */
    public function getForumId()
    {
        return $this->forum_id;
    }

    /**
     * Set user_id
     *
     * @param int $userId
     *
     * @return ForumMessage
     */
    public function setUserId($userId)
    {
        $this->user_id = $userId;

        return $this;
    }

    /**
     * Get user_id
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * Set date_time
     *
     * @param \DateTime $dateTime
     *
     * @return ForumMessage
     */
    public function setDateTime($dateTime)
    {
        $this->date_time = $dateTime;

        return $this;
    }

    /**
     * Get date_time
     *
     * @return \DateTime
     */
    public function getDateTime()
    {
        return $this->date_time;
    }

    /**
     * Set subject
     *
     * @param string $subject
     *
     * @return ForumMessage
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
     *
     * @return ForumMessage
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
     * Set resource_id
     *
     * @param int $resourceId
     *
     * @return ForumMessage
     */
    public function setResourceId($resourceId)
    {
        $this->resource_id = $resourceId;

        return $this;
    }

    /**
     * Get resource_id
     *
     * @return int
     */
    public function getResourceId()
    {
        return $this->resource_id;
    }

    /**
     * Set ancestor_id
     *
     * @param int $ancestorId
     *
     * @return ForumMessage
     */
    public function setAncestorId($ancestorId)
    {
        $this->ancestor_id = $ancestorId;

        return $this;
    }

    /**
     * Get ancestor_id
     *
     * @return int
     */
    public function getAncestorId()
    {
        return $this->ancestor_id;
    }

    /**
     * Set last_post
     *
     * @param \DateTime $lastPost
     *
     * @return ForumMessage
     */
    public function setLastPost($lastPost)
    {
        $this->last_post = $lastPost;

        return $this;
    }

    /**
     * Get last_post
     *
     * @return \DateTime
     */
    public function getLastPost()
    {
        return $this->last_post;
    }
}
