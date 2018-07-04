<?php

namespace Acts\CamdramSecurityBundle\Security\Acl\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Grants access if
 */
class AdminVoter extends Voter
{
    public function supports($attribute, $subject)
    {
        return is_object($subject) &&
                    strpos(get_class($subject), 'Acts\\') !== false;
    }

    public function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        foreach ($token->getRoles() as $role) {
            if ($role->getRole() == 'ROLE_ADMIN'
                || $role->getRole() == 'ROLE_SUPER_ADMIN') {
                return true;
            }
        }

        return false;
    }
}
