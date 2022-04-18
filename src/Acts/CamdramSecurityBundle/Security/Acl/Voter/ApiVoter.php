<?php
namespace Acts\CamdramSecurityBundle\Security\Acl\Voter;

use FOS\OAuthServerBundle\Security\Authentication\Token\OAuthToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

/**
 * Grants access if
 */
class ApiVoter implements VoterInterface
{
    public function supportsAttribute($attribute): bool
    {
        return $attribute == 'ROLE_API';
    }

    public function supportsClass($class): bool
    {
        return true;
    }

    public function vote(TokenInterface $token, $object, array $attributes): int
    {
        foreach ($attributes as $attribute) {
            if ($this->supportsAttribute($attribute) && $token instanceof OAuthToken) {
                return self::ACCESS_GRANTED;
            }
        }
        return self::ACCESS_ABSTAIN;
    }
}
