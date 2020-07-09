<?php

namespace Acts\CamdramBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class AdvertExpiry extends Constraint
{
    public $too_late_message = 'The expiry date cannot be more than %days% days in the future';
 
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }

    public function validatedBy()
    {
        return \get_class($this).'Validator';
    }
}
