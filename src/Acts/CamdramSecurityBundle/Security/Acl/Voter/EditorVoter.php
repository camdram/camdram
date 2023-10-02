<?php

namespace Acts\CamdramSecurityBundle\Security\Acl\Voter;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Grants access if user is an editor/'content admin'
 */
class EditorVoter extends Voter
{
    public function supports($attribute, $subject): bool
    {
        if (!in_array($attribute, ['EDIT', 'CREATE', 'APPROVE', 'DELETE'])) {
            return false;
        }

        if (is_object($subject)) {
            $subject = get_class($subject);
        }

        return in_array($subject,
            [
                \Acts\CamdramBundle\Entity\Show::class,
                \Acts\CamdramBundle\Entity\Society::class,
                \Acts\CamdramBundle\Entity\Venue::class,
                \Acts\CamdramBundle\Entity\Person::class,
                \Acts\CamdramBundle\Entity\Advert::class,
            ]);
    }

    public function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        return TokenUtilities::isInteractiveRequest($token)
            && TokenUtilities::hasRole($token, 'ROLE_EDITOR');
    }
}
