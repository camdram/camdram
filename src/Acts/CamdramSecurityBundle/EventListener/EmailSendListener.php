<?php
namespace Acts\CamdramSecurityBundle\EventListener;

use Acts\CamdramBundle\Entity\User;
use Acts\CamdramSecurityBundle\Event\UserEvent;
use Acts\CamdramSecurityBundle\Service\EmailConfirmationTokenGenerator;
use Symfony\Component\Routing\RouterInterface;

class EmailSendListener
{
    private $mailer;
    private $twig;
    private $generator;
    private $from_address;

    public function __construct(\Swift_Mailer $mailer, \Twig_Environment $twig,
                                EmailConfirmationTokenGenerator $generator, $from_address)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->generator = $generator;
        $this->from_address = $from_address;
    }

    public function onRegistrationEvent(UserEvent $event)
    {
        $user = $event->getUser();
        $token = $user->getIsEmailVerified() ? null : $this->generator->generate($user);

        $message = \Swift_Message::newInstance()
            ->setSubject('Welcome to Camdram')
            ->setFrom($this->from_address)
            ->setTo($user->getEmail())
            ->setBody(
                $this->twig->render(
                    'ActsCamdramBundle:Email:create_account.txt.twig',
                    array(
                        'user' => $user,
                        'email_confirmation_token' => $token
                    )
                )
            )
        ;
        $this->mailer->send($message);
    }
}