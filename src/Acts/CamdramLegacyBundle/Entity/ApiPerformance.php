<?php

namespace Acts\CamdramLegacyBundle\Entity;

use JMS\Serializer\Annotation\XmlRoot;
use JMS\Serializer\Annotation\XmlElement;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\VirtualProperty;

/**
 * ApiPerformance - used as wrapper to the output of Show->getAllPerformances() to to make XML work nicely
 *
 * @XmlRoot("item")
 */
class ApiPerformance
{
    /**
     * @Exclude
     */
    private $performance;

    public function __construct($performance, $router)
    {
        $this->performance = $performance;
        $this->router  = $router;
    }

    /**
     * @VirtualProperty
     * @XmlElement(cdata=false)
     */
    public function getVenue()
    {
        return $this->performance['venue'];
    }

    /**
     * @VirtualProperty
     * @XmlElement(cdata=false)
     * @Type("DateTime<'r'>")
     */
    public function getDate()
    {
        return $this->performance['datetime'];
    }

    /**
     * @VirtualProperty
     * @XmlElement(cdata=false)
     */
    public function getDateIso8601()
    {
        return $this->performance['datetime'];
    }
}
