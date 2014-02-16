<?php
namespace Acts\CamdramSecurityBundle\Event;

use Acts\CamdramSecurityBundle\Entity\PendingAccess;
use Symfony\Component\EventDispatcher\Event;

/**
 * Event associated with a PendingAccess entity.
 */
class PendingAccessEvent extends Event
{
    private $pending_access;
    private $entity;

    public function __construct(PendingAccess $pending_access)
    {
        $this->pending_access = $pending_access;
    }

    public function getPendingAccess()
    {
        return $this->pending_access;
    }
}

