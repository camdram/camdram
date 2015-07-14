<?php

namespace Acts\CamdramSecurityBundle\Security\Acl\Voter;

use Symfony\Component\Security\Core\Authorization\Voter\AbstractVoter;
use Acts\CamdramSecurityBundle\Security\Acl\AclProvider;

/**
 * Grants access if
 */
class OwnerVoter extends AbstractVoter
{
    /**
     * @var \Acts\CamdramSecurityBundle\Security\Acl\AclProvider
     */
    private $aclProvider;

    public function __construct(AclProvider $aclProvider)
    {
        $this->aclProvider = $aclProvider;
    }

    protected function getSupportedClasses()
    {
        return array('\\Acts\\CamdramSecurityBundle\\Security\\OwnableInterface');
    }

    protected function getSupportedAttributes()
    {
        return array('VIEW', 'EDIT', 'DELETE');
    }

    protected function isGranted($attribute, $object, $user = null)
    {
        if ($this->aclProvider->isOwner($user, $object)) {
            return true;
        }

        return false;
    }
}
