<?php

namespace Acts\CamdramApiBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;

class KernelEventListener
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $params = $event->getRequest()->request;
        // The client_id POST parameter has the database primary key embedded
        $parts = explode("_", $params->get("client_id"));
        $id = $parts[0];
        $clientId = $parts[1];
        $clientSecret = $params->get("client_secret");

        if ($id && $clientId && $clientSecret) {
            $appRepo = $this->entityManager->getRepository('ActsCamdramApiBundle:ExternalApp');
            $app = $appRepo->findByCredentials($id, $clientId, $clientSecret);

            if ($app) {
                $now = new \DateTime;
                $app->setLastUsed($now);
                $this->entityManager->flush();
            }
        }
    }
}
