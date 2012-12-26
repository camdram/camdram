<?php
namespace Acts\SocialApiBundle\Service;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Session\SessionInterface;

use Acts\SocialApiBundle\Exception\OAuthException;

class OAuth1Api extends OAuthApi
{

    /**
     * @var \Symfony\Component\HttpFoundation\Session\SessionInterface
     */
    private $session;

    public function setSession(SessionInterface $session)
    {
        $this->session = $session;
    }

    protected function authenticateRequest(&$url, &$method, &$params)
    {
       // if (is_null($this->getToken()) || is_null($this->getTokenSecret())) {
         //   throw new OAuthException('Unable to authenticate as no credentials have been supplied');
        //}

        $params = array_merge($params, array(
            'oauth_consumer_key'     => $this->config['client_id'],
            'oauth_timestamp'        => $this->getTimestamp(),
            'oauth_nonce'            => $this->generateNonce(),
            'oauth_version'          => '1.0',
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_token'            => $this->getToken()
        ));

        $params['oauth_signature'] = $this->signRequest($url, $method, $params);
    }

    public function authenticateAsSelf()
    {
        $this->authenticateWithCredentials($this->config['access_token'], $this->config['access_token_secret']);
    }

    public function getLoginUrl($redirectUri)
    {
        $timestamp = $this->getTimestamp();
        $response = $this->doRequestToken($redirectUri);

        if (isset($response['oauth_problem']) || (isset($response['oauth_callback_confirmed']) && ($response['oauth_callback_confirmed'] != 'true'))) {
            throw new OAuthException(sprintf('%s OAuth error: "%s"', $this->getName(), $response['oauth_problem']));
        }

        if (!isset($response['oauth_token']) || !isset($response['oauth_token_secret'])) {
            throw new OAuthException(sprintf('%s returned an invalid request token', ucfirst($this->getName())));
        }

        $response['timestamp'] = $timestamp;

        $this->session->set($this->getName().'_oauth_token', $response);

        return $this->config['login_url'].'?'.http_build_query(array('oauth_token' => $response['oauth_token']));
    }

    public function authenticateWithRequest(Request $request, $redirectUri)
    {
        if (($requestToken = $this->session->get($this->getName().'_oauth_token')) !== null) {
            $this->session->remove($this->getName().'_oauth_token');
        }
        else {
            throw new \RuntimeException('No request token found in session');
        }
        $this->authenticateWithCredentials($requestToken['oauth_token'], $requestToken['oauth_token_secret']);
        $response = $this->doAccessToken($request->query->get('oauth_verifier'));

        if (isset($response['oauth_problem'])) {
            throw new OAuthException(sprintf('%s OAuth error: "%s"', $this->getName(), $response['oauth_problem']));
        }

        if (!isset($response['oauth_token']) || !isset($response['oauth_token_secret'])) {
            throw new OAuthException(sprintf('%s returned an invalid access token', ucfirst($this->getName())));
        }
        $this->authenticateWithCredentials($response['oauth_token'], $response['oauth_token_secret']);
    }

    private function signRequest($url, $method, $parameters)
    {
        // Parameters are sorted by name, using lexicographical byte value ordering.
        // Ref: Spec: 9.1.1 (1)
        uksort($parameters, 'strcmp');

        // http_build_query should use RFC3986
        $parts = array(
            $method,
            rawurlencode($url),
            rawurlencode(str_replace(array('%7E','+'), array('~','%20'), http_build_query($parameters))),
        );

        $baseString = implode('&', $parts);

        $keyParts = array(
            rawurlencode($this->config['client_secret']),
            rawurlencode($this->getTokenSecret()),
        );

        $key = implode('&', $keyParts);

        return base64_encode(hash_hmac('sha1', $baseString, $key, true));
    }

    /**
     * Generate a nonce value for the OAuth 1 request
     *
     * @return string
     */
    protected function generateNonce()
    {
        // @codeCoverageIgnoreStart
        return md5(microtime() . mt_rand());
        // @codeCoverageIgnoreEnd
    }

    /**
     * Returns the current timestamp - factored out to ease unit testing
     *
     * @return string
     */
    protected function getTimestamp()
    {
        // @codeCoverageIgnoreStart
        return time();
        // @codeCoverageIgnoreEnd
    }

    public function canAuthenticateRequest(Request $request)
    {
        return $request->query->has('oauth_token');
    }
}