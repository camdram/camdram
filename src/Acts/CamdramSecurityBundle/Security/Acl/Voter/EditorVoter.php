<?php
namespace Acts\CamdramSecurityBundle\Security\Acl\Voter;

use Acts\CamdramBundle\Search\SearchableInterface;
use Acts\CamdramSecurityBundle\Security\Acl\ClassIdentity;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

/**
 * Grants access if
 */
class EditorVoter implements VoterInterface
{
    public function supportsAttribute($attribute)
    {
        return true;
    }

    /**
     * @param \Symfony\Component\Security\Core\Authentication\Token\TokenInterface $token
     * @param \Acts\CamdramBundle\Entity\Show $object
     * @param array $attributes
     * @return int
     */
    public function vote(TokenInterface $token, $object, array $attributes)
    {
        if ($this->supportsClass(get_class($object)) ||
                   $object instanceof ClassIdentity && $this->supportsClass($object->getClassName())) {
            foreach ($token->getRoles() as $role) {
                if ($role->getRole() == 'ROLE_EDITOR') return self::ACCESS_GRANTED;
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
            || $class == 'Acts\\CamdramBundle\\Entity\\Person'
            || $class == 'Acts\\CamdramBundle\\Entity\\TechieAdvert'
            || $class == 'Acts\\CamdramBundle\\Entity\\Audition'
            || $class == 'Acts\\CamdramBundle\\Entity\\Application';
    }
}
