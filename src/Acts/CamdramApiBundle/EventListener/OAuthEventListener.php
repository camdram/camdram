<?php

namespace Acts\CamdramApiBundle\EventListener;

use Doctrine\ORM\EntityManager;
use FOS\OAuthServerBundle\Event\OAuthEvent;

class OAuthEventListener
{
    /**
     * @var \Acts\CamdramApiBundle\Entity\AuthorisationRepository
     */
    private $repository;

    public function __construct(EntityManager $em)
    {
        $this->repository = $em->getRepository('ActsCamdramApiBundle:Authorisation');
    }

    public function onPreAuthorizationProcess(OAuthEvent $event)
    {
        if ($user = $this->getUser($event)) {
            $event->setAuthorizedClient(
                $this->repository->exists($user, $event->getClient())
            );
        }
    }

    public function onPostAuthorizationProcess(OAuthEvent $event)
    {
        if ($event->isAuthorizedClient()) {
            if (null !== $client = $event->getClient()) {
                $user = $this->getUser($event);
                $user->addClient($client);
                $user->save();
            }
        }
    }

}
