<?php

/*
 * This file is part of the HWIOAuthBundle package.
 *
 * (c) Hardware.Info <opensource@hardware.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Acts\ExternalLoginBundle\Security\Service;

use Acts\SocialApiBundle\Exception\OAuthException;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

use Buzz\Client\ClientInterface as HttpClientInterface;

use Symfony\Component\Security\Core\Exception\AuthenticationException,
    Symfony\Component\Security\Http\HttpUtils,
    Symfony\Component\HttpFoundation\Request;

use Acts\SocialApiBundle\Service\OAuthApi;

class SocialApiService extends AbstractService
{
    /**
     * @var OAuthApi
     */
    protected $api;

    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritDoc}
     */
    public function getUserInfo($accessToken = null)
    {
        return $this->api->doCurrentUser();
    }

    /**
     * {@inheritDoc}
     */
    public function getAuthorizationUrl($redirectUri, array $extraParameters = array())
    {
        return $this->api->getLoginUrl($redirectUri);
    }

    /**
     * {@inheritDoc}
     */
    public function getAccessToken(Request $request, $redirectUri, array $extraParameters = array())
    {
        try {
            $this->api->authenticateWithRequest($request, $redirectUri);
            return $this->api->getToken();
        }
        catch (OAuthException $e) {
            throw new AuthenticationException();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function handles(Request $request)
    {
        return $this->api->canAuthenticateRequest($request);
    }

}