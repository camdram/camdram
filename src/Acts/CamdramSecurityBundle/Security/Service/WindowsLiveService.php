<?php
namespace Acts\CamdramSecurityBundle\Security\Service;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\RedirectResponse,
    Symfony\Component\Security\Core\Authentication\Token\TokenInterface;


class WindowsLiveService extends OAuth2Service
{
    public function getName()
    {
        return 'windows_live';
    }

    /**
     * {@inheritDoc}
     */
    protected $options = array(
        'authorization_url'   => 'https://login.live.com/oauth20_authorize.srf',
        'access_token_url'    => 'https://login.live.com/oauth20_token.srf',
        'info_url'           => 'https://apis.live.net/v5.0/me',
        'scope'               => 'wl.basic wl.emails wl.signin',
    );


    public function parseUserInfo($content)
    {
        return array('id' => $content['id'], 'name' => $content['name'], 'username' => $content['emails']['account']);
    }

}
