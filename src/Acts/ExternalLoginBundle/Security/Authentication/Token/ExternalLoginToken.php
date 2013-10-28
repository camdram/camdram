<?php
namespace Acts\ExternalLoginBundle\Security\Authentication\Token;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;
use Symfony\Component\Security\Core\User\UserInterface;

class ExternalLoginToken extends AbstractToken
{
    /**
     * @param string $accessToken The OAuth access token
     * @param array  $roles       Roles for the token
     */
    public function __construct($service_name, $access_token, array $roles = array())
    {
        parent::__construct($roles);
        $this->setAttribute('service_name', $service_name);
        $this->setAuthenticated(count($roles) > 0);
        $this->setAccessToken($access_token);
    }

    public function getServiceName()
    {
        return $this->getAttribute('service_name');
    }

    /**
     * {@inheritDoc}
     */
    public function getCredentials()
    {
        return '';
    }

    public function setAccessToken($access_token)
    {
        $this->setAttribute('access_token', $access_token);
    }

    public function getAccessToken()
    {
        return $this->getAttribute('access_token');
    }

}