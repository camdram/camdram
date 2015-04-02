<?php

namespace Acts\CamdramApiBundle\EventListener;

use Acts\CamdramApiBundle\Entity\Authorization;
use Acts\CamdramSecurityBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use FOS\OAuthServerBundle\Event\OAuthEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class OAuthEventListener
{
    /**
     * @var \Acts\CamdramApiBundle\Entity\AuthorizationRepository
     */
    private $entityManager;

    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(EntityManager $em, RequestStack $requestStack)
    {
        $this->entityManager = $em;
        $this->requestStack = $requestStack;
    }

    /**
     * Extract scopes from request for now
     * https://github.com/FriendsOfSymfony/FOSOAuthServerBundle/pull/297 will add this to the event details once merged
     *
     * @return array
     */
    private function getScopes()
    {
        $request = $this->requestStack->getMasterRequest();

        $scope_str = $request->query->get('scope',
            $request->request->get('fos_oauth_server_authorize_form' . '[scope]', '', true));
        return explode(' ', $scope_str);
    }

    public function onPreAuthorizationProcess(OAuthEvent $event)
    {
        $repo = $this->entityManager->getRepository('ActsCamdramApiBundle:Authorization');

        if ($auth = $repo->findOne($event->getUser(), $event->getClient())) {
            if ($auth->hasScopes($this->getScopes())) {
                $event->setAuthorizedClient(true);
            }
        }
    }

    public function onPostAuthorizationProcess(OAuthEvent $event)
    {
        if ($event->isAuthorizedClient() && null !== $client = $event->getClient()) {

            $repo = $this->entityManager->getRepository('ActsCamdramApiBundle:Authorization');

            if ($auth = $repo->findOne($event->getUser(), $event->getClient())) {
                $auth->addScopes($this->getScopes());
                $this->entityManager->flush();
            }
            else {
                /** @var User $user */
                $auth = new Authorization();
                $auth
                    ->setClient($event->getClient())
                    ->setUser($event->getUser())
                    ->setScopes($this->getScopes());
                $this->entityManager->persist($auth);
                $this->entityManager->flush();
            }
        }
    }

}
