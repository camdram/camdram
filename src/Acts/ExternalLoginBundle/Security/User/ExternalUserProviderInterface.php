<?php
namespace Acts\ExternalLoginBundle\Security\User;

use Symfony\Component\Security\Core\User\UserProviderInterface;

interface ExternalUserProviderInterface extends UserProviderInterface
{

    public function loadUserByServiceAndId($service, $remote_id);

    public function persistUser($userinfo, $service, $access_token);

}