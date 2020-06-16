<?php

namespace Acts\CamdramApiBundle\EventListener;

use Acts\CamdramApiBundle\Entity\Authorization;
use Acts\CamdramSecurityBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use FOS\OAuthServerBundle\Event\OAuthEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class OAuthEventListener implements EventSubscriberInterface
{
    /**
     * @var \Acts\CamdramApiBundle\Entity\AuthorizationRepository
     */
    private $entityManager;

    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(EntityManagerInterface $em, RequestStack $requestStack)
    {
        $this->entityManager = $em;
        $this->requestStack = $requestStack;
    }

    public static function getSubscribedEvents()
    {
        return [
            OAuthEvent::PRE_AUTHORIZATION_PROCESS => 'onPreAuthorizationProcess',
            OAuthEvent::POST_AUTHORIZATION_PROCESS => 'onPostAuthorizationProcess',
        ];
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

        $scope_str = $request->query->get('scope');
        if (!$scope_str) {
            $form = $request->request->get('fos_oauth_server_authorize_form');
            if ($form) {
                $scope_str = $form['scope'];
            }
        }
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
            } else {
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
