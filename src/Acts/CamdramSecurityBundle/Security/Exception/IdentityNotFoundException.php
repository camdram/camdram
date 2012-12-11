<?php
namespace Acts\CamdramSecurityBundle\Security\Exception;

use Symfony\Component\Security\Core\Exception\AuthenticationException;

class IdentityNotFoundException extends AuthenticationException
{
    private $token;

    public function setToken($token)
    {
        $this->token = $token;
    }

    public function getToken()
    {
        return $this->token;
    }
}
