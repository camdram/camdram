<?php

namespace Acts\CamdramApiBundle\EventListener;

use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use FOS\OAuthServerBundle\Security\Authentication\Token\OAuthToken;
use Doctrine\ORM\EntityManagerInterface;

class KernelEventListener
{
    private $entityManager;

    private $tokenStorage;

    public function __construct(EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage)
    {
        $this->entityManager = $entityManager;
        $this->tokenStorage = $tokenStorage;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $authed = false;
        $request = $event->getRequest();

        // Deal with incrementing API apps' counters and marking their
        // requests as authenticated. Note that the client_id POST
        // parameter has the database primary key embedded.
        $params = $request->request;
        $parts = explode("_", $params->get("client_id") ?? '');
        $clientSecret = $params->get("client_secret");
        if (count($parts) == 2) {
            $id = $parts[0];
            $clientId = $parts[1];
            if ($id && $clientId && $clientSecret) {
                $appRepo = $this->entityManager->getRepository('Acts\\CamdramApiBundle\\Entity\\ExternalApp');
                $app = $appRepo->findByCredentials($id, $clientId, $clientSecret);
                if ($app) {
                    $now = new \DateTime;
                    $app->incrementRequestCounter();
                    $app->setLastUsed($now);
                    $this->entityManager->flush();
                    $authed = true;
                }
            }
        }

        // Deal with OAuth2 bearer token. It's okay to just set this
        // truthy without validation because bad bearer tokens will
        // return invalid_grant errors.
        if ($this->tokenStorage->getToken() instanceof OAuthToken) {
            $authed = true;
        }

        // If $authed is still false by now, then any API requests must be
        // unauthenticated. Make the user's life generally a bit unpleasant.
        if (!$authed && getenv("SYMFONY_ENV") !== 'test') {
            $format = $event->getRequest()->getRequestFormat();
            if ($format == 'json' || $format == 'xml') {
                if (rand(1, 100) > 66) {
                    $response = new Response();
                    $response->setStatusCode(Response::HTTP_UNAUTHORIZED);
                    $response->setContent("Unauthenticated API requests have a 1-in-3 chance of returning an HTTP 401 error. You should use an API key to avoid this.");
                    $event->setResponse($response);
                } else {
                    sleep(12);
                }
            }
        }
    }
}
