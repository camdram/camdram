<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ActsAccess
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
     * @var \ActsUsers
     *
     * @ORM\ManyToOne(targetEntity="ActsUsers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="uid", referencedColumnName="id")
     * })
     */
    private $uid;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", nullable=false)
     */
    private $type;

    /**
     * @var \ActsUsers
     *
     * @ORM\ManyToOne(targetEntity="ActsUsers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="issuerid", referencedColumnName="id")
     * })
     */
    private $issuerid;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="creationdate", type="date", nullable=false)
     */
    private $creationdate;

    /**
     * @var \ActsUsers
     *
     * @ORM\ManyToOne(targetEntity="ActsUsers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="revokeid", referencedColumnName="id")
     * })
     */
    private $revokeid;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="revokedate", type="date", nullable=false)
     */
    private $revokedate;

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
     * @return ActsAccess
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
     * Set type
     *
     * @param string $type
     * @return ActsAccess
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
     * Set creationdate
     *
     * @param \DateTime $creationdate
     * @return ActsAccess
     */
    public function setCreationdate($creationdate)
    {
        $this->creationdate = $creationdate;
    
        return $this;
    }

    /**
     * Get creationdate
     *
     * @return \DateTime 
     */
    public function getCreationdate()
    {
        return $this->creationdate;
    }

    /**
     * Set revokedate
     *
     * @param \DateTime $revokedate
     * @return ActsAccess
     */
    public function setRevokedate($revokedate)
    {
        $this->revokedate = $revokedate;
    
        return $this;
    }

    /**
     * Get revokedate
     *
     * @return \DateTime 
     */
    public function getRevokedate()
    {
        return $this->revokedate;
    }

    /**
     * Set contact
     *
     * @param boolean $contact
     * @return ActsAccess
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
     * Set uid
     *
     * @param \Acts\CamdramBundle\Entity\ActsUsers $uid
     * @return ActsAccess
     */
    public function setUid(\Acts\CamdramBundle\Entity\ActsUsers $uid = null)
    {
        $this->uid = $uid;
    
        return $this;
    }

    /**
     * Get uid
     *
     * @return \Acts\CamdramBundle\Entity\ActsUsers 
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * Set issuerid
     *
     * @param \Acts\CamdramBundle\Entity\ActsUsers $issuerid
     * @return ActsAccess
     */
    public function setIssuerid(\Acts\CamdramBundle\Entity\ActsUsers $issuerid = null)
    {
        $this->issuerid = $issuerid;
    
        return $this;
    }

    /**
     * Get issuerid
     *
     * @return \Acts\CamdramBundle\Entity\ActsUsers 
     */
    public function getIssuerid()
    {
        return $this->issuerid;
    }

    /**
     * Set revokeid
     *
     * @param \Acts\CamdramBundle\Entity\ActsUsers $revokeid
     * @return ActsAccess
     */
    public function setRevokeid(\Acts\CamdramBundle\Entity\ActsUsers $revokeid = null)
    {
        $this->revokeid = $revokeid;
    
        return $this;
    }

    /**
     * Get revokeid
     *
     * @return \Acts\CamdramBundle\Entity\ActsUsers 
     */
    public function getRevokeid()
    {
        return $this->revokeid;
    }
}