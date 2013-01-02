<?php
namespace Acts\CamdramSecurityBundle\Security\Acl\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

use Acts\CamdramSecurityBundle\Security\Authentication\Token\CamdramUserToken;
use Acts\CamdramBundle\Entity\Person;

class ProfileVoter implements VoterInterface
{

    public function supportsAttribute($attribute)
    {
        return $attribute == 'EDIT';
    }

    public function vote(TokenInterface $token, $object, array $attributes)
    {
        if ($token instanceof CamdramUserToken && $object instanceof Person) {
            $user = $token->getUser();
            if ($user && $user->getPerson() == $object) {
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
        return $class == 'Acts\\CamdramBundle\\Entity\\Person';
    }
}
