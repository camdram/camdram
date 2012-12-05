<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ActsEmail
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
    private $emailid;

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
     * @var string
     *
     * @ORM\Column(name="title", type="text", nullable=false)
     */
    private $title;

    /**
     * @var boolean
     *
     * @ORM\Column(name="public_add", type="boolean", nullable=false)
     */
    private $publicAdd;

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
    private $listid;

    /**
     * @var boolean
     *
     * @ORM\Column(name="deleteonsend", type="boolean", nullable=false)
     */
    private $deleteonsend;



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
     * @return ActsEmail
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
     * Set publicAdd
     *
     * @param boolean $publicAdd
     * @return ActsEmail
     */
    public function setPublicAdd($publicAdd)
    {
        $this->publicAdd = $publicAdd;
    
        return $this;
    }

    /**
     * Get publicAdd
     *
     * @return boolean 
     */
    public function getPublicAdd()
    {
        return $this->publicAdd;
    }

    /**
     * Set summary
     *
     * @param string $summary
     * @return ActsEmail
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
     * @return ActsEmail
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
     * Set listid
     *
     * @param string $listid
     * @return ActsEmail
     */
    public function setListid($listid)
    {
        $this->listid = $listid;
    
        return $this;
    }

    /**
     * Get listid
     *
     * @return string 
     */
    public function getListid()
    {
        return $this->listid;
    }

    /**
     * Set deleteonsend
     *
     * @param boolean $deleteonsend
     * @return ActsEmail
     */
    public function setDeleteonsend($deleteonsend)
    {
        $this->deleteonsend = $deleteonsend;
    
        return $this;
    }

    /**
     * Get deleteonsend
     *
     * @return boolean 
     */
    public function getDeleteonsend()
    {
        return $this->deleteonsend;
    }

    /**
     * Set userid
     *
     * @param \Acts\CamdramBundle\Entity\ActsUsers $userid
     * @return ActsEmail
     */
    public function setUserid(\Acts\CamdramBundle\Entity\ActsUsers $userid = null)
    {
        $this->userid = $userid;
    
        return $this;
    }

    /**
     * Get userid
     *
     * @return \Acts\CamdramBundle\Entity\ActsUsers 
     */
    public function getUserid()
    {
        return $this->userid;
    }
}