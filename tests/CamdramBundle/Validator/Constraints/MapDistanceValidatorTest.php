<?php

namespace Camdram\Tests\CamdramBundle\Validator\Constraints;

use Acts\CamdramBundle\Entity\MapLocation;
use Acts\CamdramBundle\Validator\Constraints\MapDistance;
use Acts\CamdramBundle\Validator\Constraints\MapDistanceValidator;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;
use PHPUnit\Framework\MockObject\MockObject;

class MapDistanceValidatorTest extends ConstraintValidatorTestCase
{
    /**
     * @var \Acts\CamdramBundle\Validator\Constraints\MapDistance
     */
    private $mapDistanceConstraint;

    protected function createValidator()
    {
        return new MapDistanceValidator();
    }

    public function setUp()
    {
        $this->mapDistanceConstraint = new MapDistance();
        $this->mapDistanceConstraint->nearTo = array(52.1, 0.5);
        $this->mapDistanceConstraint->radius = 100; // kilometres
        $this->mapDistanceConstraint->message = 'myMessage';

        parent::setup();
    }

    public function testValidate_Invalid()
    {
        $value = new MapLocation(50.0, 0.0);

        $this->validator->validate($value, $this->mapDistanceConstraint);

        $this->buildViolation('myMessage')
            ->setInvalidValue($value)
            ->assertRaised();
    }

    public function testValidate_Valid()
    {
        $value = new MapLocation(52.1005, 0.5005);

        $this->validator->validate($value, $this->mapDistanceConstraint);
        $this->assertNoViolation();
    }

    public function testValidate_NotLocation()
    {
        $value = null;


        $this->validator->validate($value, $this->mapDistanceConstraint);
        $this->assertNoViolation();
    }
}
