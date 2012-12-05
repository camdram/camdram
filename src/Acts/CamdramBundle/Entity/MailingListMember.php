<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MailingListMember
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
    private $list_id;

    /**
     * @var integer
     *
     * @ORM\Column(name="uid", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $user_id;


    /**
     * Set list_id
     *
     * @param integer $listId
     * @return MailingListMember
     */
    public function setListId($listId)
    {
        $this->list_id = $listId;
    
        return $this;
    }

    /**
     * Get list_id
     *
     * @return integer 
     */
    public function getListId()
    {
        return $this->list_id;
    }

    /**
     * Set user_id
     *
     * @param integer $userId
     * @return MailingListMember
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
}