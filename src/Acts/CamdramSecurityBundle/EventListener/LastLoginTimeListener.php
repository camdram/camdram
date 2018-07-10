<?php

namespace Acts\CamdramSecurityBundle\EventListener;

use Acts\CamdramSecurityBundle\Entity\User;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use HWI\Bundle\OAuthBundle\Security\Core\Authentication\Token\OAuthToken;

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
        $now = new \DateTime;
        $token = $event->getAuthenticationToken();
        $user = $token->getUser();
        if ($user instanceof User) {
            $user->setLastLoginAt($now);

            if ($token instanceof UsernamePasswordToken)
            {
                $user->setLastPasswordLoginAt($now);
            }
            else if ($token instanceof OAuthToken)
            {
                if ($externalUser = $user->getExternalUserByService($token->getResourceOwnerName()))
                {
                    $externalUser->setLastLoginAt($now);
                }
            }

            $this->entityManager->flush();
        }
    }
}
