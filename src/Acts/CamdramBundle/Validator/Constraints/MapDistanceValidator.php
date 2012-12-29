<?php
namespace Acts\CamdramBundle\Validator\Constraints;

use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;

use Acts\CamdramBundle\Entity\MapLocation;

/**
 * @Annotation
 */
class MapDistanceValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        /** @var $value \Acts\CamdramBundle\Entity\MapLocation */

        if (!($value instanceof MapLocation)) return;

        if (!is_numeric($value->getLatitude()) || !is_numeric($value->getLongitude())) return;

        $loc = new MapLocation($constraint->nearTo[0], $constraint->nearTo[1]);

        if ($value->getDistanceTo($loc) > $constraint->radius) {
            $this->context->addViolation($constraint->message, array(), $value);
        }

    }

}