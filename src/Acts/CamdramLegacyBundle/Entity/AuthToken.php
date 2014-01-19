<?php

namespace Acts\CamdramLegacyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AuthToken
 *
 * @ORM\Table(name="acts_authtokens")
 * @ORM\Entity
 *
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
    private $site_id;

    /**
     * @var integer
     *
     * @ORM\Column(name="userid", type="integer", nullable=false)
     */
    private $user_id;

    /**
     * @var \Acts\CamdramBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="\Acts\CamdramBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="userid", referencedColumnName="id")
     * })
     */
    private $user;

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
     * @return AuthToken
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
     * Set site_id
     *
     * @param integer $siteId
     * @return AuthToken
     */
    public function setSiteId($siteId)
    {
        $this->site_id = $siteId;
    
        return $this;
    }

    /**
     * Get site_id
     *
     * @return integer 
     */
    public function getSiteId()
    {
        return $this->site_id;
    }

    /**
     * Set user_id
     *
     * @param integer $userId
     * @return AuthToken
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
     * Set issued
     *
     * @param \DateTime $issued
     * @return AuthToken
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
     * Set user
     *
     * @param \Acts\CamdramBundle\Entity\User $user
     * @return AuthToken
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
}