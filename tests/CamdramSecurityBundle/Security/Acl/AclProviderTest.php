<?php

namespace Camdram\Tests\CamdramSecurityBundle\Security\Acl;

use Acts\CamdramBundle\Entity\Show;
use Acts\CamdramBundle\Entity\Society;
use Acts\CamdramBundle\Entity\Venue;
use Acts\CamdramSecurityBundle\Entity\AccessControlEntry;
use Acts\CamdramSecurityBundle\Entity\User;
use Acts\CamdramSecurityBundle\Entity\ExternalUser;
use Acts\CamdramSecurityBundle\Security\Acl\AclProvider;
use DMS\PHPUnitExtensions\ArraySubset\ArraySubsetAsserts;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Camdram\Tests\RepositoryTestCase;

class AclProviderTest extends RepositoryTestCase
{
    use ArraySubsetAsserts;

    /**
     * @var \Acts\CamdramSecurityBundle\Security\Acl\AclProvider
     */
    private $aclProvider;

    /**
     * @var \Acts\CamdramSecurityBundle\Entity\AccessControlEntryRepository
     */
    private $aceRepo;

    /**
     * @var User
     */
    private $admin;

    /**
     * @var User
     */
    private $user;
    private $user2;

    public function setUp(): void
    {
        parent::setUp();
        $this->aclProvider = new AclProvider($this->em, new EventDispatcher());
        $this->aceRepo = $this->em->getRepository('ActsCamdramSecurityBundle:AccessControlEntry');

        $this->admin = new User();
        $this->admin->setName('Admin User')
            ->setEmail('admin@camdram.net');

        $this->user = new User();
        $this->user2 = new User();
        $this->user->setName('Test User')
            ->setEmail('testuser@camdram.net');
        $this->user2->setName('Test User 2')
            ->setEmail('user2@camdram.net');

        $this->em->persist($this->admin);
        $this->em->persist($this->user);
        $this->em->persist($this->user2);
        $this->em->flush();
    }

    private function createShow()
    {
        $show = new Show();
        $show->setName('Test Show')
            ->setCategory('drama')
            ->setAuthorised(true);
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

    public function testAdmin()
    {
        $this->aclProvider->revokeAdmin($this->admin);
        $this->aclProvider->grantAdmin($this->admin);
        $query = $this->aceRepo->createQueryBuilder('a')->where('a.type = :type')
            ->andWhere('a.user = :user')
            ->setParameter('type', 'security')
            ->setParameter('user', $this->admin)
            ->getQuery();
        // asserting both that there is exactly one result and that it is the
        // right level.
        $this->assertSame($query->getSingleResult()->getEntityId(), AccessControlEntry::LEVEL_FULL_ADMIN);
        $this->assertContains($this->admin, $this->aclProvider->getAdmins());
        $this->assertContains($this->admin, $this->aclProvider->getAdmins(AccessControlEntry::LEVEL_CONTENT_ADMIN));

        $this->aclProvider->grantAdmin($this->admin, AccessControlEntry::LEVEL_ADMIN);
        $this->aclProvider->grantAdmin($this->admin, AccessControlEntry::LEVEL_ADMIN); // second call should do nothing
        $this->assertSame($query->getSingleResult()->getEntityId(), AccessControlEntry::LEVEL_ADMIN);
        $this->assertNotContains($this->admin, $this->aclProvider->getAdmins());
        $this->assertContains($this->admin, $this->aclProvider->getAdmins(AccessControlEntry::LEVEL_ADMIN));
        $this->assertContains($this->admin, $this->aclProvider->getAdmins(AccessControlEntry::LEVEL_CONTENT_ADMIN));

        $this->aclProvider->revokeAdmin($this->admin);
        $this->assertTrue(empty($query->getResult()));
        $this->assertNotContains($this->admin, $this->aclProvider->getAdmins());
        $this->assertNotContains($this->admin, $this->aclProvider->getAdmins(AccessControlEntry::LEVEL_CONTENT_ADMIN));
    }

    public function testNullUser()
    {
        $show = $this->createShow();
        $this->assertFalse($this->aclProvider->isOwner(null, $show));
    }


    public function testGetOrganisationsByUser()
    {
        $society = new Society();
        $society->setName("Society 1");
        $venue = new Venue();
        $venue->setName("Venue 1");
        $this->em->persist($society);
        $this->em->persist($venue);
        $this->em->flush();

        $this->aclProvider->grantAccess($society, $this->user, $this->admin);
        $this->aclProvider->grantAccess($venue, $this->user, $this->admin);
        $this->assertArraySubset($this->aclProvider->getOrganisationsByUser($this->user),
            [$society, $venue]);
        $this->assertSame($this->aclProvider->getOrganisationsByUser($this->user2), []);
    }

    public function testGetEntityIdsByUser_InvalidClass()
    {
        $this->expectException(\ReflectionException::class);
        $this->aclProvider->getEntityIdsByUser($this->user, '\AnInvalidClassName');
    }

    public function testGetEntityIdsByUser_NonOwnableClass()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->aclProvider->getEntityIdsByUser($this->user, '\\Acts\\CamdramBundle\\Entity\\Position');
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

        $shows = $this->aclProvider->getEntitiesByUser($this->user2, '\\Acts\\CamdramBundle\\Entity\\Show');
        $this->assertEquals([], $shows);

        $this->assertEquals([$this->user], $this->aclProvider->getOwners($show1));
    }

    public function testShowAdminRequests()
    {
        $show = $this->createShow();
        $ace = new AccessControlEntry();
        $ace->setUser($this->user)
            ->setEntityId($show->getId())
            ->setCreatedAt(new \DateTime())
            ->setType('request-show');
        $this->em->persist($ace);
        $this->em->flush();
        $aceRequestQuery = $this->aceRepo->createQueryBuilder('a')
            ->where("a.type = 'request-show'")->andWhere('a.user = :user')
            ->setParameter('user', $this->user)
            ->getQuery();
        $aceGrantedQuery = $this->aceRepo->createQueryBuilder('a')
            ->where("a.type = 'show'")->andWhere('a.user = :user')
            ->setParameter('user', $this->user)
            ->getQuery();
        $aceRequestQuery->getSingleResult(); // asserts that there is only one
        $this->assertEmpty($aceGrantedQuery->getResult());

        $this->aclProvider->revokeAccess($show, $this->user, $this->admin);
        $this->assertEmpty($aceRequestQuery->getResult());
        $this->assertEmpty($aceGrantedQuery->getResult());

        $ace = new AccessControlEntry();
        $ace->setUser($this->user)
            ->setEntityId($show->getId())
            ->setCreatedAt(new \DateTime())
            ->setType('request-show');
        $this->em->persist($ace);
        $this->em->flush();
        $this->aclProvider->approveShowAccess($show, $this->user, $this->admin);
        $aceGrantedQuery->getSingleResult(); // asserts that there is only one
        $this->assertEmpty($aceRequestQuery->getResult());
    }
}
