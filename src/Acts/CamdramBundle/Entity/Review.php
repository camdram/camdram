<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ActsReviews
 *
 * @ORM\Table(name="acts_reviews")
 * @ORM\Entity
 */
class Review
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
     * @ORM\Column(name="review", type="text", nullable=false)
     */
    private $review;

    /**
     * @var string
     *
     * @ORM\Column(name="from", type="text", nullable=false)
     */
    private $from;

    /**
     * @var \ActsUsers
     *
     * @ORM\ManyToOne(targetEntity="ActsUsers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="uid", referencedColumnName="id")
     * })
     */
    private $uid;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime", nullable=false)
     */
    private $created;



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
     * Set review
     *
     * @param string $review
     * @return ActsReviews
     */
    public function setReview($review)
    {
        $this->review = $review;
    
        return $this;
    }

    /**
     * Get review
     *
     * @return string 
     */
    public function getReview()
    {
        return $this->review;
    }

    /**
     * Set from
     *
     * @param string $from
     * @return ActsReviews
     */
    public function setFrom($from)
    {
        $this->from = $from;
    
        return $this;
    }

    /**
     * Get from
     *
     * @return string 
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return ActsReviews
     */
    public function setCreated($created)
    {
        $this->created = $created;
    
        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime 
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set showid
     *
     * @param \Acts\CamdramBundle\Entity\ActsShows $showid
     * @return ActsReviews
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

    /**
     * Set uid
     *
     * @param \Acts\CamdramBundle\Entity\ActsUsers $uid
     * @return ActsReviews
     */
    public function setUid(\Acts\CamdramBundle\Entity\ActsUsers $uid = null)
    {
        $this->uid = $uid;
    
        return $this;
    }

    /**
     * Get uid
     *
     * @return \Acts\CamdramBundle\Entity\ActsUsers 
     */
    public function getUid()
    {
        return $this->uid;
    }
}