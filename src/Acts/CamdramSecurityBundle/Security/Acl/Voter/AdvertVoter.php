<?php

namespace Acts\CamdramSecurityBundle\Security\Acl\Voter;

use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Acts\CamdramSecurityBundle\Security\Acl\AclProvider;
use Acts\CamdramBundle\Entity\Advert;

/**
 * Grants access to a show if the user is an admin of a society/venue of the show
 */
class AdvertVoter extends Voter
{
    /**
     * @var Security
     */
    private $security;

    /**
     * @var AclProvider
     */
    private $aclProvider;

    public function __construct(Security $security, AclProvider $aclProvider)
    {
        $this->security = $security;
        $this->aclProvider = $aclProvider;
    }

    public function supports($attribute, $subject)
    {
        return in_array($attribute, ['VIEW', 'EDIT', 'DELETE'])
                       && $subject instanceof Advert;
    }

    public function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        if ($subject->getShow()) {
            return $this->security->isGranted('EDIT', $subject->getShow());
        }
        elseif ($subject->getSociety()) {
            return $this->security->isGranted('EDIT', $subject->getSociety());
        }
        elseif ($subject->getVenue()) {
            return $this->security->isGranted('EDIT', $subject->getVenue());
        }
        else {
            return $this->aclProvider->isOwner($token->getUser(), $subject);
        }
    }
}
