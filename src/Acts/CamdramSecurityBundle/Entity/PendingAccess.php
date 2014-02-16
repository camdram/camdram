<?php

namespace Acts\CamdramSecurityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PendingAccess
 *
 * PendingAccess acts as an Access Control Entry (ACE) in the case where the
 * user doesn't have an account on Camdram. In a typical case, a show is entered
 * on Camdram by a society or venue administrator. Pending access entries are 
 * created when a show-specific admin doesn't have an account on Camdram 
 * (determined by the given email address). The person is prompted
 * @ORM\Table(name="acts_pendingaccess")
 * @ORM\Entity(repositoryClass="PendingAccessRepository")
 */
class PendingAccess
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
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=false)
     */
    private $email;

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
     * @var \DateTime
     *
     * @ORM\Column(name="creationdate", type="date", nullable=false)
     */
    private $creation_date;


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
     * @return PendingAccess
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
     * Set email
     *
     * @param string $email
     * @return PendingAccess
     */
    public function setEmail($email)
    {
        $this->email = $email;
    
        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return PendingAccess
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
     * @return PendingAccess
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
     * @return PendingAccess
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
}
