<?php

namespace Acts\CamdramLegacyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Email
 *
 * @ORM\Table(name="acts_email")
 * @ORM\Entity
 */
class Email
{
    /**
     * @var integer
     *
     * @ORM\Column(name="emailid", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $email_id;

    /**
     * @var integer
     *
     * @ORM\Column(name="userid", type="integer", nullable=true)
     */
    private $user_id;

    /**
     * @var \Acts\CamdramSecurityBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="\Acts\CamdramSecurityBundle\Entity\User", inversedBy="email_builders")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="userid", referencedColumnName="id")
     * })
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=false)
     */
    private $title;

    /**
     * @var boolean
     *
     * @ORM\Column(name="public_add", type="boolean", nullable=false)
     */
    private $public_add;

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
    private $list_id;

    /**
     * @var boolean
     *
     * @ORM\Column(name="deleteonsend", type="boolean", nullable=false)
     */
    private $delete_on_send;


    /**
     * Get email_id
     *
     * @return integer
     */
    public function getEmailId()
    {
        return $this->email_id;
    }

    /**
     * Set user_id
     *
     * @param integer $userId
     * @return Email
     */
    public function setUserId($userId)
    {
        $this->user_id = $userId;

        return $this;
    }

    /**
     * Get user_id
     *
     * @return integer
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Email
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set public_add
     *
     * @param boolean $publicAdd
     * @return Email
     */
    public function setPublicAdd($publicAdd)
    {
        $this->public_add = $publicAdd;

        return $this;
    }

    /**
     * Get public_add
     *
     * @return boolean
     */
    public function getPublicAdd()
    {
        return $this->public_add;
    }

    /**
     * Set summary
     *
     * @param string $summary
     * @return Email
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;

        return $this;
    }

    /**
     * Get summary
     *
     * @return string
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * Set from
     *
     * @param integer $from
     * @return Email
     */
    public function setFrom($from)
    {
        $this->from = $from;

        return $this;
    }

    /**
     * Get from
     *
     * @return integer
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * Set list_id
     *
     * @param string $listId
     * @return Email
     */
    public function setListId($listId)
    {
        $this->list_id = $listId;

        return $this;
    }

    /**
     * Get list_id
     *
     * @return string
     */
    public function getListId()
    {
        return $this->list_id;
    }

    /**
     * Set delete_on_send
     *
     * @param boolean $deleteOnSend
     * @return Email
     */
    public function setDeleteOnSend($deleteOnSend)
    {
        $this->delete_on_send = $deleteOnSend;

        return $this;
    }

    /**
     * Get delete_on_send
     *
     * @return boolean
     */
    public function getDeleteOnSend()
    {
        return $this->delete_on_send;
    }

    /**
     * Set user
     *
     * @param \Acts\CamdramSecurityBundle\Entity\User $user
     * @return Email
     */
    public function setUser(\Acts\CamdramSecurityBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Acts\CamdramSecurityBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }
}
