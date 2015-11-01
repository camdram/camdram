<?php

namespace Acts\CamdramSecurityBundle\Security\Encoder;

use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;

class LegacyMd5Encoder implements PasswordEncoderInterface
{
    public function encodePassword($raw, $salt)
    {
        return md5($raw);
    }

    public function isPasswordValid($encoded, $raw, $salt)
    {
        return $encoded === $this->encodePassword($raw, $salt);
    }
}
