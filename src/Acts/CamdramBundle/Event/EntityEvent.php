<?php
namespace Acts\CamdramBundle\Event;

use Acts\CamdramBundle\Entity\User;
use Symfony\Component\EventDispatcher\Event;

class EntityEvent extends Event
{
    private $entity;

    public function __construct($entity, User $user)
    {
        $this->entity = $entity;
        $this->user = $user;
    }

    public function getEntity()
    {
        return $this->entity;
    }

    public function getUser()
    {
        return $this->user;
    }

}