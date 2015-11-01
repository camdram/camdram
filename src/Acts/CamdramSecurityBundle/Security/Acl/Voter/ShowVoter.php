<?php

namespace Acts\CamdramSecurityBundle\Security\Acl\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Acts\CamdramSecurityBundle\Security\Acl\AclProvider;
use Acts\CamdramBundle\Entity\Show;

/**
 * Grants access to a show if the user is an admin of a society/venue of the show
 */
class ShowVoter extends BaseVoter
{
    /**
     * @var \Acts\CamdramSecurityBundle\Security\Acl\AclProvider
     */
    private $aclProvider;

    public function __construct(AclProvider $aclProvider)
    {
        $this->aclProvider = $aclProvider;
    }

    protected function getSupportedClasses()
    {
        return array('Acts\\CamdramBundle\\Entity\\Show');
    }

    protected function getSupportedAttributes()
    {
        return array('VIEW', 'EDIT', 'APPROVE', 'DELETE');
    }

    protected function isGranted($attribute, $object, TokenInterface $token)
    {
        if ($this->isApiRequest($token) && !$this->hasRole($token, 'ROLE_API_WRITE_ORG')) {
            return false;
        }

        if ($object->getVenue()) {
            if ($this->aclProvider->isOwner($token->getUser(), $object->getVenue())) {
                return true;
            }
        }
        if ($object->getSociety()) {
            if ($this->aclProvider->isOwner($token->getUser(), $object->getSociety())) {
                return true;
            }
        }

        return false;
    }
}
