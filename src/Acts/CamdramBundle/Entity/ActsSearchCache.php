<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ActsSearchCache
 *
 * @ORM\Table(name="acts_search_cache")
 * @ORM\Entity
 */
class ActsSearchCache
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
     * @ORM\Column(name="keyword", type="string", length=200, nullable=false)
     */
    private $keyword;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="text", nullable=false)
     */
    private $text;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", nullable=false)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="text", nullable=false)
     */
    private $url;

    /**
     * @var integer
     *
     * @ORM\Column(name="sindex", type="integer", nullable=false)
     */
    private $sindex;

    /**
     * @var boolean
     *
     * @ORM\Column(name="obsolete", type="boolean", nullable=false)
     */
    private $obsolete;

    /**
     * @var string
     *
     * @ORM\Column(name="linkcode", type="string", length=2000, nullable=true)
     */
    private $linkcode;


}
