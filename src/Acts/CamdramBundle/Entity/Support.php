<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * Support
 *
 * @ORM\Table(name="acts_support")
 * @ORM\Entity
 */
class Support
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
     * @ORM\Column(name="supportid", type="integer", nullable=false)
     */
    private $support_id;

    /**
     * @var string
     *
     * @ORM\Column(name="from", type="string", nullable=false)
     */
    private $from;

    /**
     * @var string
     *
     * @ORM\Column(name="to", type="string", nullable=false)
     */
    private $to;

    /**
     * @var string
     *
     * @ORM\Column(name="cc", type="string", nullable=false)
     */
    private $cc;

    /**
     * @var string
     *
     * @ORM\Column(name="subject", type="string", nullable=false)
     */
    private $subject;

    /**
     * @var string
     *
     * @ORM\Column(name="body", type="text", nullable=false)
     */
    private $body;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="ownerid", referencedColumnName="id", nullable=true)
     */
    private $owner;

    /**
     * @var string
     *
     * @ORM\Column(name="state", type="string", length=20, nullable=false)
     */
    private $state;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="datetime", type="datetime", nullable=false)
     */
    private $date_time;

    /**
     * @ORM\OneToMany(targetEntity="Support", mappedBy="parent")
     * @ORM\OrderBy({"id"="DESC"})
     */
    private $children;

    /**
     * @ORM\ManyToOne(targetEntity="Support", inversedBy="children")
     * @ORM\JoinColumn(name="supportid", referencedColumnName="id")
     */
    private $parent;

    public function __construct() 
    {
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
    }
   
    /**
     * Get children
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getChildren()
    {
        return $this->children;
    }


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
     * Set support_id
     *
     * @param integer $supportId
     * @return Support
     */
    public function setSupportId($supportId)
    {
        $this->support_id = $supportId;
    
        return $this;
    }

    /**
     * Get support_id
     *
     * @return integer 
     */
    public function getSupportId()
    {
        return $this->support_id;
    }

    /**
     * Set from
     *
     * @param string $from
     * @return Support
     */
    public function setFrom($from)
    {
        $this->from = $from;
    
        return $this;
    }

    /**
     * Get from
     *
     * @return string 
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * Set to
     *
     * @param string $to
     * @return Support
     */
    public function setTo($to)
    {
        $this->to = $to;
    
        return $this;
    }

    /**
     * Get to
     *
     * @return string 
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * Set cc
     *
     * @param string $cc
     * @return Support
     */
    public function setCc($cc)
    {
        $this->cc = $cc;
    
        return $this;
    }

    /**
     * Get cc
     *
     * @return string 
     */
    public function getCc()
    {
        return $this->cc;
    }

    /**
     * Set subject
     *
     * @param string $subject
     * @return Support
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
     * Set body
     *
     * @param string $body
     * @return Support
     */
    public function setBody($body)
    {
        $this->body = $body;
    
        return $this;
    }

    /**
     * Get body
     *
     * @return string 
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set owner
     *
     * @param \Acts\CamdramBundle\Entity\User $owner
     * @return Support
     */
    public function setOwner(User $owner = null)
    {
        $this->owner = $owner;
    
        return $this;
    }

    /**
     * Get owner
     *
     * @return \Acts\CamdramBundle\Entity\User
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Set state
     * state for issues should be one of [unassigned, assigned, closed]
     *
     * @param string $state
     * @return Support
     */
    public function setState($state)
    {
        $this->state = $state;
    
        return $this;
    }

    /**
     * Get state
     *
     * @return string 
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set date_time
     *
     * @param \DateTime $dateTime
     * @return Support
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
}

