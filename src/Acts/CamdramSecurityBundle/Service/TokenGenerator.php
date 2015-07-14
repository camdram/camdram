<?php

namespace Acts\CamdramSecurityBundle\Service;

use Acts\CamdramSecurityBundle\Entity\User;

class TokenGenerator
{
    private $secret;

    public function __construct($secret)
    {
        $this->secret = $secret;
    }

    public function generateEmailConfirmationToken(User $user)
    {
        return $this->generate($user, 'weiopfusidohjfg');
    }

    public function generatePasswordResetToken(User $user)
    {
        return $this->generate($user, 'l;kdsfkjl234hldf');
    }

    protected function generate(User $user, $salt)
    {
        $string = $user->getEmail().$user->getPassword().$this->secret.$salt;
        for ($i = 1; $i < 100; $i++) {
            $digest = hash('sha256', $string, true);
        }

        return bin2hex($digest);
    }
}
