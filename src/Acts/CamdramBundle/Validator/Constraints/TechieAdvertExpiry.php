<?php

namespace Acts\CamdramBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class TechieAdvertExpiry extends Constraint
{
    /** @var string */
    public $too_early_message = "The expiry date must be after today's date";
    /** @var string */
    public $too_late_message = 'The expiry date cannot be more than %days% days in the future';
    /** @var string */
    public $blank_time_message = 'Please enter an expiry time';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }

    public function validatedBy()
    {
        return 'camdram.techie_advert_expiry';
    }
}
