<?php
namespace Acts\CamdramSecurityBundle\Security\Acl\Voter;

use Acts\CamdramSecurityBundle\Security\OwnableInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

use Acts\CamdramSecurityBundle\Security\Acl\AclProvider;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Grants access if
 */
class OwnerVoter extends BaseVoter
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

    protected function isGranted($attribute, $object, TokenInterface $token)
    {
        if ($this->isApiRequest($token) && $attribute != 'VIEW') {
            if (!$this->hasRole($token, 'ROLE_API_WRITE_USER')) return false;
        }

        return $this->aclProvider->isOwner($token->getUser(), $object);
    }

}
