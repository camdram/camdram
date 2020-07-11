<?php

namespace Acts\CamdramSecurityBundle\Security\Acl\Voter;

use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

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

        if (is_object($subject)) {
            $subject = get_class($subject);
        }

        return in_array($subject,
            [
                \Acts\CamdramBundle\Entity\Show::class,
                \Acts\CamdramBundle\Entity\Advert::class,
            ]);
    }

    public function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        if (TokenUtilities::isApiRequest($token)) {
            return TokenUtilities::hasRole($token, 'ROLE_WRITE')
                || TokenUtilities::hasRole($token, 'ROLE_WRITE_ORG');
        } else {
            return true;
        }
    }
}
