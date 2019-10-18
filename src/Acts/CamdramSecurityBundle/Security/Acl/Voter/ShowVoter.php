<?php

namespace Acts\CamdramSecurityBundle\Security\Acl\Voter;

use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Acts\CamdramSecurityBundle\Security\Acl\AclProvider;
use Acts\CamdramBundle\Entity\Show;

/**
 * Grants access to a show if the user is an admin of a society/venue of the show
 */
class ShowVoter extends Voter
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
        return in_array($attribute, ['VIEW', 'EDIT', 'APPROVE', 'DELETE'])
                       && $subject instanceof Show;
    }

    public function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        if (TokenUtilities::isApiRequest($token) && !TokenUtilities::hasRole($token, 'ROLE_API_WRITE_ORG')) {
            return false;
        }

        foreach ($subject->getVenues() as $venue) {
            if ($this->aclProvider->isOwner($token->getUser(), $venue)) {
                return true;
            }
        }
        foreach ($subject->getSocieties() as $society) {
            if ($this->aclProvider->isOwner($token->getUser(), $society)) {
                return true;
            }
        }

        return false;
    }
}
