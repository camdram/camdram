<?php
namespace Acts\CamdramSecurityBundle\EventListener;


use Acts\CamdramSecurityBundle\Entity\User;
use Acts\CamdramSecurityBundle\Service\EmailDispatcher;
use Acts\CamdramSecurityBundle\Service\TokenGenerator;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;

class UserListener {

    private $emailDispatcher;

    private $tokenGenerator;

    public function __construct(EmailDispatcher $emailDispatcher,  TokenGenerator $tokenGenerator)
    {
        $this->emailDispatcher = $emailDispatcher;
        $this->tokenGenerator = $tokenGenerator;
    }

    public function preUpdate(User $user, PreUpdateEventArgs $event)
    {
        if ($event->hasChangedField('email')) {
            $token = $this->tokenGenerator->generateEmailConfirmationToken($user);
            $this->emailDispatcher->sendEmailVerifyEmail($user, $token);
        }
    }

} 