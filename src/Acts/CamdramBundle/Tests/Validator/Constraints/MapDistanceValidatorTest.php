<?php

namespace Acts\CamdramBundle\Tests\Validator\Constraints;

use Acts\CamdramBundle\Entity\MapLocation;
use Acts\CamdramBundle\Validator\Constraints\MapDistance;
use Acts\CamdramBundle\Validator\Constraints\MapDistanceValidator;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class MapDistanceValidatorTest extends TestCase
{
    /**
     * @var MockObject
     */
    private $context;

    /**
     * @var \Acts\CamdramBundle\Validator\Constraints\MapDistanceValidator
     */
    private $validator;

    /**
     * @var \Acts\CamdramBundle\Validator\Constraints\MapDistance
     */
    private $constraint;

    public function setUp()
    {
        $this->context = $this->createMock('Symfony\Component\Validator\ExecutionContext', array(), array(), '', false);
        $this->validator = new MapDistanceValidator();
        $this->validator->initialize($this->context);

        $this->constraint = new MapDistance();
        $this->constraint->nearTo = array(52.1, 0.5);
        $this->constraint->radius = 100; // kilometres
        $this->constraint->message = 'myMessage';
    }

    public function testValidate_Invalid()
    {
        $value = new MapLocation(50.0, 0.0);

        $this->context->expects($this->once())
            ->method('addViolation')
            ->with('myMessage', array(), $value);

        $this->validator->validate($value, $this->constraint);
    }

    public function testValidate_Valid()
    {
        $value = new MapLocation(52.1005, 0.5005);

        $this->context->expects($this->never())
            ->method('addViolation');

        $this->validator->validate($value, $this->constraint);
    }

    public function testValidate_NotLocation()
    {
        $value = null;

        $this->context->expects($this->never())
            ->method('addViolation');

        $this->validator->validate($value, $this->constraint);
    }
}
