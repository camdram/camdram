<?php
namespace Acts\CamdramSecurityBundle\Service;

use Acts\CamdramBundle\Entity\User;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

class EmailConfirmationTokenGenerator
{
    private $secret;

    public function __construct($secret)
    {
        $this->secret = $secret;
    }

    public function generate(User $user)
    {
        $string = $user->getEmail().$user->getPassword().$this->secret;
        for ($i = 1; $i < 100; $i++) {
            $digest = hash('sha256', $string, true);
        }
        return bin2hex($digest);
    }
}