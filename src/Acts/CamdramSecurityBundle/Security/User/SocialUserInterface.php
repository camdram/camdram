<?php

namespace Acts\CamdramSecurityBundle\Security\User;

use Symfony\Component\Security\Core\User\UserInterface;

interface SocialUserInterface extends UserInterface
{
    /**
     * Returns the human name of the user
     *
     * @return string
     */
    public function getName();
}
