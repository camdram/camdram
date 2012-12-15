<?php
namespace Acts\CamdramSecurityBundle\Security\Service;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\RedirectResponse,
    Symfony\Component\Security\Core\Authentication\Token\TokenInterface;


class FacebookService extends OAuth2Service
{
    public function getName()
    {
        return 'facebook';
    }

    /**
     * {@inheritDoc}
     */
    protected $options = array(
        'authorization_url'   => 'https://www.facebook.com/dialog/oauth',
        'access_token_url'    => 'https://graph.facebook.com/oauth/access_token',
        'info_url'           => 'https://graph.facebook.com/me',
        'scope'               => 'email',
    );

    /**
     * Facebook unfortunately breaks the spec by using commas instead of spaces
     * to separate scopes
     */
    public function configure()
    {
        $this->options['scope'] = str_replace(',', ' ', $this->options['scope']);
    }

    public function parseUserInfo($content)
    {
        return array('id' => $content['id'], 'name' => $content['name'], 'username' => $content['username']);
    }

}
