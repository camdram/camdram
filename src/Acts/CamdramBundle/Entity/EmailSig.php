<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ActsEmailSigs
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
     * Set sig
     *
     * @param string $sig
     * @return ActsEmailSigs
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
     * Set uid
     *
     * @param \Acts\CamdramBundle\Entity\ActsUsers $uid
     * @return ActsEmailSigs
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
}