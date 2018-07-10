<?php

namespace Acts\CamdramSecurityBundle\Tests\Security\Acl;

use Acts\CamdramSecurityBundle\Security\Acl\ClassIdentity;
use PHPUnit\Framework\TestCase;

class ClassIdentityTestClass
{
}

class ClassIdentityTest extends TestCase
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

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidClassName()
    {
        new ClassIdentity('\AnInvalidClassName');
    }
}
