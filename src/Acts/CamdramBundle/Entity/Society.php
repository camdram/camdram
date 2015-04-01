<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Acts\CamdramApiBundle\Configuration\Annotation as Api;

/**
 * Society
 *
 * @ORM\Entity(repositoryClass="Acts\CamdramBundle\Entity\SocietyRepository")
 * @Serializer\ExclusionPolicy("all")
 * @Serializer\XmlRoot("society")
 */
class Society extends Organisation
{

    /**
     * @ORM\OneToMany(targetEntity="Show", mappedBy="society")
     * @Api\Link(route="get_society_shows", params={"identifier": "object.getSlug()"})
     */
    private $shows;

    private $entity_type = 'society';

    public function getEntityType()
    {
        return $this->entity_type;
    }

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

    public function getIndexDate()
    {
        return null;
    }

    public function getOrganisationType()
    {
        return 'society';
    }

}
