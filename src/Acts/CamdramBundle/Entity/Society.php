<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * Society
 *
 * @ORM\Entity(repositoryClass="Acts\CamdramBundle\Entity\SocietyRepository")
 * @Serializer\ExclusionPolicy("all")
 */
class Society extends Organisation
{
    /**
     * @ORM\OneToMany(targetEntity="Show", mappedBy="society")
     */
    private $shows;

    /**
     * @Serializer\Expose
     */
    protected $entity_type = 'society';

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->shows = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add shows
     *
     * @param \Acts\CamdramBundle\Entity\Show $shows
     *
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

    public function getEntityType()
    {
        return $this->entity_type;
    }

    public function getIndexDate()
    {
        return null;
    }
}
