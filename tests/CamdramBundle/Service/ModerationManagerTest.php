<?php

namespace Camdram\Tests\CamdramBundle\Service;

use Acts\CamdramBundle\Entity\Show;
use Acts\CamdramBundle\Entity\Society;
use Acts\CamdramBundle\Entity\Venue;
use Acts\CamdramBundle\Service\ModerationManager;
use Acts\CamdramSecurityBundle\Entity\AccessControlEntry;
use Acts\CamdramSecurityBundle\Entity\User;
use Camdram\Tests\RestTestCase;
use PHPUnit\Framework\MockObject\MockObject;

class ModerationManagerTest extends RestTestCase
{
    /**
     * @var MockObject
     */
    private $dispatcher;

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
        parent::setUp();

        $this->dispatcher = $this->getMockBuilder('\\Acts\\CamdramBundle\\Service\\EmailDispatcher')
            ->disableOriginalConstructor()->getMock();
        $this->authorizationChecker = $this->getMockBuilder('\\Symfony\\Component\\Security\\Core\\Authorization\\AuthorizationCheckerInterface')
            ->disableOriginalConstructor()->getMock();
        $this->tokenStorage = $this->getMockBuilder('\\Symfony\\Component\\Security\\Core\\Authentication\\Token\\Storage\\TokenStorageInterface')
            ->disableOriginalConstructor()->getMock();
        $this->logger = $this->getMockBuilder('\\Psr\\Log\\LoggerInterface')
            ->disableOriginalConstructor()->getMock();

        // Build mock entities
        for ($i = 0; $i < 6; $i++) {
            $this->users[] = $this->createUser("User ".$i, "user".$i."@camdram.net");
        }
        $this->admin = $this->createUser("Admin User", "admin@camdram.net", AccessControlEntry::LEVEL_FULL_ADMIN);
        $this->society = new Society();
        $this->venue = new Venue();
        $this->society->setName("Society X");
        $this->venue->setName("Venue X");
        $this->ownedShow = $this->createShow("Much Ado");
        $this->entityManager->persist($this->society);
        $this->entityManager->persist($this->venue);
        $this->entityManager->persist($this->ownedShow);
        $this->entityManager->flush();
        $this->aclProvider->grantAccess($this->society, $this->users[0]);
        $this->aclProvider->grantAccess($this->society, $this->users[1]);
        $this->aclProvider->grantAccess($this->venue,   $this->users[2]);
        $this->aclProvider->grantAccess($this->venue,   $this->users[3]);
        $this->aclProvider->grantAccess($this->ownedShow, $this->users[4]);
        $this->entityManager->flush();

        $this->moderationManager = new ModerationManager($this->entityManager, $this->dispatcher,
            $this->aclProvider, $this->authorizationChecker, $this->tokenStorage, $this->logger);
    }

    /**
     * @covers Acts\CamdramBundle\Service\ModerationManager::getModeratorAdmins
     * @covers Acts\CamdramBundle\Service\ModerationManager::getModeratorsForEntity
     */
    public function testGetModerators()
    {
        $show0 = $this->createShow("Henry IV, Part I");
        $showS = $this->createShow("Henry IV, Part II");
        $showV = $this->createShow("Henry VI, Part I");
        $showSV = $this->createShow("Henry VI, Part II");
        $showS ->getSocieties()->add($this->society);
        $showSV->getSocieties()->add($this->society);
        $showV ->getPerformances()->first()->setVenue($this->venue);
        $showSV->getPerformances()->first()->setVenue($this->venue);
        $this->entityManager->flush();

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
