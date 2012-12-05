<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ActsKeywords
 *
 * @ORM\Table(name="acts_keywords")
 * @ORM\Entity
 */
class Keyword
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
     * Set pageid
     *
     * @param integer $pageid
     * @return ActsKeywords
     */
    public function setPageid($pageid)
    {
        $this->pageid = $pageid;
    
        return $this;
    }

    /**
     * Get pageid
     *
     * @return integer 
     */
    public function getPageid()
    {
        return $this->pageid;
    }

    /**
     * Set kw
     *
     * @param string $kw
     * @return ActsKeywords
     */
    public function setKw($kw)
    {
        $this->kw = $kw;
    
        return $this;
    }

    /**
     * Get kw
     *
     * @return string 
     */
    public function getKw()
    {
        return $this->kw;
    }
}