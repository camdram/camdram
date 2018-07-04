<?php

namespace Acts\CamdramSecurityBundle\Tests\Security\Acl;

use Acts\CamdramBundle\Entity\Show;
use Acts\CamdramSecurityBundle\Entity\AccessControlEntry;
use Acts\CamdramSecurityBundle\Entity\User;
use Acts\CamdramSecurityBundle\Entity\ExternalUser;
use Acts\CamdramSecurityBundle\Security\Acl\AclProvider;
use Acts\CamdramSecurityBundle\Security\Acl\ClassIdentity;
use Symfony\Component\EventDispatcher\EventDispatcher;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class AclProviderTest extends TestCase
{
    /**
     * @var MockObject
     */
    private $em;

    /**
     * @var MockObject
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
        $this->aclProvider = new AclProvider($this->em, new EventDispatcher());
    }

    public function testIsOwner_UserIsOwner()
    {
        $user = new User();
        $user->setEmail('testuser@camdram.net');
        $show = new Show();
        $show->setName('Test Show');

        $this->repository->expects($this->once())->method('aceExists')->with($user, $show)->will($this->returnValue(true));

        $this->assertTrue($this->aclProvider->isOwner($user, $show));
    }

    public function testIsOwner_UserNotOwner()
    {
        $user = new User();
        $user->setEmail('testuser@camdram.net');
        $show = new Show();
        $show->setName('Test Show');

        $this->repository->expects($this->once())->method('aceExists')->with($user, $show)->will($this->returnValue(false));

        $this->assertFalse($this->aclProvider->isOwner($user, $show));
    }

    public function testIsOwner_ExternalUserIsOwner()
    {
        $user = new User();
        $user->setEmail('testuser@camdram.net');
        $external_user = new ExternalUser();
        $external_user->setUser($user)->setUsername('testuser');
        $show = new Show();
        $show->setName('Test Show');

        $this->repository->expects($this->once())->method('aceExists')->with($user, $show)->will($this->returnValue(false));

        $this->assertFalse($this->aclProvider->isOwner($user, $show));
    }

    public function testIsOwner_NotLoggedIn()
    {
        $show = new Show();
        $this->assertFalse($this->aclProvider->isOwner(null, $show));
    }

    public function testInvalidClassName()
    {
        $this->setExpectedException('\InvalidArgumentException');
        new ClassIdentity('\AnInvalidClassName');
    }

    public function testGetEntityIdsByUser_InvalidClass()
    {
        $user = new User();
        $user->setEmail('testuser@camdram.net');

        $this->setExpectedException('\ReflectionException');
        $this->aclProvider->getEntityIdsByUser($user, '\AnInvalidClassName');
    }

    public function testGetEntityIdsByUser_NonOwnableClass()
    {
        $user = new User();
        $user->setEmail('testuser@camdram.net');

        $this->setExpectedException('\InvalidArgumentException');
        $this->aclProvider->getEntityIdsByUser($user, '\\Acts\\CamdramBundle\\Entity\\News');
    }

    public function testGetEntityIdsByUser_ValidClass()
    {
        $user = new User();
        $user->setEmail('testuser@camdram.net');

        $ace1 = new AccessControlEntry();
        $ace1->setType('show');
        $ace1->setEntityId(32);
        $ace2 = new AccessControlEntry();
        $ace2->setType('show');
        $ace2->setEntityId(44);
        $aces = array($ace1, $ace2);

        $this->repository->expects($this->once())->method('findByUserAndType')->with($user, 'show')->will($this->returnValue($aces));

        $retAces = $this->aclProvider->getEntityIdsByUser($user, '\\Acts\\CamdramBundle\\Entity\\Show');
        $this->assertEquals(32, $retAces[0]);
        $this->assertEquals(44, $retAces[1]);
    }
}
