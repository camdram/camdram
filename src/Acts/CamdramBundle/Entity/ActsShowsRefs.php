<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ActsShowsRefs
 *
 * @ORM\Table(name="acts_shows_refs")
 * @ORM\Entity
 */
class ActsShowsRefs
{
    /**
     * @var integer
     *
     * @ORM\Column(name="refid", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $refid;

    /**
     * @var integer
     *
     * @ORM\Column(name="showid", type="integer", nullable=false)
     */
    private $showid;

    /**
     * @var string
     *
     * @ORM\Column(name="ref", type="text", nullable=false)
     */
    private $ref;


}
