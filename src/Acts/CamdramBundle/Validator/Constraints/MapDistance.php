<?php

namespace Acts\CamdramBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * A map distance constraint, which can be used to validate a MapLocation type against it's proximity to a certain
 * location
 *
 * @Annotation
 */
class MapDistance extends Constraint
{
    public $message = 'The location is outside the permitted geographical area';

    public $nearTo;
    public $radius;

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
