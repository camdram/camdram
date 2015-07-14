<?php

namespace Acts\CamdramSecurityBundle\Security\Acl\Voter;

use Symfony\Component\Security\Core\User\User;

/**
 * Grants access if a standard user can create the class
 */
class CreateVoter extends BaseClassIdentityVoter
{
    protected function getSupportedAttributes()
    {
        return array('CREATE');
    }

    protected function getSupportedClasses()
    {
        return array(
            'Acts\\CamdramBundle\\Entity\\Show',
            'Acts\\CamdramBundle\\Entity\\TechieAdvert',
            'Acts\\CamdramBundle\\Entity\\Audition',
            'Acts\\CamdramBundle\\Entity\\Application'
        );
    }

    protected function isGranted($attribute, $object, $user = null)
    {
        return true;
    }
}
