<?php
namespace Acts\CamdramSecurityBundle\Security\Service;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\RedirectResponse,
    Symfony\Component\Security\Core\Authentication\Token\TokenInterface;


class GoogleService extends OAuth2Service
{
    public function getName()
    {
        return 'google';
    }

    /**
     * {@inheritDoc}
     */
    protected $options = array(
        'authorization_url'   => 'https://accounts.google.com/o/oauth2/auth',
        'access_token_url'    => 'https://accounts.google.com/o/oauth2/token',
        'info_url'           => 'https://www.googleapis.com/oauth2/v1/userinfo',
        'scope'               => 'https://www.googleapis.com/auth/userinfo.profile https://www.googleapis.com/auth/userinfo.email',
    );

    public function parseUserInfo($content)
    {
        return array('id' => $content['id'], 'name' => $content['name'], 'username' => $content['email']);
    }

}
