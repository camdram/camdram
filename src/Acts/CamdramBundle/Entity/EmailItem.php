<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EmailItem
 *
 * @ORM\Table(name="acts_email_items")
 * @ORM\Entity
 */
class EmailItem
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
    private $order_id;

    /**
     * @var integer
     *
     * @ORM\Column(name="creatorid", type="integer", nullable=false)
     */
    private $creator_id;

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
     * Set emailid
     *
     * @param integer $emailid
     * @return EmailItem
     */
    public function setEmailid($emailid)
    {
        $this->emailid = $emailid;
    
        return $this;
    }

    /**
     * Get emailid
     *
     * @return integer 
     */
    public function getEmailid()
    {
        return $this->emailid;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return EmailItem
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
     * Set text
     *
     * @param string $text
     * @return EmailItem
     */
    public function setText($text)
    {
        $this->text = $text;
    
        return $this;
    }

    /**
     * Get text
     *
     * @return string 
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set order_id
     *
     * @param float $orderId
     * @return EmailItem
     */
    public function setOrderId($orderId)
    {
        $this->order_id = $orderId;
    
        return $this;
    }

    /**
     * Get order_id
     *
     * @return float 
     */
    public function getOrderId()
    {
        return $this->order_id;
    }

    /**
     * Set creator_id
     *
     * @param integer $creatorId
     * @return EmailItem
     */
    public function setCreatorId($creatorId)
    {
        $this->creator_id = $creatorId;
    
        return $this;
    }

    /**
     * Get creator_id
     *
     * @return integer 
     */
    public function getCreatorId()
    {
        return $this->creator_id;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return EmailItem
     */
    public function setCreated($created)
    {
        $this->created = $created;
    
        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime 
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set protect
     *
     * @param boolean $protect
     * @return EmailItem
     */
    public function setProtect($protect)
    {
        $this->protect = $protect;
    
        return $this;
    }

    /**
     * Get protect
     *
     * @return boolean 
     */
    public function getProtect()
    {
        return $this->protect;
    }
}