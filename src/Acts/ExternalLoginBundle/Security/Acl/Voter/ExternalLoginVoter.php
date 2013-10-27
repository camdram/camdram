<?php
namespace Acts\ExternalLoginBundle\Security\Acl\Voter;

use Acts\ExternalLoginBundle\Security\Authentication\Token\ExternalLoginToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

/**
 * Grants access if the current user is an external user
 */
class ExternalLoginVoter implements VoterInterface
{

    /**
     * @param \Symfony\Component\Security\Core\Authentication\Token\TokenInterface $token
     * @param $object
     * @param array $attributes
     * @return int
     */
    public function vote(TokenInterface $token, $object, array $attributes)
    {
        foreach ($attributes as $attribute) {
            if ($attribute == 'IS_AUTHENTICATED_EXTERNALLY') {
                if ($token instanceof ExternalLoginToken) {
                    return self::ACCESS_GRANTED;
                }
                else {
                    return self::ACCESS_DENIED;
                }
            }
        }
    }

    public function supportsAttribute($attribute)
    {
        return true;
    }

    public function supportsClass($class)
    {
        return true;
    }
}
