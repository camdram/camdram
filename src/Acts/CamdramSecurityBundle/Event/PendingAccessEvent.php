<?php

namespace Acts\CamdramSecurityBundle\Event;

use Acts\CamdramSecurityBundle\Entity\PendingAccess;
use Symfony\Contracts\EventDispatcher\Event;

class PendingAccessEvent extends Event
{
    private $pending_ace;

    public function __construct(PendingAccess $pending_ace)
    {
        $this->pending_ace = $pending_ace;
    }

    public function getPendingAccess()
    {
        return $this->pending_ace;
    }
}
