<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ActsMailinglistsMembers
 *
 * @ORM\Table(name="acts_mailinglists_members")
 * @ORM\Entity
 */
class ActsMailinglistsMembers
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


}
