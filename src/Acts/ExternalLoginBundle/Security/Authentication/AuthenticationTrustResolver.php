<?php
namespace Acts\ExternalLoginBundle\Security\Authentication;

use Acts\ExternalLoginBundle\Security\Authentication\Token\ExternalLoginToken;
use Symfony\Component\Security\Core\Authentication\AuthenticationTrustResolver as SymfonyAuthenticationTrustResolver;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * A custom AuthenticationTrustResolver, which takes external users into account
 *
 */
class AuthenticationTrustResolver extends SymfonyAuthenticationTrustResolver
{

    public function isExternal(TokenInterface $token = null)
    {
        if (null === $token) {
            return false;
        }

        return $token instanceof ExternalLoginToken;
    }

    /**
     * {@inheritDoc}
     */
    public function isFullFledged(TokenInterface $token = null)
    {
        if (null === $token) {
            return false;
        }

        return !$this->isAnonymous($token) && !$this->isRememberMe($token) && !$this->isExternal($token);
    }
}
