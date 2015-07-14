<?php

namespace Acts\CamdramSecurityBundle\Security\Acl\Voter;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Grants access if user is an editor/'content admin'
 */
class EditorVoter extends BaseClassIdentityVoter
{
    protected function getSupportedClasses()
    {
        return array('Acts\\CamdramBundle\\Entity\\Show',
            'Acts\\CamdramBundle\\Entity\\Society',
            'Acts\\CamdramBundle\\Entity\\Venue',
            'Acts\\CamdramBundle\\Entity\\Person',
            'Acts\\CamdramBundle\\Entity\\TechieAdvert',
            'Acts\\CamdramBundle\\Entity\\Audition',
            'Acts\\CamdramBundle\\Entity\\Application',
        );
    }

    protected function getSupportedAttributes()
    {
        return array('EDIT', 'CREATE', 'APPROVE', 'DELETE');
    }

    protected function isGranted($attribute, $object, $user = null)
    {
        if ($user instanceof UserInterface) {
            foreach ($user->getRoles() as $role) {
                if ($role == 'ROLE_EDITOR') {
                    return true;
                }
            }
        }

        return false;
    }
}
