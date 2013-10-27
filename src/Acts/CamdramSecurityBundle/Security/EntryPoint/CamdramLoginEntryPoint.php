<?php
namespace Acts\CamdramSecurityBundle\Security\EntryPoint;

use Acts\CamdramSecurityBundle\Security\Handler\AuthenticationSuccessHandler;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface,
    Symfony\Component\Security\Core\Exception\AuthenticationException,
    Symfony\Component\HttpFoundation\Request;

class CamdramLoginEntryPoint implements AuthenticationEntryPointInterface
{
    private $nextEntryPoint;
    private $securityContext;

    public function __construct(AuthenticationEntryPointInterface $nextEntryPoint, SecurityContextInterface $securityContext)
    {
        $this->nextEntryPoint = $nextEntryPoint;
        $this->securityContext = $securityContext;
    }

    /**
     * {@inheritDoc}
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        $request->getSession()->set(AuthenticationSuccessHandler::LAST_AUTHENTICATION_TOKEN, $this->securityContext->getToken());
        $request->getSession()->set('_security.last_exception', $authException);
        return $this->nextEntryPoint->start($request, $authException);
    }
}