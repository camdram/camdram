<?php

namespace Acts\CamdramLegacyBundle\Entity;

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
     * @var int
     *
     * @ORM\Column(name="listid", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $list_id;

    /**
     * @var int
     *
     * @ORM\Column(name="uid", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $user_id;

    /**
     * Set list_id
     *
     * @param int $listId
     *
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
     * @return int
     */
    public function getListId()
    {
        return $this->list_id;
    }

    /**
     * Set user_id
     *
     * @param int $userId
     *
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
     * @return int
     */
    public function getUserId()
    {
        return $this->user_id;
    }
}
