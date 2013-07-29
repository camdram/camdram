<?php
namespace Acts\CamdramSecurityBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Acts\CamdramSecurityBundle\Entity\Group;

class GroupListener
{

    private $role_data = array();

    public function __construct($role_data)
    {
        $this->role_data = $role_data;
    }

    public function postLoad(LifecycleEventArgs $args)
    {
        if ($args->getEntity() instanceof Group) {
            /** @var $group Group */
            $group = $args->getEntity();
            if (isset($this->role_data[$group->getShortName()])) {
                $reflection = new \ReflectionProperty(get_class($group), 'roles');
                $reflection->setAccessible(true);
                $reflection->setValue($group, $this->role_data[$group->getShortName()]['roles']);
                $reflection->setAccessible(false);
            }
        }
    }
}

