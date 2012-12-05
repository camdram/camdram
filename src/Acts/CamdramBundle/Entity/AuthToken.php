<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ActsAuthtokens
 *
 * @ORM\Table(name="acts_authtokens")
 * @ORM\Entity
 */
class AuthToken
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
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=50, nullable=false)
     */
    private $token;

    /**
     * @var integer
     *
     * @ORM\Column(name="siteid", type="integer", nullable=false)
     */
    private $siteid;

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
     * @var \DateTime
     *
     * @ORM\Column(name="issued", type="datetime", nullable=false)
     */
    private $issued;



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
     * Set token
     *
     * @param string $token
     * @return ActsAuthtokens
     */
    public function setToken($token)
    {
        $this->token = $token;
    
        return $this;
    }

    /**
     * Get token
     *
     * @return string 
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set siteid
     *
     * @param integer $siteid
     * @return ActsAuthtokens
     */
    public function setSiteid($siteid)
    {
        $this->siteid = $siteid;
    
        return $this;
    }

    /**
     * Get siteid
     *
     * @return integer 
     */
    public function getSiteid()
    {
        return $this->siteid;
    }

    /**
     * Set issued
     *
     * @param \DateTime $issued
     * @return ActsAuthtokens
     */
    public function setIssued($issued)
    {
        $this->issued = $issued;
    
        return $this;
    }

    /**
     * Get issued
     *
     * @return \DateTime 
     */
    public function getIssued()
    {
        return $this->issued;
    }

    /**
     * Set userid
     *
     * @param \Acts\CamdramBundle\Entity\ActsUsers $userid
     * @return ActsAuthtokens
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