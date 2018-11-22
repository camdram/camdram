<?php

namespace Camdram\Tests\CamdramSecurityBundle\Security\Acl;

use Acts\CamdramBundle\Entity\Show;
use Acts\CamdramSecurityBundle\Entity\AccessControlEntry;
use Acts\CamdramSecurityBundle\Entity\User;
use Acts\CamdramSecurityBundle\Entity\ExternalUser;
use Acts\CamdramSecurityBundle\Security\Acl\AclProvider;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Camdram\Tests\RepositoryTestCase;

class AclProviderTest extends RepositoryTestCase
{

    /**
     * @var \Acts\CamdramSecurityBundle\Security\Acl\AclProvider
     */
    private $aclProvider;

    /**
     * @var User
     */
    private $admin;

    /**
     * @var User
     */
    private $user;

    public function setUp()
    {
        parent::setUp();
        $this->aclProvider = new AclProvider($this->em, new EventDispatcher());

        $this->admin = new User();
        $this->admin->setName('Admin User')
            ->setEmail('admin@camdram.net');

        $this->user = new User();
        $this->user->setName('Test User')
            ->setEmail('testuser@camdram.net');
    
        $this->em->persist($this->admin);
        $this->em->persist($this->user);
        $this->em->flush();
    }

    private function createShow()
    {
        $show = new Show();
        $show->setName('Test Show')
            ->setCategory('drama')
            ->setAuthorisedBy($this->admin);
        $this->em->persist($show);
        $this->em->flush();

        return $show;
    }

    public function testGrantAccess()
    {
        $show = $this->createShow();

        //Grant access
        $this->aclProvider->grantAccess($show, $this->user, $this->admin);
        $this->assertTrue($this->aclProvider->isOwner($this->user, $show));

        //Revoke access
        $this->aclProvider->revokeAccess($show, $this->user, $this->admin);
        $this->assertFalse($this->aclProvider->isOwner($this->user, $show));

        //Grant access again
        $this->aclProvider->grantAccess($show, $this->user, $this->admin);
        $this->assertTrue($this->aclProvider->isOwner($this->user, $show));
    }

    public function testNullUser()
    {
        $show = $this->createShow();
        $this->assertFalse($this->aclProvider->isOwner(null, $show));
    }

    /**
     * @expectedException \ReflectionException
     */
    public function testGetEntityIdsByUser_InvalidClass()
    {
        $this->aclProvider->getEntityIdsByUser($this->user, '\AnInvalidClassName');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetEntityIdsByUser_NonOwnableClass()
    {
        $this->aclProvider->getEntityIdsByUser($this->user, '\\Acts\\CamdramBundle\\Entity\\News');
    }

    public function testGetEntityIdsByUser()
    {
        $show1 = $this->createShow();
        $show2 = $this->createShow();

        $this->aclProvider->grantAccess($show1, $this->user, $this->admin);
        $this->aclProvider->grantAccess($show2, $this->user, $this->admin);

        $ids = $this->aclProvider->getEntityIdsByUser($this->user, '\\Acts\\CamdramBundle\\Entity\\Show');
        $this->assertEquals([$show1->getId(), $show2->getId()], $ids);

        $shows = $this->aclProvider->getEntitiesByUser($this->user, '\\Acts\\CamdramBundle\\Entity\\Show');
        $this->assertEquals([$show1, $show2], $shows);

        $this->assertEquals([$this->user], $this->aclProvider->getOwners($show1));
    }
}
