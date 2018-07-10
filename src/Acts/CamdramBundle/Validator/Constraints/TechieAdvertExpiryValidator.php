<?php

namespace Acts\CamdramBundle\Validator\Constraints;

use Acts\CamdramBundle\Entity\TechieAdvert;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;
use Acts\CamdramBundle\Entity\MapLocation;

/**
 * A map distance validator, which evaluates a MapLocation's distance from a given centre point.
 *
 * @Annotation
 */
class TechieAdvertExpiryValidator extends ConstraintValidator
{
    private $expiry_max_days;

    public function __construct($expiry_max_days)
    {
        $this->expiry_max_days = $expiry_max_days;
    }

    public function validate($value, Constraint $constraint)
    {
        /** @var $value \Acts\CamdramBundle\Entity\TechieAdvert */
        if (!($value instanceof TechieAdvert)) {
            return;
        }

        if ($value->getDeadline()) {
            $now = new \Datetime;
            $max_expires = new \Datetime('+'.$this->expiry_max_days.' days');

            $expiry_date = new \DateTime($value->getExpiry()->format('Y-m-d').' '.$value->getDeadlineTime()->format('H:i:s'));
            
            if ($expiry_date < $now) {
                $this->context->addViolation($constraint->too_early_message, array(), $value);
            } elseif ($expiry_date  > $max_expires) {
                $this->context->addViolation($constraint->too_late_message, array('%days%' => $this->expiry_max_days), $value);
            }

            if (!$value->getDeadlineTime()) {
                $this->context->addViolation($constraint->blank_time_message, array(), $value);
            }
        }
    }
}
