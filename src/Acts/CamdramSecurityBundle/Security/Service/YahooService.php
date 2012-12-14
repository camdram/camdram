<?php
namespace Acts\CamdramSecurityBundle\Security\Service;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\RedirectResponse,
    Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class YahooService extends OAuth1Service
{
    public function getName()
    {
        return 'yahoo';
    }

    protected $options = array(
        'authorization_url'   => 'https://api.login.yahoo.com/oauth/v2/request_auth',
        'request_token_url'   => 'https://api.login.yahoo.com/oauth/v2/get_request_token',
        'access_token_url'    => 'https://api.login.yahoo.com/oauth/v2/get_token',
        'info_url'           => 'http://social.yahooapis.com/v1/user/{guid}/profile',
        'realm'               => '',
    );

    public function getUserInfo($accessToken = null)
    {
       $this->options['info_url'] = 'http://social.yahooapis.com/v1/user/'.$accessToken['xoauth_yahoo_guid'].'/profile';
       return parent::getUserInfo($accessToken);
    }

    public function parseUserInfo($content)
    {
        $content = simplexml_load_string($content);
        return array(
            'id' => (string)$content->guid,
            'name' => $content->givenName.' '.$content->familyName,
            'username' => (string) $content->emails->handle);
    }



}