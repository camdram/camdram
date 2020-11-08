<?php

namespace Acts\CamdramSecurityBundle\Event;

use Acts\CamdramSecurityBundle\Entity\AccessControlEntry;
use Symfony\Contracts\EventDispatcher\Event;

class AccessControlEntryEvent extends Event
{
    /** @var AccessControlEntry */
    private $ace;

    public function __construct(AccessControlEntry $ace)
    {
        $this->ace = $ace;
    }

    public function getAccessControlEntry(): AccessControlEntry
    {
        return $this->ace;
    }
}
