<?php

namespace Acts\CamdramSecurityBundle\Security\Acl\Voter;

use Acts\CamdramBundle\Entity\Society;
use Acts\CamdramBundle\Entity\Venue;
use Acts\CamdramSecurityBundle\Security\OwnableInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Acts\CamdramSecurityBundle\Security\Acl\AclProvider;

/**
 * Grants access if user is the 'owner' of the subject
 */
class OwnerVoter extends Voter
{
    /**
     * @var \Acts\CamdramSecurityBundle\Security\Acl\AclProvider
     */
    private $aclProvider;

    public function __construct(AclProvider $aclProvider)
    {
        $this->aclProvider = $aclProvider;
    }

    public function supports($attribute, $subject): bool
    {
        return in_array($attribute, ['VIEW', 'EDIT', 'DELETE'])
                   && $subject instanceof OwnableInterface;
    }

    public function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        if (TokenUtilities::isApiRequest($token)) {
            if ($subject instanceof Society || $subject instanceof Venue) {
                if (!TokenUtilities::hasRole($token, 'ROLE_WRITE_ORG')) {
                    return false;
                }
            } else {
                if (!TokenUtilities::hasRole($token, 'ROLE_WRITE')) {
                    return false;
                }
            }
        }
        return $this->aclProvider->isOwner($token->getUser(), $subject);
    }
}
