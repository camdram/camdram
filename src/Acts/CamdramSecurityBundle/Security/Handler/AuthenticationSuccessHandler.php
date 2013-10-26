<?php
namespace Acts\CamdramSecurityBundle\Security\Handler;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Router;
use Acts\CamdramSecurityBundle\Security\NameUtils;
use Symfony\Component\HttpFoundation\Session\Session;
use Acts\ExternalLoginBundle\Security\Authentication\Token\ExternalLoginToken;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Acts\CamdramSecurityBundle\Entity\ExternalUser;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationSuccessHandler;
use Acts\CamdramSecurityBundle\Security\UserLinker;

class AuthenticationSuccessHandler extends DefaultAuthenticationSuccessHandler
{

    const LAST_AUTHENTICATION_TOKEN = '_security.last_authentication_token';
    const NEW_TOKEN = '_security.query_link_user';

    /**
     * @var \Symfony\Component\Security\Core\SecurityContext
     */
    private $securityContext;

    /**
     * @var \Acts\CamdramSecurityBundle\Security\NameUtils
     */
    private $nameUtils;

    /**
     * @var UserLinker
     */
    private $userLinker;

    public function __construct(SecurityContext $context, NameUtils $nameUtils, UserLinker $userLinker, HttpUtils $httpUtils, $providerKey)
    {
        $this->securityContext = $context;
        $this->nameUtils = $nameUtils;
        $this->userLinker = $userLinker;
        $this->setProviderKey($providerKey);
        parent::__construct($httpUtils, array());
    }

    /**
     * This is called when an interactive authentication attempt succeeds. This
     * is called by authentication listeners inheriting from
     * AbstractAuthenticationListener.
     *
     * @param Request $request
     * @param TokenInterface $token
     *
     * @return Response never null
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
         if ($request->getSession()->has(self::LAST_AUTHENTICATION_TOKEN)) {
            $last_token =  $request->getSession()->get(self::LAST_AUTHENTICATION_TOKEN);

            $camdram_token = $this->userLinker->findCamdramToken($token, $last_token);
            $external_token = $this->userLinker->findExternalToken($token, $last_token);

            if ($camdram_token && $external_token && $camdram_token !== $external_token) {
                $camdram_user = $camdram_token->getUser();
                $external_user = $external_token->getUser();

                if ($camdram_user instanceof ExternalUser) $camdram_user = $camdram_user->getUser();

                if ($camdram_user && $external_user && !$external_user->getUser()) {
                    //We're attempted to link a Camdram account to an external account, or maybe someone else
                    //has tried to log in a separate browser window, so try to intelligently compare the names
                    if ($this->nameUtils->isSamePerson($camdram_user->getName(), $external_user->getName())
                            || $camdram_user->getEmail() == $external_user->getEmail()) {
                        //We're confident the two accounts belong to the same person so link the two together and carry on
                       $this->userLinker->linkUsers($camdram_user, $external_user);
                    }
                    else {
                        //We're not confident it's the same person, so redirect to a page where we ask the user what to do
                        $request->getSession()->set(self::NEW_TOKEN, $token);
                        $this->securityContext->setToken($last_token);
                        return $this->httpUtils->createRedirectResponse($request, 'acts_camdram_security_link_user');
                    }
                }

                //If a user has now logged in with both a camdram and external login, keep the camdram token as it has
                //greater rights
                if ($external_user->getUser() == $camdram_user && $camdram_token instanceof UsernamePasswordToken) {
                    $this->securityContext->setToken($camdram_token);
                }
            }

        }
        $request->getSession()->set(self::LAST_AUTHENTICATION_TOKEN, $token);
        return $this->httpUtils->createRedirectResponse($request, $this->determineTargetUrl($request));
    }

}