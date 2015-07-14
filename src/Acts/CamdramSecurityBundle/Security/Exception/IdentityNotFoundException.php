<?php

namespace Acts\CamdramSecurityBundle\Security\Exception;

use Symfony\Component\Security\Core\Exception\AuthenticationException;

class IdentityNotFoundException extends AuthenticationException
{
    private $token, $name;

    public function setToken($token)
    {
        $this->token = $token;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function setServiceName($name)
    {
        $this->name = $name;
    }

    public function getServiceName()
    {
        return $this->name;
    }
}
