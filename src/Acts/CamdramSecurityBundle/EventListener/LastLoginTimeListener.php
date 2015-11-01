<?php

namespace Acts\CamdramSecurityBundle\EventListener;

use Acts\CamdramSecurityBundle\Security\User\CamdramUserInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Acts\TimeMockBundle\Service\TimeService;
use Doctrine\ORM\EntityManager;

/**
 * LastLoginTimeListener
 *
 * Updates the 'last_login_at' field of the user every time he/she logs in
 */
class LastLoginTimeListener
{
    private $timeService;

    private $entityManager;

    public function __construct(TimeService $timeService, EntityManager $entityManager)
    {
        $this->timeService = $timeService;
        $this->entityManager = $entityManager;
    }

    /**
     * Updates the 'last_login_at' field of the user
     */
    public function onAuthenticationSuccess(InteractiveLoginEvent $event)
    {
        $user = $event->getAuthenticationToken()->getUser();
        if ($user instanceof CamdramUserInterface) {
            $user->setLastLoginAt($this->timeService->getCurrentTime());
            $this->entityManager->flush($user);
        }
    }
}
