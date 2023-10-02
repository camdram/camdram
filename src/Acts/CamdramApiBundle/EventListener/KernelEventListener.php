<?php

namespace Acts\CamdramApiBundle\EventListener;

use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;

class KernelEventListener
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $authed = false;
        $request = $event->getRequest();

        // For some unfathomable reason, Symfony apears to strip out the
        // Authorization header. Here we check if we can and should add
        // it back in.
        if ((!$request->headers->get('Authorization')) && function_exists("apache_request_headers")) {
            $all_headers = apache_request_headers();
            if (isset($all_headers['Authorization'])) {
                $request->headers->set('Authorization', $all_headers['Authorization']);
            }
        }

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
                $appRepo = $this->entityManager->getRepository('ActsCamdramApiBundle:ExternalApp');
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
        if (substr($event->getRequest()->headers->get('Authorization'), 0, 6) == 'Bearer') {
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
