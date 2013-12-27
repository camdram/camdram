<?php
namespace Acts\CamdramSecurityBundle\Security\Acl\Voter;

use Acts\CamdramSecurityBundle\Security\Acl\ClassIdentity;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

use Acts\CamdramBundle\Entity\Show;
use Symfony\Component\Security\Core\User\User;

/**
 * Grants access if
 */
class CreateVoter implements VoterInterface
{
    public function supportsAttribute($attribute)
    {
        return $attribute == 'CREATE';
    }

    /**
     * @param \Symfony\Component\Security\Core\Authentication\Token\TokenInterface $token
     * @param \Acts\CamdramBundle\Entity\Show $object
     * @param array $attributes
     * @return int
     */
    public function vote(TokenInterface $token, $object, array $attributes)
    {
        if ($object instanceof ClassIdentity && $attributes == array('CREATE')) {
            switch ($object->getClassName()) {
                case 'Acts\\CamdramBundle\\Entity\\Show':
                case 'Acts\\CamdramBundle\\Entity\\TechieAdvert':
                case 'Acts\\CamdramBundle\\Entity\\Audition':
                case 'Acts\\CamdramBundle\\Entity\\Application':
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
        return $class == 'Acts\\CamdramBundle\\Entity\\Show';
    }
}
