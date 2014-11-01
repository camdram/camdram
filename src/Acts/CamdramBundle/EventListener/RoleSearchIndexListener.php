<?php
namespace Acts\CamdramBundle\EventListener;

use Acts\CamdramBundle\Entity\Role;
use Acts\SphinxRealTimeBundle\Persister\ObjectPersisterInterface;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;

class RoleSearchIndexListener
{
    private $personObjectPersister;

    public function __construct(ObjectPersisterInterface $personObjectPersister = null)
    {
        $this->personObjectPersister = $personObjectPersister;
    }

    private function updateSearchIndex(Role $role)
    {
        if ($this->personObjectPersister instanceof ObjectPersisterInterface) {
            $this->personObjectPersister->replaceOne($role->getPerson());
        }
    }

    public function postPersist(Role $role, LifecycleEventArgs $event)
    {
        $this->updateSearchIndex($role);
    }

    public function postUpdate(Role $role, LifecycleEventArgs $event)
    {
        $this->updateSearchIndex($role);
    }

    public function postRemove(Role $role, LifecycleEventArgs $event)
    {
        $this->updateSearchIndex($role);
    }
}
