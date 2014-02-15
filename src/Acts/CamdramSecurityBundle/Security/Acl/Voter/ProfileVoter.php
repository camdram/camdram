<?php
namespace Acts\CamdramSecurityBundle\Security\Acl\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

use Acts\CamdramBundle\Entity\User;
use Acts\CamdramBundle\Entity\Person;

class ProfileVoter implements VoterInterface
{

    public function supportsAttribute($attribute)
    {
        return $attribute == 'EDIT';
    }

    public function vote(TokenInterface $token, $object, array $attributes)
    {
        if ($object instanceof Person && $attributes == array('EDIT')) {
            $user = $token->getUser();
            if ($user instanceof User && $user->getPerson() == $object) {
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
        return strpos($class, 'Acts\\CamdramBundle\\Entity\\Person') !== false;
    }
}
