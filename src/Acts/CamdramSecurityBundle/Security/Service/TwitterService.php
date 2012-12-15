<?php
namespace Acts\CamdramSecurityBundle\Security\Service;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\RedirectResponse,
    Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class TwitterService extends OAuth1Service
{
    public function getName()
    {
        return 'twitter';
    }

    protected $options = array(
        'authorization_url'   => 'https://api.twitter.com/oauth/authenticate',
        'request_token_url'   => 'https://api.twitter.com/oauth/request_token',
        'access_token_url'    => 'https://api.twitter.com/oauth/access_token',
        'info_url'           => 'http://api.twitter.com/1/account/verify_credentials.json',
        'realm'               => '',
    );

    public function parseUserInfo($content)
    {
        $content = json_decode($content, true);
        return array('id' => $content['id'], 'name' => $content['name'], 'username' => $content['screen_name']);
    }


}
