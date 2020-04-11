<?php
namespace Acts\CamdramSecurityBundle\Security\Acl\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Acts\ExternalLoginBundle\Security\Authentication\Token\ExternalLoginToken;
use FOS\OAuthServerBundle\Security\Authentication\Token\OAuthToken;

class TokenUtilities
{

    /**
     * Utility function to find role in token
     *
     * @param TokenInterface $token
     * @param $role
     * @return bool
     */
    public static function hasRole(TokenInterface $token, string $role)
    {
        foreach ($token->getRoleNames() as $tokenRole) {
            if ($role == $tokenRole) {
                return true;
            }
        }
    }

    /**
     * Is an API request
     *
     * @param TokenInterface $token
     * @return bool
     */
    public static function isApiRequest(TokenInterface $token)
    {
        return $token instanceof OAuthToken;
    }


    /**
     * Is an interactive user request (i.e. not an API request)
     *
     * @param TokenInterface $token
     * @return bool
     */
    public static function isInteractiveRequest(TokenInterface $token)
    {
        return $token instanceof \HWI\Bundle\OAuthBundle\Security\Core\Authentication\Token\OAuthToken;
    }
}
