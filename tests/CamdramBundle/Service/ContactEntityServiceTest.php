<?php

namespace Camdram\Tests\CamdramBundle\Service;

use Acts\CamdramBundle\Entity\Show;
use Acts\CamdramBundle\Entity\Society;
use Acts\CamdramBundle\Entity\Venue;
use Acts\CamdramSecurityBundle\Entity\User;
use Acts\CamdramBundle\Service\ContactEntityService;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class ContactEntityServiceTest extends TestCase
{
    /**
     * @var MockObject
     */
    private $aclProvider;

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
        $this->mailer = $this->getMockBuilder('\Swift_Mailer')
            ->disableOriginalConstructor()->disableOriginalClone()->getMock();
        $this->aclProvider = $this->getMockBuilder('\Acts\\CamdramSecurityBundle\\Security\\Acl\\AclProvider')
            ->disableOriginalConstructor()->getMock();

        $this->contactEntityService = new ContactEntityService($this->mailer, $this->aclProvider);

        for ($i = 0; $i < 6; $i++) {
            $user = new User();
            $user->setName("User ".$i);
            $user->setEmail("user".$i."@camdram.net");
            $user->setIsEmailVerified(!($i&1));
            $this->users[] = $user;
        }
    }

    public function testEmailEntity() {
        $showA = new Show();
        $showB = new Show();
        $showC = new Show();
        $showD = new Show();
        $showE = new Show();
        $venue = new Venue();
        $society = new Society();

        $society->setName("Society 1");
        $venue->setName("Venue 1");
        $showA->setName("Show A")->setVenue($venue)->getSocieties()->add($society);
        $showB->setName("Show B")->setVenue($venue)->getSocieties()->add($society);
        $showC->setName("Show C")->getSocieties()->add($society);
        $showD->setName("Show D")->setVenue($venue);
        $showE->setName("Show E");
        $this->aclProvider->method('getOwners')->will($this->returnValueMap([
            [$showA, [$this->users[0], $this->users[1]]],
            [$showB, []],
            [$showC, []],
            [$showD, []],
            [$showE, []],
            [$society, [$this->users[2], $this->users[3]]],
            [$venue, [$this->users[4], $this->users[5]]],
        ]));
        $this->mailer->expects($this->exactly(7))->method("send")
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
                     case "Venue 1":
                         $this->assertSame(['user4@camdram.net' => 'User 4'], $msg->getTo());
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
        $this->contactEntityService->emailEntity($society, "Camdram", "support@camdram.net", "Email Test", "A message");
        $this->contactEntityService->emailEntity($venue, "Camdram", "support@camdram.net", "Email Test", "A message");
    }
}
