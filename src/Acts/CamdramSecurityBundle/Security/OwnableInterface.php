<?php

namespace Acts\CamdramSecurityBundle\Security;

interface OwnableInterface
{
    public function getId(): ?int;

    public static function getAceType(): string;
}
