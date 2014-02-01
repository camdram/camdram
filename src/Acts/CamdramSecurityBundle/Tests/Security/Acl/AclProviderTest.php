<?php
namespace Acts\CamdramSecurityBundle\Tests\Security\Acl;

use Acts\CamdramBundle\Entity\Show;
use Acts\CamdramBundle\Entity\User;
use Acts\CamdramSecurityBundle\Entity\ExternalUser;
use Acts\CamdramSecurityBundle\Security\Acl\AclProvider;
use Acts\CamdramSecurityBundle\Security\Acl\ClassIdentity;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class AclProviderTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $em;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $repository;

    /**
     * @var \Acts\CamdramSecurityBundle\Security\Acl\AclProvider
     */
    private $aclProvider;

    public function setUp()
    {
        $this->repository = $this->getMockBuilder('\Acts\CamdramSecurityBundle\Entity\AccessControlEntryRepository')
            ->disableOriginalConstructor()->getMock();
        $this->em = $this->getMockBuilder('Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();
        $this->em->expects($this->any())->method('getRepository')->with('ActsCamdramSecurityBundle:AccessControlEntry')
                ->will($this->returnValue($this->repository));
        $this->aclProvider = new AclProvider($this->em);
    }

    public function testIsOwner_UserIsOwner()
    {
        $user = new User;
        $user->setEmail('testuser@camdram.net');
        $show = new Show;
        $show->setName('Test Show');
        $token = new UsernamePasswordToken($user, '', 'public');

        $this->repository->expects($this->once())->method('aceExists')->with($user, $show)->will($this->returnValue(true));

        $this->assertTrue($this->aclProvider->isOwner($token, $show));
    }

    public function testIsOwner_UserNotOwner()
    {
        $user = new User;
        $user->setEmail('testuser@camdram.net');
        $show = new Show;
        $show->setName('Test Show');
        $token = new UsernamePasswordToken($user, '', 'public');

        $this->repository->expects($this->once())->method('aceExists')->with($user, $show)->will($this->returnValue(false));

        $this->assertFalse($this->aclProvider->isOwner($token, $show));
    }

    public function testIsOwner_ExternalUserIsOwner()
    {
        $user = new User;
        $user->setEmail('testuser@camdram.net');
        $external_user = new ExternalUser;
        $external_user->setUser($user)->setUsername('testuser');
        $show = new Show;
        $show->setName('Test Show');
        $token = new UsernamePasswordToken($external_user, '', 'public');

        $this->repository->expects($this->once())->method('aceExists')->with($user, $show)->will($this->returnValue(false));

        $this->assertFalse($this->aclProvider->isOwner($token, $show));
    }

    public function testIsOwner_NotLoggedIn()
    {
        $token = new AnonymousToken('test', 'anon');
        $show = new Show;
        $this->assertFalse($this->aclProvider->isOwner($token, $show));
    }

    public function testInvalidClassName()
    {
        $this->setExpectedException('\InvalidArgumentException');
        new ClassIdentity('\AnInvalidClassName');
    }

}