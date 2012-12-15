<?php
namespace Acts\CamdramSecurityBundle\Tests\Security;

use Acts\CamdramSecurityBundle\Security\NamesUtils;

class NameUtilsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Acts\CamdramSecurityBundle\Security\NamesUtils
     */

    public function getNameUtils()
    {
        $em = $this->getMockBuilder('\Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();
        return new \Acts\CamdramSecurityBundle\Security\NameUtils($em);
    }

    public function testSamePerson()
    {
        $this->assertTrue($this->getNameUtils()->isSamePerson('John Smith', 'John Smith'));
        $this->assertTrue($this->getNameUtils()->isSamePerson('John Smith', 'JOHN SMITH'));

        $this->assertTrue($this->getNameUtils()->isSamePerson('John Fred Smith', 'John Smith'));
        $this->assertFalse($this->getNameUtils()->isSamePerson('John Smith', 'Fred Smith'));

        $this->assertTrue($this->getNameUtils()->isSamePerson('Tom Smith', 'Thomas Smith'));
        $this->assertFalse($this->getNameUtils()->isSamePerson('Max Smith', 'Matt Smith'));
        $this->assertTrue($this->getNameUtils()->isSamePerson('Andrew Smith', 'Andy Smith'));
    }
}