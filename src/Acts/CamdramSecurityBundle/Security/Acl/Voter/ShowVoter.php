<?php
namespace Acts\CamdramSecurityBundle\Security\Acl\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Acl\Model\AclProviderInterface;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

use Acts\CamdramSecurityBundle\Security\Authentication\Token\CamdramUserToken;
use Acts\CamdramSecurityBundle\Security\Acl\Permission\PermissionMap;
use Acts\CamdramBundle\Entity\Show;

/**
 * Grants access if
 */
class ShowVoter implements VoterInterface
{
    /**
     * @var \Symfony\Component\Security\Core\Authorization\Voter\VoterInterface
     */
    private $aclVoter;

    /**
     * @var \Acts\CamdramSecurityBundle\Security\Acl\Permission\PermissionMap
     */
    private $permissionMap;

    public function __construct(VoterInterface $aclVoter, PermissionMap $permissionMap)
    {
        $this->aclVoter = $aclVoter;
        $this->permissionMap = $permissionMap;
    }

    public function supportsAttribute($attribute)
    {
        return $attribute == 'EDIT' || $attribute == 'CREATE' || $attribute == 'DELETE';
    }

    /**
     * @param \Symfony\Component\Security\Core\Authentication\Token\TokenInterface $token
     * @param \Acts\CamdramBundle\Entity\Show $object
     * @param array $attributes
     * @return int
     */
    public function vote(TokenInterface $token, $object, array $attributes)
    {
        if ($object instanceof Show) {

            /*if ($society = $object->getSociety()) {
                $society_permissions = $this->permissionMap->showToOwnerPermissions($attributes);
                return $this->aclVoter->vote($token, $object->getSociety(), $attributes);
            }

            if ($venue = $object->getVenue()) {
                return $this->aclVoter->vote($token, $object->getVenue(), $attributes);
            }*/
        }
        return self::ACCESS_ABSTAIN;
    }

    /**
     * You can override this method when writing a voter for a specific domain
     * class.
     *
     * @param string $class The class name
     *
     * @return Boolean
     */
    public function supportsClass($class)
    {
        return $class == 'Acts\\CamdramBundle\\Entity\\Show';
    }
}
