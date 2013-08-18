<?php
namespace Acts\CamdramSecurityBundle\Security\Authentication\Token;

class CamdramUserTokenService
{
    private $service_name, $access_token, $user_info;

    public function __construct($service_name, $access_token, $user_info)
    {
        $this->service_name = $service_name;
        $this->access_token = $access_token;
        $this->user_info = $user_info;
    }

    public function getName()
    {
        return $this->service_name;
    }

    public function getAccessToken()
    {
        return $this->access_token;
    }

    public function getUserInfo($key = null)
    {
        if (is_string($key)) {
            if (isset($this->user_info[$key])) {
                return $this->user_info[$key];
            }
            else {
                return null;
            }
        }
        return $this->user_info;
    }

}