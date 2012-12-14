<?php
namespace Acts\CamdramSecurityBundle\Security\Firewall;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Http\Session\SessionAuthenticationStrategyInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\Firewall\AbstractAuthenticationListener;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;


use Acts\CamdramSecurityBundle\Security\ServiceMap,
    Acts\CamdramSecurityBundle\Security\Authentication\Token\CamdramUserToken,
    Acts\CamdramSecurityBundle\Security\Exception\IdentityNotFoundException;


/**
 * OAuthListener
 *
 * @author Geoffrey Bachelet <geoffrey.bachelet@gmail.com>
 * @author Alexander <iam.asm89@gmail.com>
 */
class CamdramListener extends AbstractAuthenticationListener
{
    /**
     * @var ServiceMap
     */
    private $serviceMap;

    /**
     * @var array
     */
    private $checkPaths;

    /**
     * @var NewIdentityHandler;
     */
    private $newIdentityHandler;

    private $securityContext;

    private $router;

    public function __construct(SecurityContextInterface $securityContext, AuthenticationManagerInterface $authenticationManager, SessionAuthenticationStrategyInterface $sessionStrategy, HttpUtils $httpUtils, $providerKey, AuthenticationSuccessHandlerInterface $successHandler, AuthenticationFailureHandlerInterface $failureHandler, array $options = array(), LoggerInterface $logger = null, EventDispatcherInterface $dispatcher = null)
    {
        parent::__construct($securityContext, $authenticationManager, $sessionStrategy, $httpUtils, $providerKey, $successHandler,  $failureHandler, $options,$logger, $dispatcher);
        $this->securityContext = $securityContext;
    }

    /**
     * @var ServiceMap $resourceOwnerMap
     */
    public function setServiceMap(ServiceMap $serviceMap)
    {
        $this->serviceMap = $serviceMap;
    }

    public function setNewIdentityHandler(NewIdentityHandler $handler)
    {
        $this->newIdentityHandler = $handler;
    }

    public function setRouter(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @param array $checkPaths
     */
    public function setCheckPaths(array $checkPaths)
    {
        $this->checkPaths = $checkPaths;
    }

    /**
     * {@inheritDoc}
     */
    public function requiresAuthentication(Request $request)
    {
        // Check if the route matches one of the check paths
        foreach ($this->checkPaths as $checkPath) {
            if ($this->httpUtils->checkRequestPath($request, $checkPath)) {
                return true;
            }
        }
        if ($this->httpUtils->checkRequestPath($request, "/login/complete")) return true;
        return false;
    }

    /**
     * {@inheritDoc}
     */
    protected function attemptAuthentication(Request $request)
    {
        $token = $this->securityContext->getToken();
        if ($token && $token->getUser()) {
            //TODO
        }

        if (!($token instanceof CamdramUserToken)) {
            $token = new CamdramUserToken;
            $this->securityContext->setToken($token);
        }

        list($service, $checkPath) = $this->serviceMap->getServiceByRequest($request);
        if ($service) {

            if (!$service->handles($request)) {
                // Can't use AuthenticationException below, as it leads to infinity loop
                throw new \RuntimeException('No oauth code in the request.');
            }

            $accessToken = $service->getAccessToken(
                $request,
                $this->httpUtils->createRequest($request, $checkPath)->getUri()
            );

            $token->addService($service->getName(), $accessToken, $service->getUserInfo($accessToken));
        }

        try {
            $token = $this->authenticationManager->authenticate($token);
            if ($token->getPotentialUserCount() > 0) {
                return new RedirectResponse($this->router->generate('camdram_security_connect_users_process'));
            }
            else {
                return $token;
            }
        }
        catch (IdentityNotFoundException $e) {
            if (isset($this->newIdentityHandler)) {
                return $this->newIdentityHandler->handle($e->getToken(), $e->getServiceName());
            }
            else throw $e;
        }
    }

}