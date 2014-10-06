<?php

namespace Acts\CamdramLegacyBundle\Entity;

use JMS\Serializer\Annotation\XmlRoot;
use JMS\Serializer\Annotation\XmlElement;
use JMS\Serializer\Annotation\XmlList;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\VirtualProperty;

/**
 * ApiShow - used as wrapper to Show to make XML work nicely
 *
 * @XmlRoot("show")
 */
class ApiShow
{
    /**
     * @Exclude
     */
    private $show;

    /**
     * @Exclude
     */
    private $router;

    public function __construct($show, $router)
    {
        $this->show = $show;
        $this->router  = $router;
    }

    /**
     * @VirtualProperty
     * @XmlElement(cdata=false)
     */

    public function getTitle()
    {
        return $this->show->getName();
    }

    /**
     * @VirtualProperty
     * @XmlElement(cdata=false)
     */

    public function getAuthor()
    {
        return $this->show->getAuthor();
    }

    /**
     * @VirtualProperty
     * @XmlElement(cdata=false)
     */

    public function getVenue()
    {
        return $this->show->getVenueName();
    }

    /**
     * @VirtualProperty
     * @XmlElement(cdata=false)
     */

    public function getId()
    {
        return $this->show->getId();
    }

    /**
     * @VirtualProperty
     * @XmlElement(cdata=false)
     */

    public function getOnlinebookingurl()
    {
        return $this->show->getOnlineBookingUrl();
    }

    /**
     * @VirtualProperty
     * @XmlElement(cdata=false)
     */

    public function getCamdramshowurl()
    {
        return $this->router->generate('get_show', array('identifier' =>  $this->show->getSlug()), true);
    }


    /**
     * @VirtualProperty
     * @XmlElement(cdata=false)
     */

    public function getFacebookurl()
    {
        return $this->show->getFacebookUrl();
    }

    /**
     * @VirtualProperty
     * @XmlElement(cdata=false)
     */

    public function getOtherurl()
    {
        return $this->show->getOtherUrl();
    }

    /**
     * @VirtualProperty
     * @XmlElement(cdata=false)
     * @XmlList(entry="item")
     */

    public function getPerformances()
    {
        $callback = function ($value) {
            return new ApiPerformance($value);
        };
        return array_map($callback, $this->show->getAllPerformances());
    }

    /**
     * @VirtualProperty
     * @XmlElement(cdata=false)
     * @XmlList(entry="item")
     */

    public function getCast()
    {
        return $this->wrapRoles('cast');
    }

    /**
     * @VirtualProperty
     * @XmlElement(cdata=false)
     * @XmlList(entry="item")
     */

    public function getOrchestra()
    {
        return $this->wrapRoles('band');
    }

    /**
     * @VirtualProperty
     * @XmlElement(cdata=false)
     * @XmlList(entry="item")
     */

    public function getProd()
    {
        return $this->wrapRoles('prod');
    }


    private function wrapRoles($type)
    {
        $callback = function ($value) {
            return new ApiRole($value, $this->show, $this->router);
        };
        return array_map($callback, $this->show->getRolesByType($type)->toArray(false));
    }

}
