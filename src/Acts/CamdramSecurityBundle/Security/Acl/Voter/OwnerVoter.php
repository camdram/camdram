<?php
namespace Acts\CamdramSecurityBundle\Security\Acl\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

use Acts\CamdramSecurityBundle\Security\Acl\AclProvider;

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
        if ($object instanceof OwnableInterface && ($attributes == array('EDIT') || $attributes == array('VIEW'))) {
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
        $reflection = new \ReflectionClass($class);
        return $reflection->implements('\\Acts\\CamdramSecurityBundle\\Security\\OwnableInterface');
    }
}
