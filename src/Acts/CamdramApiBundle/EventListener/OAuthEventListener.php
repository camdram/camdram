<?php

namespace Acts\CamdramApiBundle\EventListener;

use Acts\CamdramApiBundle\Entity\Authorization;
use Acts\CamdramSecurityBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use FOS\OAuthServerBundle\Event\PostAuthorizationEvent;
use FOS\OAuthServerBundle\Event\PreAuthorizationEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class OAuthEventListener implements EventSubscriberInterface
{
    /**
     * @var EntityManagerInterface
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
            PreAuthorizationEvent::class  => 'onPreAuthorizationProcess',
            PostAuthorizationEvent::class => 'onPostAuthorizationProcess',
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
            $form = $request->request->all('fos_oauth_server_authorize_form');
            if ($form) {
                $scope_str = $form['scope'];
            }
        }
        return explode(' ', $scope_str);
    }

    public function onPreAuthorizationProcess(PreAuthorizationEvent $event)
    {
        $repo = $this->entityManager->getRepository(Authorization::class);

        if ($auth = $repo->findOne($event->getUser(), $event->getClient())) {
            if ($auth->hasScopes($this->getScopes())) {
                $event->setAuthorizedClient(true);
            }
        }
    }

    public function onPostAuthorizationProcess(PostAuthorizationEvent $event)
    {
        if ($event->isAuthorizedClient() && null !== $client = $event->getClient()) {
            $repo = $this->entityManager->getRepository(Authorization::class);

            if ($auth = $repo->findOne($event->getUser(), $event->getClient())) {
                $auth->addScopes($this->getScopes());
                $this->entityManager->flush();
            } else {
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
