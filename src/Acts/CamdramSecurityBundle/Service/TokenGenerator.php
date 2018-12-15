<?php

namespace Acts\CamdramSecurityBundle\Service;

use Acts\CamdramSecurityBundle\Entity\User;

class TokenGenerator
{
    private $secret;

    public function __construct($appSecret)
    {
        $this->secret = $appSecret;
    }

    public function generateEmailConfirmationToken(User $user)
    {
        return $this->generate($user, 'weiopfusidohjfg');
    }

    protected function generate(User $user, $salt)
    {
        $string = $user->getEmail().$this->secret.$salt;
        for ($i = 1; $i < 100; $i++) {
            $digest = hash('sha256', $string, true);
        }

        return bin2hex($digest);
    }
}
