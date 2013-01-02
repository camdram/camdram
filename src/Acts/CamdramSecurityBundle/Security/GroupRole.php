<?php
namespace Acts\CamdramSecurityBundle\Security;

use Symfony\Component\Security\Core\Role\RoleInterface;
use Acts\CamdramSecurityBundle\Entity\Group;

class GroupRole implements RoleInterface
{
    private $group;

    /**
     * Constructor.
     *
     * @param string $role The role name
     */
    public function __construct(Group $group)
    {
        $this->group = $group;
    }

    /**
     * {@inheritdoc}
     */
    public function getRole()
    {
        return 'GROUP_'.$this->group->getShortName();
    }

    public function getGroup()
    {
        return $this->group;
    }

    public function __toString()
    {
        return $this->getRole();
    }
}
