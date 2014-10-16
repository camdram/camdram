<?php
namespace Acts\CamdramSecurityBundle\Security\Acl\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

use Acts\CamdramSecurityBundle\Security\Authentication\Token\CamdramUserToken;
use Acts\CamdramSecurityBundle\Security\Acl\AclProvider;
use Acts\CamdramBundle\Entity\Show;

/**
 * Grants access if
 */
class EmailBuilderVoter implements VoterInterface
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
     * @param \Acts\CamdramBundle\Entity\EmailBuilder $object
     * @param array $attributes
     * @return int
     */
    public function vote(TokenInterface $token, $object, array $attributes)
    {
        if ($object instanceof EmailBuilder) {
            if ($attributes == array('EDIT') || $attributes == array('VIEW')) {
                if ($this->aclProvider->isOwner($token, $object))
                {
                    return self::ACCESS_GRANTED;
                }
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
        return strpos($class, 'Acts\\CamdramBundle\\Entity\\EmailBuilder') !== false;
    }
}
