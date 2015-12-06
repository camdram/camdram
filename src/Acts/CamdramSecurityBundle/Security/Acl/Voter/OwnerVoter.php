<?php

namespace Acts\CamdramSecurityBundle\Security\Acl\Voter;

use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Acts\CamdramSecurityBundle\Security\Acl\AclProvider;
use Acts\CamdramSecurityBundle\Security\OwnableInterface;

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

    public function supports($attribute, $subject)
    {
        return in_array($attribute, ['VIEW', 'EDIT', 'DELETE'])
                   && $subject instanceof OwnableInterface;
    }

    public function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        if ($this->aclProvider->isOwner($token->getUser(), $subject)) {
            return true;
        }

        return false;
    }
}
