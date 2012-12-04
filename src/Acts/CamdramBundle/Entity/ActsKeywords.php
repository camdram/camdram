<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ActsKeywords
 *
 * @ORM\Table(name="acts_keywords")
 * @ORM\Entity
 */
class ActsKeywords
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
     * @ORM\Column(name="pageid", type="integer", nullable=false)
     */
    private $pageid;

    /**
     * @var string
     *
     * @ORM\Column(name="kw", type="text", nullable=false)
     */
    private $kw;


}
