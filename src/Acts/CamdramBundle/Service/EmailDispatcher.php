<?php
namespace Acts\CamdramBundle\Service;

use Acts\CamdramBundle\Entity\Show;
use Acts\CamdramBundle\Entity\User;
use Acts\CamdramSecurityBundle\Event\UserEvent;
use Acts\CamdramSecurityBundle\Service\EmailConfirmationTokenGenerator;
use Symfony\Component\Routing\RouterInterface;

class EmailDispatcher
{
    private $mailer;
    private $twig;
    private $from_address;

    public function __construct(\Swift_Mailer $mailer, \Twig_Environment $twig, $from_address)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->from_address = $from_address;
    }

    public function sendShowCreatedEmail(Show $show, array $owners, array $users)
    {
        $emails = array();
        foreach ($users as $user) {
            $emails[$user->getEmail()] = $user->getName();
        }

        $message = \Swift_Message::newInstance()
            ->setSubject('New show needs authorization on camdram.net: '.$show->getName())
            ->setFrom($this->from_address)
            ->setTo($emails)
            ->setBody(
                $this->twig->render(
                    'ActsCamdramBundle:Email:show_created.txt.twig',
                    array(
                        'owners' => $owners,
                        'show' => $show,
                    )
                )
            )
        ;
        $this->mailer->send($message);
    }
}