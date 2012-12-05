<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ActsShowsRefs
 *
 * @ORM\Table(name="acts_shows_refs")
 * @ORM\Entity
 */
class ShowRef
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
     * @var \ActsShows
     *
     * @ORM\ManyToOne(targetEntity="ActsShows")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="showid", referencedColumnName="id")
     * })
     */
    private $showid;

    /**
     * @var string
     *
     * @ORM\Column(name="ref", type="text", nullable=false)
     */
    private $ref;



    /**
     * Get refid
     *
     * @return integer 
     */
    public function getRefid()
    {
        return $this->refid;
    }

    /**
     * Set ref
     *
     * @param string $ref
     * @return ActsShowsRefs
     */
    public function setRef($ref)
    {
        $this->ref = $ref;
    
        return $this;
    }

    /**
     * Get ref
     *
     * @return string 
     */
    public function getRef()
    {
        return $this->ref;
    }

    /**
     * Set showid
     *
     * @param \Acts\CamdramBundle\Entity\ActsShows $showid
     * @return ActsShowsRefs
     */
    public function setShowid(\Acts\CamdramBundle\Entity\ActsShows $showid = null)
    {
        $this->showid = $showid;
    
        return $this;
    }

    /**
     * Get showid
     *
     * @return \Acts\CamdramBundle\Entity\ActsShows 
     */
    public function getShowid()
    {
        return $this->showid;
    }
}