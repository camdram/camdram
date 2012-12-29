<?php
namespace Acts\CamdramBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

use Acts\CamdramBundle\Entity\MapLocation as MapLocationEntity;


/**
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