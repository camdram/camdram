<?php

namespace Acts\CamdramBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Map Location
 */
class MapLocation
{
    /**
     * @var float
     * @Assert\Range(min=-90, max=90,
     *      invalidMessage="The latitude must be a valid number",
     *      minMessage="The latitude must be greater than -90",
     *      maxMessage="The latitude must be less than 90"
     * )
     */
    private $latitude;

    /**
     * @var float
     * @Assert\Range(min=-180, max=180,
     *      invalidMessage="The longitude must be a valid number",
     *      minMessage="The longitude must be greater than -180",
     *      maxMessage="The longitude must be less than 180"
     * )
     */
    private $longitude;

    /**
     * @return float
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @param float $latitude
     */
    public function setLatitude($latitude): void
    {
        $this->latitude = $latitude;
    }

    /**
     * @return float
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * @param float $longitude
     */
    public function setLongitude($longitude): void
    {
        $this->longitude = $longitude;
    }

    /**
     * @param float $latitude
     * @param float $longitude
     */
    public function __construct($latitude = null, $longitude = null)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }

    public function __toString()
    {
        return $this->latitude.', '.$this->longitude;
    }

    /** @return float */
    public function getDistanceTo(MapLocation $location)
    {
        $earthRadius = 3958.75;

        $dLat = deg2rad($location->getLatitude() - $this->getLatitude());
        $dLng = deg2rad($location->getLongitude() - $this->getLongitude());

        $a = sin($dLat / 2) * sin($dLat / 2)
                + cos(deg2rad($this->getLatitude())) * cos(deg2rad($location->getLatitude()))
                * sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $dist = $earthRadius * $c;

        // from miles
        $meterConversion = 1609;
        $geopointDistance = $dist * $meterConversion;

        return $geopointDistance;
    }
}
