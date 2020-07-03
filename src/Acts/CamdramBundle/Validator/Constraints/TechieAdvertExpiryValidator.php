<?php

namespace Acts\CamdramBundle\Validator\Constraints;

use Acts\CamdramBundle\Entity\TechieAdvert;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;

/**
 *
 * @Annotation
 */
class TechieAdvertExpiryValidator extends ConstraintValidator
{
    /** @var int */
    private $expiry_max_days;

    /** @param int $expiry_max_days */
    public function __construct($expiry_max_days)
    {
        $this->expiry_max_days = $expiry_max_days;
    }

    /**
     * @param TechieAdvertExpiry $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        /** @var $value \Acts\CamdramBundle\Entity\TechieAdvert */
        if (!($value instanceof TechieAdvert)) {
            return;
        }

        if ($value->getDeadline()) {
            $now = new \Datetime;
            $max_expires = new \Datetime('+'.$this->expiry_max_days.' days');

            $expiry_date = $value->getExpiry();

            if ($expiry_date < $now) {
                $this->context->addViolation($constraint->too_early_message, []);
            } elseif ($expiry_date  > $max_expires) {
                $this->context->addViolation($constraint->too_late_message, ['%days%' => $this->expiry_max_days]);
            }
        }
    }
}
