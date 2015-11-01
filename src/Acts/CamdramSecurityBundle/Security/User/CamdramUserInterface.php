<?php

namespace Acts\CamdramSecurityBundle\Security\User;

use Symfony\Component\Security\Core\User\UserInterface;

interface CamdramUserInterface extends UserInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getEmail();

    /**
     * @return string
     */
    public function getUsername();

    /**
     * @return string
     */
    public function getType();

    /**
     * @return string
     */
    public function getProfilePictureUrl();

    /**
     * @return \Acts\CamdramBundle\Entity\Person
     */
    public function getPerson();

    /**
     * @param \DateTime|null $last_login_at
     *
     * @return CamdramUserInterface
     */
    public function setLastLoginAt($last_login_at);
}
