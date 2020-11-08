<?php

namespace Acts\CamdramSecurityBundle\Event;

use Acts\CamdramSecurityBundle\Entity\PendingAccess;
use Symfony\Contracts\EventDispatcher\Event;

class PendingAccessEvent extends Event
{
    /** @var PendingAccess */
    private $pending_ace;

    public function __construct(PendingAccess $pending_ace)
    {
        $this->pending_ace = $pending_ace;
    }

    public function getPendingAccess(): PendingAccess
    {
        return $this->pending_ace;
    }
}
