<?php

namespace Acts\CamdramSecurityBundle\Security\Acl\Voter;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Acts\CamdramSecurityBundle\Security\Acl\ClassIdentity;

/**
 * Grants access if user is an editor/'content admin'
 */
class EditorVoter extends Voter
{
    public function supports($attribute, $subject)
    {
        if (!in_array($attribute, ['EDIT', 'CREATE', 'APPROVE', 'DELETE'])) return false;
    
        if (!$subject instanceof ClassIdentity) {
            $subject = new ClassIdentity(get_class($subject));
        }

        return in_array(
            $subject->getClassName(),
            [
                'Acts\\CamdramBundle\\Entity\\Show',
                'Acts\\CamdramBundle\\Entity\\Society',
                'Acts\\CamdramBundle\\Entity\\Venue',
                'Acts\\CamdramBundle\\Entity\\Person',
                'Acts\\CamdramBundle\\Entity\\TechieAdvert',
                'Acts\\CamdramBundle\\Entity\\Audition',
                'Acts\\CamdramBundle\\Entity\\Application',
            ]
        );
    }

    public function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        foreach ($token->getRoles() as $role) {
            if ($role->getRole() == 'ROLE_EDITOR') {
                return true;
            }
        }
        
        return false;
    }
}
