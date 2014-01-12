<?php

namespace Acts\CamdramLegacyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EmailSig
 *
 * @ORM\Table(name="acts_email_sigs")
 * @ORM\Entity
 */
class EmailSig
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
     * @ORM\Column(name="uid", type="integer", nullable=false)
     */
    private $user_id;

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
     * @ORM\Column(name="sig", type="text", nullable=false)
     */
    private $sig;

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
     * Set user_id
     *
     * @param integer $userId
     * @return EmailSig
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
     * Set sig
     *
     * @param string $sig
     * @return EmailSig
     */
    public function setSig($sig)
    {
        $this->sig = $sig;
    
        return $this;
    }

    /**
     * Get sig
     *
     * @return string 
     */
    public function getSig()
    {
        return $this->sig;
    }

    /**
     * Set user
     *
     * @param \Acts\CamdramBundle\Entity\User $user
     * @return EmailSig
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