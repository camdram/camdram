<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Footprints
 *
 * @ORM\Table(name="footprints")
 * @ORM\Entity
 */
class Footprint
{
    /**
     * @var integer
     *
     * @ORM\Column(name="from", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $from;

    /**
     * @var integer
     *
     * @ORM\Column(name="to", type="integer", nullable=false)
     */
    private $to;

    /**
     * @var integer
     *
     * @ORM\Column(name="time", type="integer", nullable=false)
     */
    private $time;



    /**
     * Get from
     *
     * @return integer 
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * Set to
     *
     * @param integer $to
     * @return Footprints
     */
    public function setTo($to)
    {
        $this->to = $to;
    
        return $this;
    }

    /**
     * Get to
     *
     * @return integer 
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * Set time
     *
     * @param integer $time
     * @return Footprints
     */
    public function setTime($time)
    {
        $this->time = $time;
    
        return $this;
    }

    /**
     * Get time
     *
     * @return integer 
     */
    public function getTime()
    {
        return $this->time;
    }
}