<?php
namespace Acts\CamdramSecurityBundle\Security\Acl\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

/**
 * Grants access if
 */
class AdminVoter extends BaseVoter
{
    public function supportsAttribute($attribute)
    {
        return true;
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
        return strpos($class, 'Acts\\') !== false;
    }

    /**
     * @param \Symfony\Component\Security\Core\Authentication\Token\TokenInterface $token
     * @param \Acts\CamdramBundle\Entity\Show $object
     * @param array $attributes
     * @return int
     */
    public function isGranted($attribute, $object, TokenInterface $token)
    {
        if (is_object($object) && $this->isInteractiveRequest($token)) {
            return $this->hasRole($token, 'ROLE_ADMIN')
                || $this->hasRole($token, 'ROLE_SUPER_ADMIN');
        }

        return false;
    }

    //Not used
    protected function getSupportedClasses() {}

    protected function getSupportedAttributes() {}
}
