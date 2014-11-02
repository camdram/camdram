<?php
namespace Acts\CamdramBundle\Validator\Constraints;

use Acts\CamdramBundle\Entity\TechieAdvert;
use Acts\TimeMockBundle\Service\TimeService;
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
    private $timeService;
    private $expiry_max_days;

    public function __construct(TimeService $timeService, $expiry_max_days)
    {
        $this->timeService = $timeService;
        $this->expiry_max_days = $expiry_max_days;
    }

    public function validate($value, Constraint $constraint)
    {
        /** @var $value \Acts\CamdramBundle\Entity\TechieAdvert */

        if (!($value instanceof TechieAdvert)) return;

        if ($value->getDeadline()) {
            $max_expires = $this->timeService->getCurrentTime();
            $max_expires->modify('+'.$this->expiry_max_days.' days');

            $expiry_date = new \DateTime($value->getExpiry()->format('Y-m-d').' '.$value->getDeadlineTime()->format('H:i:s'));

            if ($expiry_date < $this->timeService->getCurrentTime()) {
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
