<?php
namespace Acts\CamdramSecurityBundle\Security\Acl\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\AbstractVoter;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

use Acts\CamdramSecurityBundle\Security\Authentication\Token\CamdramUserToken;
use Acts\CamdramSecurityBundle\Security\Acl\AclProvider;
use Acts\CamdramBundle\Entity\Show;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Grants access to a show if the user is an admin of a society/venue of the show
 */
class ShowVoter extends  AbstractVoter
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

    protected function isGranted($attribute, $object, $user = null)
    {
        if ($object->getVenue()) {
            if ($this->aclProvider->isOwner($user, $object->getVenue())) return true;
        }
        if ($object->getSociety()) {
            if ($this->aclProvider->isOwner($user, $object->getSociety())) return true;
        }
        return false;
    }

}
