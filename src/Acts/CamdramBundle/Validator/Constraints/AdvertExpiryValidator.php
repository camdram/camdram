<?php

namespace Acts\CamdramBundle\Validator\Constraints;

use Acts\CamdramBundle\Entity\Advert;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;

/**
 *
 * @Annotation
 */
class AdvertExpiryValidator extends ConstraintValidator
{
    /** @var int */
    const EXPIRES_AT_MAX_DAYS = 40;

    /**
     * @param AdvertExpiry $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        /** @var $value Advert */
        if (!($value instanceof Advert)) {
            return;
        }

        if ($value->getExpiresAt()) {
            $now = new \Datetime;
            $max_expires = new \Datetime('+'.self::EXPIRES_AT_MAX_DAYS.' days');

            $expiry_date = $value->getExpiresAt();

            if ($expiry_date  > $max_expires) {
                $this->context->addViolation($constraint->too_late_message, ['%days%' => self::EXPIRES_AT_MAX_DAYS]);
            }
        }
    }
}
