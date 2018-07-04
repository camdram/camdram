<?php

namespace Acts\CamdramSecurityBundle\Security\Acl\Voter;

use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Acts\CamdramSecurityBundle\Security\Acl\ClassIdentity;

/**
 * Grants access if a standard user can create the class
 */
class CreateVoter extends Voter
{
    public function supports($attribute, $subject)
    {
        if ($attribute != "CREATE") {
            return false;
        }
    
        if (!$subject instanceof ClassIdentity) {
            $subject = new ClassIdentity(get_class($subject));
        }

        return in_array(
            $subject->getClassName(),
            [
                'Acts\\CamdramBundle\\Entity\\Show',
                'Acts\\CamdramBundle\\Entity\\TechieAdvert',
                'Acts\\CamdramBundle\\Entity\\Audition',
                'Acts\\CamdramBundle\\Entity\\Application'
            ]
        );
    }

    public function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        if (TokenUtilities::isApiRequest($token)) {
            return TokenUtilities::hasRole($token, 'ROLE_API_WRITE')
                || TokenUtilities::hasRole($token, 'ROLE_API_WRITE_ORG');
        } else {
            return true;
        }
    }
}
