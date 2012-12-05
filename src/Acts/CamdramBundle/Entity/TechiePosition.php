<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ActsTechiesPositions
 *
 * @ORM\Table(name="acts_techies_positions")
 * @ORM\Entity
 */
class TechiePosition
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
     * @ORM\Column(name="position", type="text", nullable=false)
     */
    private $position;

    /**
     * @var float
     *
     * @ORM\Column(name="orderid", type="float", nullable=false)
     */
    private $orderid;



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
     * Set position
     *
     * @param string $position
     * @return ActsTechiesPositions
     */
    public function setPosition($position)
    {
        $this->position = $position;
    
        return $this;
    }

    /**
     * Get position
     *
     * @return string 
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set orderid
     *
     * @param float $orderid
     * @return ActsTechiesPositions
     */
    public function setOrderid($orderid)
    {
        $this->orderid = $orderid;
    
        return $this;
    }

    /**
     * Get orderid
     *
     * @return float 
     */
    public function getOrderid()
    {
        return $this->orderid;
    }
}