<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ActsReviews
 *
 * @ORM\Table(name="acts_reviews")
 * @ORM\Entity
 */
class ActsReviews
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
     * @ORM\Column(name="showid", type="integer", nullable=false)
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
     * @var integer
     *
     * @ORM\Column(name="uid", type="integer", nullable=false)
     */
    private $uid;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime", nullable=false)
     */
    private $created;


}
