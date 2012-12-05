<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ActsPendingaccess
 *
 * @ORM\Table(name="acts_pendingaccess")
 * @ORM\Entity
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
     * @ORM\Column(name="email", type="text", nullable=false)
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
    private $issuerid;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="creationdate", type="date", nullable=false)
     */
    private $creationdate;



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
     * @return ActsPendingaccess
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
     * @return ActsPendingaccess
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
     * @return ActsPendingaccess
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
     * Set issuerid
     *
     * @param integer $issuerid
     * @return ActsPendingaccess
     */
    public function setIssuerid($issuerid)
    {
        $this->issuerid = $issuerid;
    
        return $this;
    }

    /**
     * Get issuerid
     *
     * @return integer 
     */
    public function getIssuerid()
    {
        return $this->issuerid;
    }

    /**
     * Set creationdate
     *
     * @param \DateTime $creationdate
     * @return ActsPendingaccess
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
}