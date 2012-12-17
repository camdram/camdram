<?php
namespace Acts\SocialApiBundle\Service;

use Symfony\Component\HttpFoundation\Request;

use Acts\SocialApiBundle\Exception\OAuthException;

class OAuth2Api extends OAuthApi
{

    protected function authenticateRequest(&$url, &$method, &$params)
    {
        if (is_null($this->getToken())) {
            throw new OAuthException('Unable to authenticate as no credentials have been supplied');
        }

        $params['access_token'] = $this->getToken();
    }

    public function authenticateAsSelf()
    {
        $response = $this->doAccessToken(
            $this->config['client_id'],
            $this->config['client_secret'],
            null,
            'client_credentials'
        );
        if (isset($response['access_token'])) {
            $this->authenticateWithCredentials($response['access_token']);
        }
    }

    public function authenticateWithRequest(Request $request, $redirectUri)
    {
        $response = $this->doAccessToken(
            $this->config['client_id'],
            $this->config['client_secret'],
            $request->query->get('code'),
            'authorization_code',
            $redirectUri
        );
        if (!isset($response['access_token'])) {
            throw new OAuthException(sprintf('%s returned an invalid request token', ucfirst($this->getName())));
        }

        $this->authenticateWithCredentials($response['access_token']);
    }

    public function getLoginUrl($redirectUri)
    {
        $params = array(
            'response_type' => 'code',
            'client_id'     => $this->config['client_id'],
            'scope'         => $this->config['scope'],
            'redirect_uri'  => $redirectUri,
        );
        return $this->config['login_url'].'?'.http_build_query($params);
    }

}