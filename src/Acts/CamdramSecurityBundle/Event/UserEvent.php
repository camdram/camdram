<?php

namespace Acts\CamdramSecurityBundle\Event;

use Acts\CamdramSecurityBundle\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class UserEvent extends Event
{
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function getUser()
    {
        return $this->user;
    }
}
