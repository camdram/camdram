<?php

namespace Acts\CamdramBundle\Tests\Service;

use Acts\CamdramBundle\Entity\Performance;
use Acts\CamdramBundle\Entity\Show;
use Acts\CamdramSecurityBundle\Entity\User;
use Acts\CamdramBundle\Service\DiaryHelper;
use Acts\CamdramBundle\Service\EmailDispatcher;

class EmailDispatcherTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $mailer;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
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

        $this->twig->expects($this->once())->method('render')
            ->with($this->anything(), array('owners' => $owners, 'show' => $show))
            ->will($this->returnValue('The message'));

        $this->mailer->expects($this->once())->method('send');

        $this->emailDispatcher->sendShowCreatedEmail($show, $owners, $recipients);
    }


}
