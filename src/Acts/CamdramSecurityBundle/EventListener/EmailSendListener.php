<?php
namespace Acts\CamdramSecurityBundle\EventListener;

use Acts\CamdramBundle\Entity\User;
use Acts\CamdramSecurityBundle\Event\UserEvent;
use Acts\CamdramSecurityBundle\Service\EmailDispatcher;
use Acts\CamdramSecurityBundle\Service\TokenGenerator;
use Symfony\Component\Routing\RouterInterface;

class EmailSendListener
{
    private $dispatcher;
    private $generator;

    public function __construct(EmailDispatcher $dispatcher, TokenGenerator $generator)
    {
        $this->dispatcher = $dispatcher;
        $this->generator = $generator;
    }

    public function onRegistrationEvent(UserEvent $event)
    {
        $user = $event->getUser();
        $token = $user->getIsEmailVerified() ? null : $this->generator->generateEmailConfirmationToken($user);

        $this->dispatcher->sendRegistrationEmail($user, $token);
    }

    public function onEmailChangeEvent(UserEvent $event)
    {
        $user = $event->getUser();
        $token = $this->generator->generateEmailConfirmationToken($user);

        $this->dispatcher->sendEmailVerifyEmail($user, $token);
    }
}