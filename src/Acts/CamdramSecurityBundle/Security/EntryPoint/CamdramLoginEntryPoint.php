<?php
namespace Acts\CamdramSecurityBundle\Security\EntryPoint;

use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface,
    Symfony\Component\Security\Core\Exception\AuthenticationException,
    Symfony\Component\HttpFoundation\Request;

class CamdramLoginEntryPoint implements AuthenticationEntryPointInterface
{
    private $nextEntryPoint;

    public function __construct(AuthenticationEntryPointInterface $nextEntryPoint)
    {
        $this->nextEntryPoint = $nextEntryPoint;
    }

    /**
     * {@inheritDoc}
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        $request->getSession()->set('_security.last_exception', $authException);
        return $this->nextEntryPoint->start($request, $authException);
    }
}