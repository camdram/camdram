<?php
namespace Acts\CamdramSecurityBundle\Security\Acl\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
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
    public static function hasRole(TokenInterface $token, $role)
    {
        if (is_string($role)) {
            $role = new Role($role);
        }


        foreach ($token->getRoles() as $tokenRole) {
            if ($role->getRole() == $tokenRole->getRole()) {
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
        return $token instanceof UsernamePasswordToken
            || $token instanceof \HWI\Bundle\OAuthBundle\Security\Core\Authentication\Token\OAuthToken;
    }
}
