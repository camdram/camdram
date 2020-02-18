<?php

namespace Acts\CamdramSecurityBundle\Event;

use Acts\CamdramSecurityBundle\Entity\AccessControlEntry;
use Symfony\Contracts\EventDispatcher\Event;

class AccessControlEntryEvent extends Event
{
    private $ace;

    public function __construct(AccessControlEntry $ace)
    {
        $this->ace = $ace;
    }

    public function getAccessControlEntry()
    {
        return $this->ace;
    }
}
