<?php
namespace Acts\CamdramSecurityBundle\Security\Acl\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Acl\Model\AclProviderInterface;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

use Acts\CamdramSecurityBundle\Security\Acl\AclProvider;
use Acts\CamdramSecurityBundle\Security\Authentication\Token\CamdramUserToken;
use Acts\CamdramBundle\Entity\Entity;
use Acts\CamdramSecurityBundle\Entity\AccessControlEntryRepository;

/**
 * Grants access if
 */
class OwnerVoter implements VoterInterface
{
    /**
     * @var \Acts\CamdramSecurityBundle\Security\Acl\AclProvider
     */
    private $aclProvider;

    public function __construct(AclProvider $aclProvider)
    {
        $this->aclProvider = $aclProvider;
    }

    public function supportsAttribute($attribute)
    {
        return $attribute == 'EDIT';
    }

    /**
     * @param \Symfony\Component\Security\Core\Authentication\Token\TokenInterface $token
     * @param \Acts\CamdramBundle\Entity\Show $object
     * @param array $attributes
     * @return int
     */
    public function vote(TokenInterface $token, $object, array $attributes)
    {
        if ($object instanceof Entity && ($attributes == array('EDIT') || $attributes == array('VIEW'))) {
            if ($this->aclProvider->isOwner($token, $object)) {
                return self::ACCESS_GRANTED;
            }
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
        return $class == 'Acts\\CamdramBundle\\Entity\\Show'
            || $class == 'Acts\\CamdramBundle\\Entity\\Society'
            || $class == 'Acts\\CamdramBundle\\Entity\\Venue'
            || $class == 'Acts\\CamdramBundle\\Entity\\Person';
    }
}
