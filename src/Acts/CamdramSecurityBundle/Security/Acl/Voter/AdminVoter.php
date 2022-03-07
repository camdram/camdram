<?php

namespace Acts\CamdramSecurityBundle\Security\Acl\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Grants access if user is an admin or super admin.
 */
class AdminVoter extends Voter
{
    public function supports($attribute, $subject)
    {
        if (is_object($subject)) {
            $subject = get_class($subject);
        } else if ($subject == null) {
            return false;
        }

        return strpos($subject, 'Acts\\') !== false;
    }

    public function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        foreach ($token->getRoleNames() as $role) {
            if ($role == 'ROLE_ADMIN' || $role == 'ROLE_SUPER_ADMIN') {
                return true;
            }
        }

        return false;
    }
}
