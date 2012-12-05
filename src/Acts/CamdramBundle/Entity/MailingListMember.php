<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ActsMailinglistsMembers
 *
 * @ORM\Table(name="acts_mailinglists_members")
 * @ORM\Entity
 */
class MailingListMember
{
    /**
     * @var integer
     *
     * @ORM\Column(name="listid", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $listid;

    /**
     * @var integer
     *
     * @ORM\Column(name="uid", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $uid;



    /**
     * Set listid
     *
     * @param integer $listid
     * @return ActsMailinglistsMembers
     */
    public function setListid($listid)
    {
        $this->listid = $listid;
    
        return $this;
    }

    /**
     * Get listid
     *
     * @return integer 
     */
    public function getListid()
    {
        return $this->listid;
    }

    /**
     * Set uid
     *
     * @param integer $uid
     * @return ActsMailinglistsMembers
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
}