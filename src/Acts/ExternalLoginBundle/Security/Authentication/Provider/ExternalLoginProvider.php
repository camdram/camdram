<?php
namespace Acts\ExternalLoginBundle\Security\Authentication\Provider;

use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Acts\ExternalLoginBundle\Security\Authentication\Token\ExternalLoginToken;
use Acts\ExternalLoginBundle\Security\Service\ServiceProvider;
use Acts\ExternalLoginBundle\Security\User\ExternalLoginUserProvider;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * ExternalLoginProvider
 *
 */
class ExternalLoginProvider implements AuthenticationProviderInterface
{
    /**
     * @var ServiceProvider
     */
    private $serviceProvider;

    /**
     * @var ExternalLoginUserProvider
     */
    private $userProvider;

    /**
     * @var UserCheckerInterface
     */
    private $userChecker;

    /**
     * @param UserProviderInterface $userProvider     User provider
     * @param ServiceProvider                $serviceProvider Service provider
     * @param UserCheckerInterface            $userChecker      User checker
     */
    public function __construct(UserProviderInterface $userProvider, ServiceProvider $serviceProvider, RouterInterface $router, UserCheckerInterface $userChecker = null)
    {
        $this->userProvider     = $userProvider;
        $this->serviceProvider = $serviceProvider;
        $this->router = $router;
        $this->userChecker      = $userChecker;
    }

    /**
     * {@inheritDoc}
     */
    public function supports(TokenInterface $token)
    {
        return $token instanceof ExternalLoginToken;
    }

    /**
     * {@inheritDoc}
     */
    public function authenticate(TokenInterface $token)
    {
        if (!$this->supports($token)) {
            return null;
        }

        /** @var $token ExternalLoginToken */
        $service = $this->serviceProvider->getServiceByName($token->getServiceName());

        if (!$token->getUser()) {
            $userinfo = $service->getUserInfo($token->getAccessToken());
            try {
                $user = $this->userProvider->loadUserByServiceAndId($token->getServiceName(), $userinfo['id']);
            }
            catch (UsernameNotFoundException $e) {
                $user = $this->userProvider->persistUser($userinfo, $token->getServiceName(), $token->getAccessToken());
            }
            $token = new ExternalLoginToken($service->getName(), $token->getAccessToken(), $user->getRoles());
            $token->setUser($user);
        }

        $user = $token->getUser();
        if ($user instanceof UserInterface) {
            $this->userChecker->checkPostAuth($user);
        }
        return $token;
    }
}