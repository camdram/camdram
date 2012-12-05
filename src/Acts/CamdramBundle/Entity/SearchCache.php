<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ActsSearchCache
 *
 * @ORM\Table(name="acts_search_cache")
 * @ORM\Entity
 */
class SearchCache
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
     * Set keyword
     *
     * @param string $keyword
     * @return ActsSearchCache
     */
    public function setKeyword($keyword)
    {
        $this->keyword = $keyword;
    
        return $this;
    }

    /**
     * Get keyword
     *
     * @return string 
     */
    public function getKeyword()
    {
        return $this->keyword;
    }

    /**
     * Set text
     *
     * @param string $text
     * @return ActsSearchCache
     */
    public function setText($text)
    {
        $this->text = $text;
    
        return $this;
    }

    /**
     * Get text
     *
     * @return string 
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return ActsSearchCache
     */
    public function setType($type)
    {
        $this->type = $type;
    
        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set url
     *
     * @param string $url
     * @return ActsSearchCache
     */
    public function setUrl($url)
    {
        $this->url = $url;
    
        return $this;
    }

    /**
     * Get url
     *
     * @return string 
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set sindex
     *
     * @param integer $sindex
     * @return ActsSearchCache
     */
    public function setSindex($sindex)
    {
        $this->sindex = $sindex;
    
        return $this;
    }

    /**
     * Get sindex
     *
     * @return integer 
     */
    public function getSindex()
    {
        return $this->sindex;
    }

    /**
     * Set obsolete
     *
     * @param boolean $obsolete
     * @return ActsSearchCache
     */
    public function setObsolete($obsolete)
    {
        $this->obsolete = $obsolete;
    
        return $this;
    }

    /**
     * Get obsolete
     *
     * @return boolean 
     */
    public function getObsolete()
    {
        return $this->obsolete;
    }

    /**
     * Set linkcode
     *
     * @param string $linkcode
     * @return ActsSearchCache
     */
    public function setLinkcode($linkcode)
    {
        $this->linkcode = $linkcode;
    
        return $this;
    }

    /**
     * Get linkcode
     *
     * @return string 
     */
    public function getLinkcode()
    {
        return $this->linkcode;
    }
}