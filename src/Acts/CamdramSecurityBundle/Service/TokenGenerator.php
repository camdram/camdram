<?php

namespace Acts\CamdramSecurityBundle\Service;

use Acts\CamdramSecurityBundle\Entity\User;

class TokenGenerator
{
    /** @var string */
    private $secret;

    public function __construct(string $appSecret)
    {
        $this->secret = $appSecret;
    }

    public function generateEmailConfirmationToken(User $user): string
    {
        return $this->generate($user);
    }

    public function verifyEmailConfirmationToken(User $user, string $token): bool
    {
        return hash_equals($this->generate($user), $token);
    }

    private function generate(User $user): string
    {
        $digest = $user->getEmail() . $this->secret . 'weiopfusidohjfg';
        for ($i = 1; $i < 100; $i++) {
            $digest = hash('sha256', $digest, true);
        }

        return bin2hex($digest);
    }
}
