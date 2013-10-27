<?php
namespace Acts\CamdramSecurityBundle\Security\User;

use Symfony\Component\Security\Core\User\UserInterface;

interface CamdramUserInterface extends UserInterface {

    public function getName();

    public function getEmail();

    public function getUsername();

    public function getType();

    public function getProfilePictureUrl();
}