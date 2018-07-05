<?php

namespace Acts\CamdramSecurityBundle\EventListener;

use Acts\CamdramSecurityBundle\Security\User\CamdramUserInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Doctrine\ORM\EntityManager;

/**
 * LastLoginTimeListener
 *
 * Updates the 'last_login_at' field of the user every time he/she logs in
 */
class LastLoginTimeListener
{
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Updates the 'last_login_at' field of the user
     */
    public function onAuthenticationSuccess(InteractiveLoginEvent $event)
    {
        $user = $event->getAuthenticationToken()->getUser();
        if ($user instanceof CamdramUserInterface) {
            $user->setLastLoginAt(new \DateTime);
            $this->entityManager->flush($user);
        }
    }
}
