<?php
namespace Acts\CamdramSecurityBundle\Security\Service;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\RedirectResponse,
    Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class TwitterService extends AbstractService
{
    private $session;

    public function getName()
    {
        return 'twitter';
    }

    public function setSession($session)
    {
        $this->session = $session;
    }

    public function getAuthorizationUrl($redirect_uri, array $extraParameters = array())
    {
        $request_token = $this->api->getRequestToken($redirect_uri);

        $this->session->set('twitter_oauth_token', $request_token['oauth_token']);
        $this->session->set('twitter_oauth_token_secret', $request_token['oauth_token_secret']);

        return $this->api->getAuthorizeURL($request_token);
    }

    public function getAccessToken(Request $request, $redirectUri, array $extraParameters = array())
    {
        $this->api->setOAuthToken( $this->session->get('twitter_oauth_token'),
                $this->session->get('twitter_oauth_token_secret'));
        $ret = $this->api->getAccessToken();
        if (!isset($ret['oauth_token'])) {
            throw new \Exception('Error authenticating with twitter');
        }
        return array('token' => $ret['oauth_token'], 'token_secret' => $ret['oauth_token_secret']);
    }

    public function handles(Request $request)
    {
        return $request->query->has('oauth_token');
    }

    public function getUserInfo($access_token = null)
    {
        $info = $this->api->get('/account/verify_credentials');
        return array('id' => $info->id, 'username' => $info->screen_name, 'name' => $info->name);
    }




}
