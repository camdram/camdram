<?php

namespace Acts\CamdramApiBundle\EventListener;

use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use NinjaMutex\Lock\FlockLock;
use NinjaMutex\Mutex;

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
        $params = $event->getRequest()->request;
        // The client_id POST parameter has the database primary key embedded
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
        if (!$authed && getenv("SYMFONY_ENV") !== 'test') {
            $format = $event->getRequest()->getRequestFormat();
            if ($format == 'json' || $format == 'xml') {
                if (rand(1, 100) > 66) {
                    $response = new Response();
                    $response->setStatusCode(Response::HTTP_UNAUTHORIZED);
                    $response->setContent("Unauthenticated API requests have a 1-in-3 chance of returning a HTTP 401 error. You should use an API key.");
                    $event->setResponse($response);
                } else {
                    $lock = new FlockLock('../app/cache');
                    $mutex = new Mutex('unauth-api-req', $lock);
                    $mutex->acquireLock();
                    sleep(12);
                    $mutex->releaseLock();
                }
            }
        }
    }
}
