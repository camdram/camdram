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
class ShowVoter implements VoterInterface
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
        return $attribute == 'EDIT'
            || $attribute == 'APPROVE';
    }

    /**
     * @param \Symfony\Component\Security\Core\Authentication\Token\TokenInterface $token
     * @param \Acts\CamdramBundle\Entity\Show $object
     * @param array $attributes
     * @return int
     */
    public function vote(TokenInterface $token, $object, array $attributes)
    {
        if ($object instanceof Show && ($attributes == array('EDIT') || $attributes == array('APPROVE'))) {
            if ($object->getVenue()) {
                if ($this->aclProvider->isOwner($token, $object->getVenue())) return self::ACCESS_GRANTED;
            }
            if ($object->getSociety()) {
                if ($this->aclProvider->isOwner($token, $object->getSociety())) return self::ACCESS_GRANTED;
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
        return strpos($class, 'Acts\\CamdramBundle\\Entity\\Show') !== false;
    }
}
