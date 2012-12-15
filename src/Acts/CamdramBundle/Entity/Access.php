<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Access
 *
 * @ORM\Table(name="acts_access")
 * @ORM\Entity
 */
class Access
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
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="uid", referencedColumnName="id")
     * })
     */
    private $user;

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
    private $issuer_id;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="issuerid", referencedColumnName="id")
     * })
     */
    private $issuer;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="creationdate", type="date", nullable=false)
     */
    private $creation_date;

    /**
     * @var integer
     *
     * @ORM\Column(name="revokeid", type="integer", nullable=false)
     */
    private $revoke_id;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="revokeid", referencedColumnName="id")
     * })
     */
    private $revoker;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="revokedate", type="date", nullable=false)
     */
    private $revoke_date;

    /**
     * @var boolean
     *
     * @ORM\Column(name="contact", type="boolean", nullable=false)
     */
    private $contact;

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
     * Set rid
     *
     * @param integer $rid
     * @return Access
     */
    public function setRid($rid)
    {
        $this->rid = $rid;
    
        return $this;
    }

    /**
     * Get rid
     *
     * @return integer 
     */
    public function getRid()
    {
        return $this->rid;
    }

    /**
     * Set uid
     *
     * @param integer $uid
     * @return Access
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
     * Set type
     *
     * @param string $type
     * @return Access
     */
    public function setType($type)
    {
        $this->type = $type;
    
        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set issuer_id
     *
     * @param integer $issuerId
     * @return Access
     */
    public function setIssuerId($issuerId)
    {
        $this->issuer_id = $issuerId;
    
        return $this;
    }

    /**
     * Get issuer_id
     *
     * @return integer 
     */
    public function getIssuerId()
    {
        return $this->issuer_id;
    }

    /**
     * Set creation_date
     *
     * @param \DateTime $creationDate
     * @return Access
     */
    public function setCreationDate($creationDate)
    {
        $this->creation_date = $creationDate;
    
        return $this;
    }

    /**
     * Get creation_date
     *
     * @return \DateTime 
     */
    public function getCreationDate()
    {
        return $this->creation_date;
    }

    /**
     * Set revoke_id
     *
     * @param integer $revokeId
     * @return Access
     */
    public function setRevokeId($revokeId)
    {
        $this->revoke_id = $revokeId;
    
        return $this;
    }

    /**
     * Get revoke_id
     *
     * @return integer 
     */
    public function getRevokeId()
    {
        return $this->revoke_id;
    }

    /**
     * Set revoke_date
     *
     * @param \DateTime $revokeDate
     * @return Access
     */
    public function setRevokeDate($revokeDate)
    {
        $this->revoke_date = $revokeDate;
    
        return $this;
    }

    /**
     * Get revoke_date
     *
     * @return \DateTime 
     */
    public function getRevokeDate()
    {
        return $this->revoke_date;
    }

    /**
     * Set contact
     *
     * @param boolean $contact
     * @return Access
     */
    public function setContact($contact)
    {
        $this->contact = $contact;
    
        return $this;
    }

    /**
     * Get contact
     *
     * @return boolean 
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * Set user
     *
     * @param \Acts\CamdramBundle\Entity\User $user
     * @return Access
     */
    public function setUser(\Acts\CamdramBundle\Entity\User $user = null)
    {
        $this->user = $user;
    
        return $this;
    }

    /**
     * Get user
     *
     * @return \Acts\CamdramBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set issuer
     *
     * @param \Acts\CamdramBundle\Entity\User $issuer
     * @return Access
     */
    public function setIssuer(\Acts\CamdramBundle\Entity\User $issuer = null)
    {
        $this->issuer = $issuer;
    
        return $this;
    }

    /**
     * Get issuer
     *
     * @return \Acts\CamdramBundle\Entity\User 
     */
    public function getIssuer()
    {
        return $this->issuer;
    }

    /**
     * Set revoker
     *
     * @param \Acts\CamdramBundle\Entity\User $revoker
     * @return Access
     */
    public function setRevoker(\Acts\CamdramBundle\Entity\User $revoker = null)
    {
        $this->revoker = $revoker;
    
        return $this;
    }

    /**
     * Get revoker
     *
     * @return \Acts\CamdramBundle\Entity\User 
     */
    public function getRevoker()
    {
        return $this->revoker;
    }
}