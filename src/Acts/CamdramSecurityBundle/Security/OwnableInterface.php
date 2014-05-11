<?php
namespace Acts\CamdramSecurityBundle\Security;

interface OwnableInterface
{
    public function getId();

    public static function getAceType();
}