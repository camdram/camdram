<?php

namespace Camdram\Tests\CamdramBundle\Service;

use Acts\CamdramBundle\Entity\Show;
use Acts\CamdramBundle\Entity\Society;
use Acts\CamdramSecurityBundle\Entity\User;
use Acts\CamdramBundle\Service\EmailDispatcher;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class EmailDispatcherTest extends TestCase
{
    /**
     * @var MockObject
     */
    private $mailer;

    /**
     * @var MockObject
     */
    private $twig;

    /**
     * @var \Acts\CamdramBundle\Service\EmailDispatcher
     */
    private $emailDispatcher;

    private $from_address = 'from-address@camdram.net';

    public function setUp()
    {
        $this->mailer = $this->getMockBuilder('\Swift_Mailer')
            ->disableOriginalConstructor()->disableOriginalClone()->getMock();
        $this->twig = $this->getMockBuilder('\Twig_Environment')
            ->disableOriginalClone()->disableOriginalConstructor()->getMock();

        $this->emailDispatcher = new EmailDispatcher($this->mailer, $this->twig, $this->from_address);
    }

    public function testSendShowCreatedEmail()
    {
        $show = new Show();
        $owners = array('owner1', 'owner2');

        $user1 = new User();
        $user1->setEmail('user1@camdram.net');
        $user2 = new User();
        $user2->setEmail('user2@camdram.net');
        $recipients = array(
            $user1, $user2
        );
        $admins = array($user2);

        $this->twig->expects($this->exactly(2))->method('render')
            ->with($this->anything(), array('owners' => $owners, 'show' => $show))
            ->will($this->returnValue('The message'));

        $this->mailer->expects($this->once())->method('send');

        $this->emailDispatcher->sendShowCreatedEmail($show, $owners, $recipients, $admins);
    }

    public function testSendShowSocietyChangedEmail() {
        $show = new Show();
        $show->getSocieties()->add(new Society());
        $owners = array('owner1', 'owner2');

        $user1 = new User();
        $user1->setEmail('user1@camdram.net');
        $user2 = new User();
        $user2->setEmail('user2@camdram.net');
        $moderators = array(
            $user1, $user2
        );

        $this->twig->expects($this->exactly(1))->method('render')
            ->with($this->anything(), array('owners' => $owners, 'show' => $show))
            ->will($this->returnValue('The message'));

        $this->mailer->expects($this->once())->method('send');
        $this->emailDispatcher->sendShowSocietyChangedEmail($show, $owners, $moderators);
    }
}
