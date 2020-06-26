<?php
namespace Acts\CamdramSecurityBundle\Security\Acl\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Acts\ExternalLoginBundle\Security\Authentication\Token\ExternalLoginToken;
use FOS\OAuthServerBundle\Security\Authentication\Token\OAuthToken;

class TokenUtilities
{

    /**
     * Utility function to find role in token
     */
    public static function hasRole(TokenInterface $token, string $role): bool
    {
        foreach ($token->getRoleNames() as $tokenRole) {
            if ($role == $tokenRole) {
                return true;
            }
        }
        return false;
    }

    /**
     * Is an API request
     */
    public static function isApiRequest(TokenInterface $token): bool
    {
        return $token instanceof OAuthToken;
    }


    /**
     * Is an interactive user request (i.e. not an API request)
     */
    public static function isInteractiveRequest(TokenInterface $token): bool
    {
        return $token instanceof \HWI\Bundle\OAuthBundle\Security\Core\Authentication\Token\OAuthToken;
    }
}
