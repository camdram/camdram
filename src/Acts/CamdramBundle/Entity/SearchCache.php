<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SearchCache
 *
 * @ORM\Table(name="acts_search_cache", indexes={@ORM\Index(name="keyword", columns={"keyword"})})
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
    private $s_index;

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
    private $link_code;


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
     * @return SearchCache
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
     * @return SearchCache
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
     * @return SearchCache
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
     * @return SearchCache
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
     * Set s_index
     *
     * @param integer $sIndex
     * @return SearchCache
     */
    public function setSIndex($sIndex)
    {
        $this->s_index = $sIndex;
    
        return $this;
    }

    /**
     * Get s_index
     *
     * @return integer 
     */
    public function getSIndex()
    {
        return $this->s_index;
    }

    /**
     * Set obsolete
     *
     * @param boolean $obsolete
     * @return SearchCache
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
     * Set link_code
     *
     * @param string $linkCode
     * @return SearchCache
     */
    public function setLinkCode($linkCode)
    {
        $this->link_code = $linkCode;
    
        return $this;
    }

    /**
     * Get link_code
     *
     * @return string 
     */
    public function getLinkCode()
    {
        return $this->link_code;
    }
}