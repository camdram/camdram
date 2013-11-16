<?php
namespace Acts\CamdramBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

use Acts\CamdramBundle\Entity\MapLocation as MapLocationEntity;


/**
 * A map distance constraint, which can be used to validate a MapLocation type against it's proximity to a certain
 * location
 *
 * @Annotation
 */
class TechieAdvertExpiry extends Constraint
{
    public $too_early_message = "The expiry date must be before today's date";
    public $too_late_message = 'The expiry date cannot be more than %days% days in the future';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }

    public function validatedBy()
    {
        return 'camdram.techie_advert_expiry';
    }

}