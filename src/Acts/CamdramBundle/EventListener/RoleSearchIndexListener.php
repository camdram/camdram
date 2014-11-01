<?php
namespace Acts\CamdramBundle\EventListener;

use Acts\CamdramBundle\Entity\Role;
use Acts\SphinxRealTimeBundle\Persister\ObjectPersisterInterface;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\Common\Persistence\ObjectManager;

class RoleSearchIndexListener
{

    private $searchProvider;

    private $personObjectPersister;

    public function __construct($searchProvider, ObjectPersisterInterface $personObjectPersister)
    {
        $this->searchProvider = $searchProvider;
        $this->personObjectPersister = $personObjectPersister;
    }

    private function updateSearchIndex(Role $role)
    {
        if ($this->searchProvider == 'sphinx') {
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
