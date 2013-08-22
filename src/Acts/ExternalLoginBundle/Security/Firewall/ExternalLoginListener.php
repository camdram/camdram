<?php
namespace Acts\ExternalLoginBundle\Security\Firewall;

use Acts\ExternalLoginBundle\Security\Authentication\Token\ExternalLoginToken;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;
use Symfony\Component\Security\Http\Session\SessionAuthenticationStrategyInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Http\RememberMe\RememberMeServicesInterface;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;

use Acts\ExternalLoginBundle\Security\Service\ServiceProvider;

/**
 * ExternalLoginListener
 *
 */
class ExternalLoginListener implements ListenerInterface
{
    /**
     * @var ServiceProvider
     */
    private $serviceProvider;

    /**
     * @var array
     */
    private $urls;

    private $securityContext;
    private $sessionStrategy;
    private $dispatcher;
    private $successHandler;
    private $failureHandler;

    /**
     * Constructor.
     *
     * @param SecurityContextInterface               $securityContext       A SecurityContext instance
     * @param AuthenticationManagerInterface         $authenticationManager An AuthenticationManagerInterface instance
     * @param SessionAuthenticationStrategyInterface $sessionStrategy
     * @param HttpUtils                              $httpUtils             An HttpUtilsInterface instance
     * @param string                                 $providerKey
     * @param AuthenticationSuccessHandlerInterface  $successHandler
     * @param AuthenticationFailureHandlerInterface  $failureHandler
     * @param array                                  $options               An array of options for the processing of a
     *                                                                      successful, or failed authentication attempt
     * @param LoggerInterface                        $logger                A LoggerInterface instance
     * @param EventDispatcherInterface               $dispatcher            An EventDispatcherInterface instance
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(SecurityContextInterface $securityContext, AuthenticationManagerInterface $authenticationManager, SessionAuthenticationStrategyInterface $sessionStrategy, HttpUtils $httpUtils, $providerKey, AuthenticationSuccessHandlerInterface $successHandler, AuthenticationFailureHandlerInterface $failureHandler, array $options = array(), LoggerInterface $logger = null, EventDispatcherInterface $dispatcher = null)
    {
        if (empty($providerKey)) {
            throw new \InvalidArgumentException('$providerKey must not be empty.');
        }

        $this->securityContext = $securityContext;
        $this->authenticationManager = $authenticationManager;
        $this->sessionStrategy = $sessionStrategy;
        $this->providerKey = $providerKey;
        $this->successHandler = $successHandler;
        $this->failureHandler = $failureHandler;
        $this->logger = $logger;
        $this->dispatcher = $dispatcher;
        $this->httpUtils = $httpUtils;
    }

    /**
     * Sets the RememberMeServices implementation to use
     *
     * @param RememberMeServicesInterface $rememberMeServices
     */
    public function setRememberMeServices(RememberMeServicesInterface $rememberMeServices)
    {
        $this->rememberMeServices = $rememberMeServices;
    }

    public function setUrls($urls)
    {
        $this->urls = $urls;
    }

    /**
     * @var ServiceProvider $serviceProvider
     */
    public function setServiceProvider(ServiceProvider $serviceProvider)
    {
        $this->serviceProvider = $serviceProvider;
    }

    /**
     * {@inheritDoc}
     */
    public function handle(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        if (preg_match('/^('.preg_quote($this->urls['entry'], '/').'|'.preg_quote($this->urls['auth'], '/').')\/(.*)/i',
                $request->getPathInfo(), $matches)) {
            $action = $matches[1];
            $service_name = $matches[2];
            if ($service = $this->serviceProvider->getServiceByName($service_name)) {
                $return_url = $this->httpUtils->generateUri($event->getRequest(), $this->urls['auth'].'/'.$service_name);
                if ($action == $this->urls['entry']) {
                    $event->setResponse(new RedirectResponse($service->getAuthorizationUrl($return_url)));
                }
                elseif ($action == $this->urls['auth']) {
                    $token = new ExternalLoginToken($service_name);
                    try {
                        $access_token = $service->getAccessToken($event->getRequest(), $return_url);
                        $token->setAccessToken($access_token);
                        $this->authenticationManager->authenticate($token);

                        $this->sessionStrategy->onAuthentication($request, $token);

                        $response = $this->successHandler->onAuthenticationSuccess($request, $token);
                        $this->securityContext->setToken($token);
                    }
                    catch (AuthenticationException $e) {
                        $response = $this->failureHandler->onAuthenticationFailure($request, $e);
                    }
                    $event->setResponse($response);
                }
            }
        }
    }

}