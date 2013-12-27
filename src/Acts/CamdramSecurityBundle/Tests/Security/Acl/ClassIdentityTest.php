<?php
namespace Acts\CamdramSecurityBundle\Tests\Security\Acl;

use Acts\CamdramSecurityBundle\Security\Acl\ClassIdentity;

class ClassIdentityTestClass {}

class ClassIdentityTest extends \PHPUnit_Framework_TestCase
{

    private $class_name;

    public function setUp()
    {
        $this->class_name = '\Acts\CamdramSecurityBundle\Tests\Security\Acl\ClassIdentityTestClass';
    }

    public function testCreate()
    {
        $classIdentity = new ClassIdentity($this->class_name);
        $this->assertEquals($this->class_name, $classIdentity->getClassName());
    }

    public function testInvalidClassName()
    {
        $this->setExpectedException('\InvalidArgumentException');
        new ClassIdentity('\AnInvalidClassName');
    }

}