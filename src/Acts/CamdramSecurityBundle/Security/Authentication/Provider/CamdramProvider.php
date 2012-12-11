<?php
namespace Acts\CamdramSecurityBundle\Security\Authentication\Provider;

use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\NonceExpiredException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Acts\CamdramSecurityBundle\Security\Authentication\Token\CamdramUserToken;
use Acts\CamdramSecurityBundle\Security\ServiceMap;
use Acts\CamdramSecurityBundle\Security\Exception\IdentityNotFoundException;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * CamdramProvider
 *
 */
class CamdramProvider implements AuthenticationProviderInterface
{
    /**
     * @var ServiceMap
     */
    private $serviceMap;

    /**
     * @var CamdramUserProvider
     */
    private $userProvider;

    /**
     * @var UserCheckerInterface
     */
    private $userChecker;

    /**
     * @param OAuthAwareUserProviderInterface $userProvider     User provider
     * @param ResourceOwnerMap                $resourceOwnerMap Resource owner map
     * @param UserCheckerInterface            $userChecker      User checker
     */
    public function __construct(UserProviderInterface $userProvider, ServiceMap $serviceMap, RouterInterface $router, UserCheckerInterface $userChecker = null)
    {
        $this->userProvider     = $userProvider;
        $this->serviceMap = $serviceMap;
        $this->router = $router;
        $this->userChecker      = $userChecker;
    }

    /**
     * {@inheritDoc}
     */
    public function supports(TokenInterface $token)
    {
        return $token instanceof CamdramUserToken;
    }

    /**
     * {@inheritDoc}
     */
    public function authenticate(TokenInterface $token)
    {
        //TODO: Check if different services load multiple different users...
        $user = null;
        $e = null;
        foreach ($token->getServices() as $service) {
            try {
                $user = $this->userProvider->loadUserByServiceAndUser($service->getName(), $service->getUserInfo());
                $this->userProvider->updateAccessToken($user, $service->getName(), $service->getAccessToken());
            } catch (IdentityNotFoundException $e) {
                $e->setToken($token);
            }
        }

        if ($user && $token->isPotentialUser($user)) {
            $token->setUserValidated($user);
        }
        else if ($e) {
            $e->setToken($token);
            throw $e;
        }
        else {
            $token->setUser($user);
            $token->setAuthenticated(true);
            $this->userChecker->checkPostAuth($user);
        }

        return $token;
    }
}