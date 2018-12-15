<?php

namespace Acts\CamdramSecurityBundle\EventListener;

use Acts\CamdramSecurityBundle\Entity\User;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\SecurityEvents;
use Symfony\Component\Security\Core\Authentication\Token\RememberMeToken;
use Acts\CamdramBundle\Service\Time;

/**
 * LastLoginTimeListener
 *
 * Updates the 'last_login_at' field of the user every time he/she logs in
 */
class LastLoginTimeListener implements EventSubscriberInterface
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public static function getSubscribedEvents()
    {
        return [
            SecurityEvents::INTERACTIVE_LOGIN => 'onAuthenticationSuccess',
        ];
    }

    /**
     * Updates the 'last_login_at' field of the user
     */
    public function onAuthenticationSuccess(InteractiveLoginEvent $event)
    {
        $now = Time::now();
        $token = $event->getAuthenticationToken();
        $user = $token->getUser();
        if ($user instanceof User) {
            $user->setLastSessionAt($now);

            if (!$token instanceof RememberMeToken) {
                $user->setLastLoginAt($now);

                if ($externalUser = $user->getExternalUserByService($token->getResourceOwnerName()))
                {
                    $externalUser->setLastLoginAt($now);
                }
            }

            $this->entityManager->flush();
        }
    }
}
