<?php

/*
 * This file is part of the HWIOAuthBundle package.
 *
 * (c) Hardware.Info <opensource@hardware.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Acts\CamdramSecurityBundle\Security\Service;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

use Buzz\Client\ClientInterface as HttpClientInterface;

use Symfony\Component\Security\Core\Exception\AuthenticationException,
    Symfony\Component\Security\Http\HttpUtils,
    Symfony\Component\HttpFoundation\Request;

abstract class OAuth1Service extends AbstractService
{
    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * @var array
     */
    protected $options = array(
        'client_id' => '',
        'client_secret' => '',
        'info_url' => '',
        'realm' => null,
    );

    /**
     * @param HttpClientInterface                $httpClient Buzz http client
     * @param HttpUtils                          $httpUtils  Http utils
     * @param array                              $options    Options for the resource owner
     * @param string                             $name       Name for the resource owner
     * @param OAuth1RequestTokenStorageInterface $storage Request token storage
     */

    public function setSession(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function parseUserInfo($content)
    {
        return $content;
    }

    /**
     * {@inheritDoc}
     */
    public function getUserInfo($accessToken = null)
    {
        $parameters = array(
            'oauth_consumer_key'     => $this->getOption('client_id'),
            'oauth_timestamp'        => time(),
            'oauth_nonce'            => $this->generateNonce(),
            'oauth_version'          => '1.0',
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_token'            => $accessToken['oauth_token'],
        );

        $url = $this->getOption('info_url');
        $parameters['oauth_signature'] = $this->signRequest('GET', $url, $parameters, $this->getOption('client_secret'), $accessToken['oauth_token_secret']);

        $response = $this->doGetUserInformationRequest($url, $parameters)->getContent();

        return $this->parseUserInfo($response);
    }

    /**
     * {@inheritDoc}
     */
    public function getAuthorizationUrl($redirectUri, array $extraParameters = array())
    {
        $token = $this->getRequestToken($redirectUri, $extraParameters);

        return $this->getOption('authorization_url').'?'.http_build_query(array('oauth_token' => $token['oauth_token']));
    }

    /**
     * {@inheritDoc}
     */
    public function getAccessToken(Request $request, $redirectUri, array $extraParameters = array())
    {
        if (($requestToken = $this->session->get('oauth_token_'.$this->getName())) !== null) {
            $this->session->remove('oauth_token_'.$this->getName());
        }
        else {
            throw new \RuntimeException('No request token found in session');
        }

        $parameters = array_merge($extraParameters, array(
            'oauth_consumer_key'     => $this->getOption('client_id'),
            'oauth_timestamp'        => time(),
            'oauth_nonce'            => $this->generateNonce(),
            'oauth_version'          => '1.0',
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_token'            => $requestToken['oauth_token'],
            'oauth_verifier'         => $request->query->get('oauth_verifier'),
        ));

        $url = $this->getOption('access_token_url');
        $parameters['oauth_signature'] = $this->signRequest('POST', $url, $parameters, $this->getOption('client_secret'), $requestToken['oauth_token_secret']);

        $response = $this->doGetAccessTokenRequest($url, $parameters);
        $response = $this->getResponseContent($response);

        if (isset($response['oauth_problem'])) {
            throw new AuthenticationException(sprintf('OAuth error: "%s"', $response['oauth_problem']));
        }

        if (!isset($response['oauth_token']) || !isset($response['oauth_token_secret'])) {
            throw new AuthenticationException('Not a valid request token.');
        }

        return $response;
    }

    /**
     * {@inheritDoc}
     */
    public function handles(Request $request)
    {
        return $request->query->has('oauth_token');
    }

    /**
     * {@inheritDoc}
     */
    protected function getRequestToken($redirectUri, array $extraParameters = array())
    {
        $timestamp = time();

        $parameters = array_merge($extraParameters, array(
            'oauth_consumer_key'     => $this->getOption('client_id'),
            'oauth_timestamp'        => $timestamp,
            'oauth_nonce'            => $this->generateNonce(),
            'oauth_version'          => '1.0',
            'oauth_callback'         => $redirectUri,
            'oauth_signature_method' => 'HMAC-SHA1',
        ));

        $url = $this->getOption('request_token_url');
        $parameters['oauth_signature'] = $this->signRequest('POST', $url, $parameters, $this->getOption('client_secret'));

        $apiResponse = $this->httpRequest($url, null, $parameters, array(), 'POST');

        $response = $this->getResponseContent($apiResponse);

        if (isset($response['oauth_problem']) || (isset($response['oauth_callback_confirmed']) && ($response['oauth_callback_confirmed'] != 'true'))) {
            throw new AuthenticationException(sprintf('OAuth error: "%s"', $response['oauth_problem']));
        }

        if (!isset($response['oauth_token']) || !isset($response['oauth_token_secret'])) {
            throw new AuthenticationException('Not a valid request token.');
        }

        $response['timestamp'] = $timestamp;

        $this->session->set('oauth_token_'.$this->getName(), $response);

        return $response;
    }

    private function signRequest($method, $url, $parameters, $clientSecret, $tokenSecret = '')
    {
        // Validate required parameters
        foreach (array('oauth_consumer_key', 'oauth_timestamp', 'oauth_nonce', 'oauth_version', 'oauth_signature_method') as $parameter) {
            if (!isset($parameters[$parameter])) {
                throw new \RuntimeException(sprintf('Parameter "%s" must be set.', $parameter));
            }
        }

        // Remove oauth_signature if present
        // Ref: Spec: 9.1.1 ("The oauth_signature parameter MUST be excluded.")
        if (isset($parameters['oauth_signature'])) {
            unset($parameters['oauth_signature']);
        }

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
            rawurlencode($clientSecret),
            rawurlencode($tokenSecret),
        );

        $key = implode('&', $keyParts);

        return base64_encode(hash_hmac('sha1', $baseString, $key, true));
    }


    /**
     * Generate a non-guessable nonce value.
     *
     * @return string
     */
    protected function generateNonce()
    {
        return md5(microtime() . mt_rand());
    }

    /**
     * {@inheritDoc}
     */
    protected function httpRequest($url, $content = null, $parameters = array(), $headers = array(), $method = null)
    {
        $authorization = 'Authorization: OAuth';
        if (null !== $this->getOption('realm')) {
            $authorization = 'Authorization: OAuth realm="' . rawurlencode($this->getOption('realm')) . '"';
        }

        foreach ($parameters as $key => $value) {
            $value = rawurlencode($value);
            $authorization .= ", $key=\"$value\"";
        }

        $headers[] = $authorization;

        return parent::httpRequest($url, $content, $headers, $method);
    }

    /**
     * {@inheritDoc}
     */
    protected function doGetAccessTokenRequest($url, array $parameters = array())
    {
        return $this->httpRequest($url, null, $parameters, array(), 'POST');
    }

    /**
     * {@inheritDoc}
     */
    protected function doGetUserInformationRequest($url, array $parameters = array())
    {
        return $this->httpRequest($url, null, $parameters, array(), 'GET');
    }
}