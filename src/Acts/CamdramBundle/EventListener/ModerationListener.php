<?php

namespace Acts\CamdramBundle\EventListener;

use Acts\CamdramBundle\Entity\Show;
use Acts\CamdramBundle\Event\EntityEvent;
use Acts\CamdramBundle\Service\ModerationManager;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Class AutoApproveListener
 *
 * Listens to creation events and either automatically approves the show or trigger sending an email
 */
class ModerationListener
{
    private $securityContext;
    private $entityManager;
    private $moderation;

    public function __construct(SecurityContextInterface $securityContext, EntityManager $entityManager, ModerationManager $moderation)
    {
        $this->securityContext = $securityContext;
        $this->entityManager = $entityManager;
        $this->moderation = $moderation;
    }

    public function onShowCreated(EntityEvent $event)
    {
    }
}
