<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Gedmo\Mapping\Annotation as Gedmo;
use Acts\CamdramApiBundle\Configuration\Annotation as Api;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Society
 *
 * @ORM\Entity(repositoryClass="VenueRepository")
 * @Serializer\ExclusionPolicy("all")
 * @Serializer\XmlRoot("venue")
 */
class Venue extends Organisation
{
    /**
     * @var string
     *
     * @ORM\Column(name="address", type="text", nullable=true)
     * @Serializer\Expose
     * @Gedmo\Versioned
     */
    private $address;

    /**
     * @var float
     *
     * @ORM\Column(name="latitude", type="float", nullable=true)
     * @Serializer\Expose
     * @Gedmo\Versioned
     */
    private $latitude;

    /**
     * @var float
     *
     * @ORM\Column(name="longitude", type="float", nullable=true)
     * @Serializer\Expose
     * @Gedmo\Versioned
     */
    private $longitude;

    /**
     * @ORM\OneToMany(targetEntity="Show", mappedBy="venue")
     * @Api\Link(route="get_venue_shows", params={"identifier": "object.getSlug()"})
     */
    private $shows;

    /**
     * @ORM\OneToMany(targetEntity="Performance", mappedBy="venue")
     */
    private $performances;

    /**
     * Set latitude
     *
     * @param float $latitude
     * @return Venue
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * Get latitude
     *
     * @return float
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set longitude
     *
     * @param float $longitude
     * @return Venue
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * Get longitude
     *
     * @return float
     */
    public function getLongitude()
    {
        return $this->longitude;
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
     * @return Venue
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

    /**
     * Set address
     *
     * @param string $address
     * @return Venue
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param MapLocation|null $location
     */
    public function setLocation($location)
    {
        if ($location instanceof MapLocation) {
            $this->latitude = $location->getLatitude();
            $this->longitude = $location->getLongitude();
        }
    }

    /**
     * @return MapLocation|null
     * @Assert\Valid()
     */
    public function getLocation()
    {
        return new MapLocation($this->latitude, $this->longitude);
    }

    public function getIndexDate()
    {
        return null;
    }

    /**
     * Add performances
     *
     * @param \Acts\CamdramBundle\Entity\Performance $performances
     * @return Venue
     */
    public function addPerformance(\Acts\CamdramBundle\Entity\Performance $performances)
    {
        $this->performances[] = $performances;

        return $this;
    }

    /**
     * Remove performances
     *
     * @param \Acts\CamdramBundle\Entity\Performance $performances
     */
    public function removePerformance(\Acts\CamdramBundle\Entity\Performance $performances)
    {
        $this->performances->removeElement($performances);
    }

    /**
     * Get performances
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPerformances()
    {
        return $this->performances;
    }

    public function getOrganisationType()
    {
        return 'venue';
    }
}
