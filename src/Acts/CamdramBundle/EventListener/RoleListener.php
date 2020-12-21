<?php
namespace Acts\CamdramBundle\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;

use Acts\CamdramBundle\Entity\Role;
use Acts\CamdramBundle\Entity\PositionTag;

class RoleListener
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function prePersist(Role $role, LifecycleEventArgs $event)
    {
        $this->updatePosition($role);
    }

    public function preUpdate(Role $role, LifecycleEventArgs $event)
    {
        $this->updatePosition($role);
    }

    public function updatePosition(Role $role)
    {
        if ($role->getType() == 'prod') {
            $tagRepository = $this->entityManager->getRepository(PositionTag::class);
            $tag = $tagRepository->findOneByName($role->getRole());
            
            if ($tag) {
                $role->setPosition($tag->getPosition());
            } else {
                $role->setPosition(null);
            }
        } else {
            $role->setPosition(null);
        }
    }
}