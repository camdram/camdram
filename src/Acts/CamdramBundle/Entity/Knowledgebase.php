<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ActsKnowledgebase
 *
 * @ORM\Table(name="acts_knowledgebase")
 * @ORM\Entity
 */
class Knowledgebase
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
     * @ORM\Column(name="text", type="text", nullable=false)
     */
    private $text;

    /**
     * @var \ActsUsers
     *
     * @ORM\ManyToOne(targetEntity="ActsUsers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="userid", referencedColumnName="id")
     * })
     */
    private $userid;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime", nullable=false)
     */
    private $date;



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
     * @return ActsKnowledgebase
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
     * Set text
     *
     * @param string $text
     * @return ActsKnowledgebase
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
     * Set date
     *
     * @param \DateTime $date
     * @return ActsKnowledgebase
     */
    public function setDate($date)
    {
        $this->date = $date;
    
        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set userid
     *
     * @param \Acts\CamdramBundle\Entity\ActsUsers $userid
     * @return ActsKnowledgebase
     */
    public function setUserid(\Acts\CamdramBundle\Entity\ActsUsers $userid = null)
    {
        $this->userid = $userid;
    
        return $this;
    }

    /**
     * Get userid
     *
     * @return \Acts\CamdramBundle\Entity\ActsUsers 
     */
    public function getUserid()
    {
        return $this->userid;
    }
}