<?php

namespace Acts\CamdramSecurityBundle\Security\EntryPoint;

use Acts\CamdramSecurityBundle\Security\Handler\AuthenticationSuccessHandler;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStoragEInterface;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\Request;

class CamdramLoginEntryPoint implements AuthenticationEntryPointInterface
{
    private $nextEntryPoint;
    private $tokenStorage;

    public function __construct(AuthenticationEntryPointInterface $nextEntryPoint, TokenStorageInterface $tokenStorage)
    {
        $this->nextEntryPoint = $nextEntryPoint;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * {@inheritDoc}
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        $request->getSession()->set(AuthenticationSuccessHandler::LAST_AUTHENTICATION_TOKEN, $this->tokenStorage->getToken());
        $request->getSession()->set('_security.last_exception', $authException);

        return $this->nextEntryPoint->start($request, $authException);
    }
}
