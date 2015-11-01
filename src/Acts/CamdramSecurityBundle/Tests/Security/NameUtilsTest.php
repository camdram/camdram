<?php

namespace Acts\CamdramSecurityBundle\Tests\Security;

class NameUtilsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Acts\CamdramSecurityBundle\Security\NamesUtils;
     */
    private $utils;

    /**
     * @var \Acts\CamdramSecurityBundle\Entity\SimilarNameRepository;
     */
    private $repo;

    public function setUp()
    {
        $this->utils = $this->getMockBuilder('\Acts\CamdramSecurityBundle\Security\NameUtils')
            ->disableOriginalConstructor()
            ->setMethods(array('getRepository'))
            ->getMock();

        $this->repo = $this->getMockBuilder('\Acts\CamdramSecurityBundle\Entity\SimilarNameRepository')
            ->disableOriginalConstructor()->getMock();

        $this->utils->expects($this->any())->method('getRepository')
            ->will($this->returnValue($this->repo));
    }

    public function testSamePerson()
    {
        $this->assertTrue($this->utils->isSamePerson('John Smith', 'John Smith'));
        $this->assertTrue($this->utils->isSamePerson('John Smith', 'JOHN SMITH'));

        $this->assertTrue($this->utils->isSamePerson('John Fred Smith', 'John Smith'));
        $this->assertFalse($this->utils->isSamePerson('John Smith', 'Fred Smith'));

        $this->assertTrue($this->utils->isSamePerson('Tom Smith', 'Thomas Smith'));
        $this->assertFalse($this->utils->isSamePerson('Max Smith', 'Matt Smith'));
        $this->assertTrue($this->utils->isSamePerson('Andrew Smith', 'Andy Smith'));
    }
}
