<?php
namespace Acts\CamdramSecurityBundle\Security\Acl\Voter;

use Acts\CamdramSecurityBundle\Security\OwnableInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\AbstractVoter;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

use Acts\CamdramSecurityBundle\Security\Acl\AclProvider;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Grants access if
 */
class OwnerVoter extends AbstractVoter
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
        return array('\\Acts\\CamdramSecurityBundle\\Security\\OwnableInterface');
    }

    protected function getSupportedAttributes()
    {
        return array('VIEW', 'EDIT', 'DELETE');
    }

    protected function isGranted($attribute, $object, $user = null)
    {
        if ($this->aclProvider->isOwner($user, $object)) {
            return true;
        }
        return false;
    }

}
