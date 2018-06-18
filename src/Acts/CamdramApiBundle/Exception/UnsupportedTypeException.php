<?php

namespace Acts\CamdramApiBundle\Exception;

class UnsupportedTypeException extends \Exception
{
    public function __construct($type, $class)
    {
        parent::__construct(sprintf('The class %s does not support the type %s', $class, $type));
    }
}
