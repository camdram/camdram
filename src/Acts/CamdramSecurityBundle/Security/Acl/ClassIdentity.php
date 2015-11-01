<?php

namespace Acts\CamdramSecurityBundle\Security\Acl;

class ClassIdentity
{
    private $class_name;

    public function __construct($class_name)
    {
        if (!class_exists($class_name)) {
            throw new \InvalidArgumentException(sprintf('%s is an invalid class name', $class_name));
        }
        $this->class_name = $class_name;
    }

    public function getClassName()
    {
        return $this->class_name;
    }
}
