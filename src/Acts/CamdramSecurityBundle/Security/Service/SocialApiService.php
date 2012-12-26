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

use Acts\SocialApiBundle\Service\RestApi;

class SocialApiService extends AbstractService
{

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
        return $this->api->authenticateWithRequest($request, $redirectUri);
    }

    /**
     * {@inheritDoc}
     */
    public function handles(Request $request)
    {
        return $this->api->canAuthenticateRequest($request);
    }

}