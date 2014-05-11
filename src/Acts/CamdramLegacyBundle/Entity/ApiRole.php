<?php

namespace Acts\CamdramLegacyBundle\Entity;

use JMS\Serializer\Annotation\XmlRoot;
use JMS\Serializer\Annotation\XmlElement;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\VirtualProperty;

/**
 * ApiRole - used as wrapper to the output of Show->getRoleOfType() to to make XML work nicely
 *
 * @XmlRoot("item")
 */
class ApiRole
{
    /**
     * @Exclude
     */
    private $role;

    /**
     * @Exclude
     */
    private $router;

    /**
     * @Exclude
     */
    private $show;

    public function __construct($role, $show, $router)
    {
    $this->role = $role;
    $this->show = $show;
    $this->router  = $router;
    }

    /**
     * @VirtualProperty
     * @XmlElement(cdata=false)
     */

    public function getName()
    {
        return $this->role->getPerson()->getName();
    }

    /**
     * @VirtualProperty
     * @XmlElement(cdata=false)
     */

    public function getRole()
    {
        return $this->role->getRole();
    }

    /**
     * @VirtualProperty
     * @XmlElement(cdata=false)
     */

    public function getUrl()
    {
    return $this->router->generate('get_person', array('identifier' => $this->role->getPerson()->getSlug(), 'fromShow' => $this->show->getSlug()), true);
    }

}
