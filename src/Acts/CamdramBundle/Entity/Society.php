<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Exclude;

/**
 * Society
 *
 * @ORM\Table(name="acts_societies_new")
 * @ORM\Entity(repositoryClass="Acts\CamdramBundle\Entity\SocietyRepository")
 */
class Society extends Organisation
{

    /**
     * @ORM\OneToMany(targetEntity="Show", mappedBy="society")
     * @Exclude
     */
    private $shows;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->shows = new \Doctrine\Common\Collections\ArrayCollection();
        $this->setType(false);
    }

    /**
     * Add shows
     *
     * @param \Acts\CamdramBundle\Entity\Show $shows
     * @return Society
     */
    public function addShow(\Acts\CamdramBundle\Entity\Show $shows)
    {
        $this->shows[] = $shows;

        return $this;
    }

    /**
     * Remove shows
     *
     * @param \Acts\CamdramBundle\Entity\Show $shows
     */
    public function removeShow(\Acts\CamdramBundle\Entity\Show $shows)
    {
        $this->shows->removeElement($shows);
    }

    /**
     * Get shows
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getShows()
    {
        return $this->shows;
    }

}