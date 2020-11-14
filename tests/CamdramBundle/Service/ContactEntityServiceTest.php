<?php

namespace Camdram\Tests\CamdramBundle\Service;

use Acts\CamdramBundle\Entity\Performance;
use Acts\CamdramBundle\Entity\Show;
use Acts\CamdramBundle\Entity\Society;
use Acts\CamdramBundle\Entity\Venue;
use Acts\CamdramSecurityBundle\Entity\User;
use Acts\CamdramBundle\Service\ContactEntityService;
use Camdram\Tests\RestTestCase;
use PHPUnit\Framework\MockObject\MockObject;

class ContactEntityServiceTest extends RestTestCase
{
    /**
     * @var MockObject
     */
    private $mailer;

    private $users = [];

    /**
     * @var ContactEntityService
     */
    private $contactEntityService;

    public function setUp(): void
    {
        parent::setUp();

        $this->mailer = $this->getMockBuilder('\Swift_Mailer')
            ->disableOriginalConstructor()->disableOriginalClone()->getMock();

        $this->contactEntityService = new ContactEntityService($this->mailer, $this->aclProvider);

        for ($i = 0; $i < 6; $i++) {
            $user = new User();
            $user->setName("User ".$i);
            $user->setEmail("user".$i."@camdram.net");
            $user->setIsEmailVerified(!($i&1));
            $this->users[] = $user;
            $this->entityManager->persist($user);
        }
    }

    public function testEmailEntity() {
        $showA = $this->createShow("Show A");
        $showB = $this->createShow("Show B");
        $showC = $this->createShow("Show C");
        $showD = $this->createShow("Show D");
        $showE = $this->createShow("Show E");
        $venues = [new Venue(), new Venue()];
        $societies = [new Society(), new Society()];
        $this->entityManager->persist($venues[0]);
        $this->entityManager->persist($venues[1]);
        $this->entityManager->persist($societies[0]);
        $this->entityManager->persist($societies[1]);

        $societies[0]->setName("Society 1");
        $societies[1]->setName("Society 2");
        $societies[1]->setContactEmail("committee@society2.org.uk");
        $venues[0]->setName("Venue 1");
        $venues[1]->setName("Venue 2");
        $venues[1]->setContactEmail("committee@venue2.org.uk");
        $showA->getPerformances()->first()->setVenue($venues[0]);
        $showB->getPerformances()->first()->setVenue($venues[0]);
        $showD->getPerformances()->first()->setVenue($venues[0]);
        $showA->getSocieties()->add($societies[0]);
        $showB->getSocieties()->add($societies[0]);
        $showC->getSocieties()->add($societies[0]);
        $this->entityManager->flush();

        $this->aclProvider->grantAccess($showA,   $this->users[0]);
        $this->aclProvider->grantAccess($showA,   $this->users[1]);
        $this->aclProvider->grantAccess($societies[0], $this->users[2]);
        $this->aclProvider->grantAccess($societies[0], $this->users[3]);
        $this->aclProvider->grantAccess($venues[0], $this->users[4]);
        $this->aclProvider->grantAccess($venues[0], $this->users[5]);
        $this->aclProvider->grantAccess($venues[1], $this->users[5]);

        $this->mailer->expects($this->exactly(9))->method("send")
             ->with($this->callback(function($msg) {
                 if ($msg->getFrom()['support@camdram.net'] != 'Camdram') return false;
                 if (strpos($msg->getSubject(), 'Email Test') === false) return false;
                 $matches = [];
                 if (!preg_match('/receiving.*because.*(Show [A-E]|Society \d|Venue \d)/',
                     $msg->getBody(), $matches)) return false;
                 switch($matches[1]) {
                     case "Show A":
                         $this->assertSame(['user0@camdram.net' => 'User 0'], $msg->getTo());
                         return true;
                     case "Show B":
                         $this->assertSame(['user2@camdram.net' => 'User 2'], $msg->getTo());
                         return true;
                     case "Show C":
                         $this->assertSame(['user2@camdram.net' => 'User 2'], $msg->getTo());
                         return true;
                     case "Show D":
                         $this->assertSame(['user4@camdram.net' => 'User 4'], $msg->getTo());
                         return true;
                     case "Show E":
                         $this->assertSame(['support@camdram.net' => NULL], $msg->getTo());
                         return true;
                     case "Society 1":
                         $this->assertSame(['user2@camdram.net' => 'User 2'], $msg->getTo());
                         return true;
                     case "Society 2":
                         $this->assertSame(['committee@society2.org.uk' => 'Society 2'], $msg->getTo());
                         return true;
                     case "Venue 1":
                         $this->assertSame(['user4@camdram.net' => 'User 4'], $msg->getTo());
                         return true;
                     case "Venue 2":
                         $this->assertSame(['committee@venue2.org.uk' => 'Venue 2'], $msg->getTo());
                         return true;
                     default:
                         return false;
                 }
             }));

        $this->contactEntityService->emailEntity($showA, "Camdram", "support@camdram.net", "Email Test", "A message");
        $this->contactEntityService->emailEntity($showB, "Camdram", "support@camdram.net", "Email Test", "A message");
        $this->contactEntityService->emailEntity($showC, "Camdram", "support@camdram.net", "Email Test", "A message");
        $this->contactEntityService->emailEntity($showD, "Camdram", "support@camdram.net", "Email Test", "A message");
        $this->contactEntityService->emailEntity($showE, "Camdram", "support@camdram.net", "Email Test", "A message");
        $this->contactEntityService->emailEntity($societies[0], "Camdram", "support@camdram.net", "Email Test", "A message");
        $this->contactEntityService->emailEntity($venues[0], "Camdram", "support@camdram.net", "Email Test", "A message");
        $this->contactEntityService->emailEntity($societies[1], "Camdram", "support@camdram.net", "Email Test", "A message");
        $this->contactEntityService->emailEntity($venues[1], "Camdram", "support@camdram.net", "Email Test", "A message");
    }
}
