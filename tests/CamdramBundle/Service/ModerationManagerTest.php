<?php

namespace Camdram\Tests\CamdramBundle\Service;

use Acts\CamdramBundle\Entity\Show;
use Acts\CamdramBundle\Entity\Society;
use Acts\CamdramBundle\Entity\Venue;
use Acts\CamdramBundle\Service\ModerationManager;
use Acts\CamdramSecurityBundle\Entity\AccessControlEntry;
use Acts\CamdramSecurityBundle\Entity\User;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class ModerationManagerTest extends TestCase
{
    /**
     * @var MockObject
     */
    private $entityManager;

    /**
     * @var MockObject
     */
    private $dispatcher;

    /**
     * @var MockObject
     */
    private $aclProvider;

    /**
     * @var MockObject
     */
    private $authorizationChecker;

    /**
     * @var MockObject
     */
    private $tokenStorage;

    /**
     * @var MockObject
     */
    private $logger;

    /**
     * @var MockObject
     */
    private $userRepo;

    /**
     * @var \Acts\CamdramBundle\Service\ModerationManager
     */
    private $moderationManager;

    # Useful shared entities with some mocking common to all tests.
    private $users = [];
    private $admin;
    private $society;
    private $venue;
    private $ownedShow;

    public function setUp(): void
    {
        $this->entityManager = $this->getMockBuilder('Doctrine\\ORM\\EntityManager')
            ->disableOriginalConstructor()->getMock();
        $this->dispatcher = $this->getMockBuilder('\\Acts\\CamdramBundle\\Service\\EmailDispatcher')
            ->disableOriginalConstructor()->getMock();
        $this->aclProvider = $this->getMockBuilder('\\Acts\\CamdramSecurityBundle\\Security\\Acl\\AclProvider')
            ->disableOriginalConstructor()->getMock();
        $this->authorizationChecker = $this->getMockBuilder('\\Symfony\\Component\\Security\\Core\\Authorization\\AuthorizationCheckerInterface')
            ->disableOriginalConstructor()->getMock();
        $this->tokenStorage = $this->getMockBuilder('\\Symfony\\Component\\Security\\Core\\Authentication\\Token\\Storage\\TokenStorageInterface')
            ->disableOriginalConstructor()->getMock();
        $this->logger = $this->getMockBuilder('\\Psr\\Log\\LoggerInterface')
            ->disableOriginalConstructor()->getMock();
        $this->userRepo = $this->getMockBuilder('\\Acts\\CamdramSecurityBundle\\Entity\\UserRepository')
            ->disableOriginalConstructor()->getMock();

        // Build mock entities
        for ($i = 0; $i < 6; $i++) {
            $user = new User();
            $user->setName("User ".$i);
            $user->setEmail("user".$i."@camdram.net");
            $this->users[] = $user;
        }
        $this->admin = new User();
        $this->admin->setName("Admin User");
        $this->admin->setEmail("admin@camdram.net");
        $this->admin->setIsEmailVerified(true);
        $this->society = new Society();
        $this->venue = new Venue();
        $this->ownedShow = new Show();

        // Any test should be able to retrieve the userRepo without further set-up.
        $this->entityManager->expects($this->any())->method('getRepository')
             ->will($this->returnValueMap([['ActsCamdramSecurityBundle:User', $this->userRepo]]));
        $this->userRepo->method('findAdmins')->with(AccessControlEntry::LEVEL_FULL_ADMIN)
             ->will($this->returnValue([$this->admin]));
        $this->userRepo->method('getEntityOwners')->will($this->returnValueMap([
            [$this->society, [$this->users[0], $this->users[1]]],
            [$this->venue,   [$this->users[2], $this->users[3]]],
            [$this->ownedShow, [$this->users[4]]]
        ]));

        $this->moderationManager = new ModerationManager($this->entityManager, $this->dispatcher,
            $this->aclProvider, $this->authorizationChecker, $this->tokenStorage, $this->logger);
    }

    /**
     * @covers Acts\CamdramBundle\Service\ModerationManager::getModeratorAdmins
     * @covers Acts\CamdramBundle\Service\ModerationManager::getModeratorsForEntity
     */
    public function testGetModerators()
    {
        $show0 = new Show();
        $showS = new Show();
        $showV = new Show();
        $showSV = new Show();
        $showS->getSocieties()->add($this->society);
        $showV->setVenue($this->venue);
        $showSV->setVenue($this->venue)->getSocieties()->add($this->society);

        $this->assertSame($this->moderationManager->getModeratorAdmins(), [$this->admin]);
        $this->assertSame($this->moderationManager->getModeratorsForEntity($show0), [$this->admin]);
        $this->assertSame($this->moderationManager->getModeratorsForEntity($showS), array_slice($this->users, 0, 2));
        $this->assertSame($this->moderationManager->getModeratorsForEntity($showV), array_slice($this->users, 2, 2));
        $this->assertSame($this->moderationManager->getModeratorsForEntity($showSV), array_slice($this->users, 0, 4));
    }

    public function testNotifySocietyChanged() {
        $this->dispatcher->expects($this->once())->method('sendShowSocietyChangedEmail')
            ->with($this->ownedShow, [$this->users[4]], array_slice($this->users, 0, 2));

        # Show owned by users[4] and society, no token stored
        $this->tokenStorage->method('getToken')->willReturn(null);
        $this->ownedShow->getSocieties()->add($this->society);
        $this->moderationManager->notifySocietyChanged($this->ownedShow);
    }
}
